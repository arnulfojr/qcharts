
//console controller to control all the other controllers
//needs a controller for the list and a controller for the calendar to display the snapshot
/**
 * @param baseApiUrl
 * @param service
 * @param options
 * @constructor
 */
var SnapshotConsoleController = function(baseApiUrl, service, options) {
    Communicator.call(this, baseApiUrl, service, options);

    /** @var DirectoryController this.dirController */
    this.dirController = undefined;
    /** @var CalendarController this.calendarController */
    this.calendarController = undefined;

    this.currentQueryId = undefined;
    this.options = options;
};

SnapshotConsoleController.prototype = Object.create(Communicator.prototype);
SnapshotConsoleController.prototype.constructor = SnapshotConsoleController;

SnapshotConsoleController.prototype.init = function() {

    var controller = this;

    Communicator.prototype.init.call(this, function() {
        //success callback
        controller.calendarController = new CalendarController(
            controller.options["calendar"]["id"],
            controller.baseApiUrl,
            controller.service,
            controller.options
        );

        controller.calendarController.setCustomEventClick(function(event) {
            controller.calendarEventClick(event);
        });
        controller.calendarController.init();

        controller.dirController = new DirectoryController(
            controller.options["directory"]["id"],
            controller.options["path"]["id"],
            '', // undefined form, there's no add form!
            controller.options["modal"]["id"],
            controller.options["loading"]["id"],
            controller.service,
            controller.baseApiUrl
        );
        controller.dirController.setEditable(true);
        controller.dirController.setOnlyTimeMachine(true);
        controller.dirController.setCellCallback(function(elem) {
            controller.directoryCellClick(elem);
        });
        controller.dirController.init();
    }, function() {
        //error callback
    });
};

/**
 * @param event
 */
SnapshotConsoleController.prototype.calendarEventClick = function(event) {
    //confirm and do delete of snapshot!
    if (confirm("Are you sure you want to delete this snapshot?")) {
        //ok
        this.deleteSnapshot(event["id"]);
    }
};

/**
 * @param elem
 */
SnapshotConsoleController.prototype.directoryCellClick = function(elem) {
    console.log(elem);
    this.calendarController.setQueryId(elem["id"]);
    this.calendarController.updateCalendar();
};

/**
 * @param snapshotId
 */
SnapshotConsoleController.prototype.deleteSnapshot = function(snapshotId) {
    var urls = this.getUrls();
    var controller = this;

    var data = {
        snapshot: snapshotId,
        q: controller.calendarController.getQueryId()
    };

    var callbacks = {
        successCallback: function(text) {
            //do something if successful
            //show the text and
            controller.modalController.init();
            controller.modalController.setSmallModal();
            var modalContent = {
                title: "Success",
                body: text
            };
            controller.modalController.setModal(modalContent);
            controller.modalController.pop();
        },
        errorCallback: function(response) {
            //do something if error, Communicator already handles the PopConnectionError
        },
        dataKey: "textStatus"
    };

    this.doAjax("DELETE", data, urls["snapshots"]["snapshots"], callbacks);
};