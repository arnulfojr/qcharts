/**
 * @param tableId
 * @param baseApiUrl
 * @param service
 * @param queryId
 * @param options
 * @constructor
 */
var SnapshotTableController = function(tableId, baseApiUrl, service, queryId, options) {
    UrlFetcher.call(this, baseApiUrl, service, options["modalId"], options["loadingIconId"]);
    this.tableId = tableId;
    this.urls = undefined;
    this.table = undefined;
    this.resultFetcher = new ResultsFetcher(service, queryId, "table", "");
    this.results = [];
    this.snapshot = undefined;
};

SnapshotTableController.prototype = Object.create(UrlFetcher.prototype);
SnapshotTableController.prototype.constructor = SnapshotTableController;

/**
 * @param snapshot
 */
SnapshotTableController.prototype.setSnapshot = function(snapshot) {
    this.resultFetcher.setSnapshot(snapshot);
    this.snapshot = snapshot;
};

/**
 * @returns {undefined|*}
 */
SnapshotTableController.prototype.getSnapshot = function() {
    return this.snapshot;
};

SnapshotTableController.prototype.init = function() {

    var store = this;

    this.fetchUrls(function(urls) {
        store.urls = urls;
        //console.log(urls);
        store.resultFetcher.url = urls["chart"];
        store.fetchResults();
    });
};

SnapshotTableController.prototype.setQueryDuration = function(value) {
    $("#queryDuration").html();
};

/**
 * fetches the results again
 */
SnapshotTableController.prototype.fetchResults = function() {
    var store = this;
    this.resultFetcher.getResults(function(response) {
        var content = response["chartData"];
        console.log(response);
        store.results = content;
        $("#queryDuration").html(response["duration"]);
        store.renderTable(content, response["snapshot"]);
    });
};

/**
 * @param content
 * @param snapshotMessage
 */
SnapshotTableController.prototype.renderTable = function(content, snapshotMessage) {
    console.log(content);
    var keys = Object.keys(content[0]);
    var columns = [];

    for (var i = 0; i < keys.length; i++) {
        columns.push({
            "data": keys[i],
            "title": keys[i]
        });
    }

    var query = "#"+this.tableId;
    console.log(this);

    if (this.table != undefined) {
        this.table.destroy();
        $(query).empty();
    }

    var date = new Date();

    this.table = $(query).DataTable({
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'print',
                message: "Data source: " + snapshotMessage + ", on " + moment().format('MMM Do YYYY, h:mm:ss a'),
                text: '<span class="glyphicon glyphicon-print"></span>',
                className: 'btn btn-print',
                title: document.title
            }
        ],
        data: content,
        columns: columns,
        fixedHeader: true
    });

};