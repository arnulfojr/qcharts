/**
 * @param containerId
 * @param baseApiUrl
 * @param service
 * @param options
 * @constructor
 */
var CalendarController = function(containerId, baseApiUrl, service, options) {
    UrlFetcher.call(this, baseApiUrl, service, options["modal"]["id"], options["loading"]["id"]);
    this.containerId = containerId;
    this.urls = undefined;
    this.calendar = undefined;
    this.options = options;
    this.snapshots = undefined;
    /** @var ChartManager this.chartManager */
    this.chartManager = undefined;
    /** @var SnapshotTableController this.tableController */
    this.tableController = undefined;
    this.customCellClickEvent = undefined;
    this.downloadButtonController = new SnapshotFileDownload(
        options["download"]["button"]["id"],
        service,
        baseApiUrl,
        options["queryId"],
        options
    );
};

CalendarController.prototype = Object.create(UrlFetcher.prototype);
CalendarController.prototype.constructor = CalendarController;

CalendarController.prototype.init = function() {
    var store = this;
    $(document).ready(function() {
        store.fetchUrls(function(urls) {
            store.urls = urls;
            //ok, call the rest for setting up!
            store.downloadButtonController.urls = urls;
            store.downloadButtonController.setSnapshot();
            store.downloadButtonController.setUpButton();
            store.setUpCalendar();
            store.updateCalendar();
        });
    });
};

CalendarController.prototype.updateCalendar = function() {
    var store = this;
    this.calendar.fullCalendar("removeEvents");
    this.fetchSnapshots(function(rawSnapshots) {
        store.snapshots = store.processSnapshots(rawSnapshots);
        store.setEvents(store.snapshots);
    });
};

/**
 * @param queryId
 */
CalendarController.prototype.setQueryId = function(queryId) {
    this.options["queryId"] = queryId;
};

/**
 * @returns {*}
 */
CalendarController.prototype.getQueryId = function() {
    return this.options["queryId"];
};

/**
 * @param action
 */
CalendarController.prototype.setCustomEventClick = function(action) {
    this.customCellClickEvent = action;
};

/**
 * @param c
 */
CalendarController.prototype.setChart = function(c) {
    this.chartManager = c;
};

/**
 * @param snap
 */
CalendarController.prototype.updateChart = function(snap) {

    if (this.chartManager != undefined) {
        this.chartManager.setSnapshot(snap);
        this.chartManager.initializeChart();
    }

    if (this.tableController != undefined) {
        this.tableController.setSnapshot(snap);
        this.tableController.fetchResults();
    }

    if (this.downloadButtonController != undefined) {
        this.downloadButtonController.setSnapshot(snap);
    }
};

/**
 *
 * @param t
 */
CalendarController.prototype.setTable = function(t) {
    this.tableController = t;
};

/**
 * @param events
 */
CalendarController.prototype.setEvents = function(events) {
    this.calendar.fullCalendar({
        events: events,
        color: "yellow",
        textColor: "black"
    });
    console.log(events);
};

/**
 * @param rawSnapshots
 * @returns {Array}
 */
CalendarController.prototype.processSnapshots = function(rawSnapshots) {
    var formatted = [];
    var store = this;

    for (var key in rawSnapshots) {
        if (rawSnapshots.hasOwnProperty(key)) {
            var timestamp = parseInt(key * 1000);
            var dateTime = new Date(timestamp);
            var endDateTime = new Date(dateTime.getTime() + 12*60000);
            var temp = {
                title: "",
                id: key,
                start: dateTime.toISOString(),
                end: endDateTime.toISOString(),
                allDay: false,
                overlap: true,
                textColor: "black",
                backgroundColor: "#21dc9b",
                borderColor: "#21dc9b"
            };

            formatted.push(temp);
            store.calendar.fullCalendar('renderEvent', temp, true);
        }
    }

    return formatted;
};

CalendarController.prototype.getEvents = function() {
    return this.snapshots;
};

/**
 * @param callback
 */
CalendarController.prototype.fetchSnapshots = function(callback) {
    var store = this;

    var data = this.getObject('get', this.urls["snapshots"]["snapshots"], {
        q: store.options["queryId"]
    });

    this.service(data).then(function(response) {
        if (response["status"] == 200) {
            if (callback != undefined) {
                callback(response["snapshots"]);
            }
        }
    }, function() {
        //failed
        store.modalController.popConnectionError();
    });

};

CalendarController.prototype.setUpCalendar = function() {
    var store = this;
    var query = "#" + this.containerId;

    $(query).addClass("animated fadeIn");
    var options = {
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,basicWeek,agendaDay'
        },
        businessHours: {
            start: '08:00',
            end: '18:00',
            dow: [1,2, 3, 4, 5]
        },
        slotDuration: "00:20:00",
        timezone: "local",
        eventClick: function(event, e, view) {
            if (store.customCellClickEvent == undefined) {
                //snapshot id
                store.updateChart(event["id"]);
            }else{
                //custom click action!
                store.customCellClickEvent(event);
            }
        },
        eventMouseover: function(event, e, view) {
            $(this).css("cursor", "pointer");
            if (view.name == "agendaWeek" || view.name == "agendaDay") {
                $(this).css("width", "100%");
                $(this)
                    .animate({"height": "25px", "zIndex": 1000}, {duration: "fast"});
            }
        }, eventMouseout: function() {
            store.calendar.fullCalendar('rerenderEvents');
        }
    };
    this.calendar = $(query).fullCalendar(options);
    this.calendar.fullCalendar('addEventSource', store.getEvents());
};