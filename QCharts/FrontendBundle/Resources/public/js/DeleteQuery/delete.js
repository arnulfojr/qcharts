/**
 * @param queryId
 * @param buttonId
 * @param service
 * @param baseApiUrl
 * @param options
 * @constructor
 */
var DeleteController = function(queryId, buttonId, service, baseApiUrl, options) {
    UrlFetcher.call(this, baseApiUrl, service, options["modal"]["id"], options["loading"]["id"]);
    this.queryId = queryId;
    this.buttonId = buttonId;
    this.urls = undefined;
};

DeleteController.prototype = Object.create(UrlFetcher.prototype);
DeleteController.prototype.constructor = UrlFetcher;

DeleteController.prototype.initialize = function() {
    var store = this;
    this.fetchUrls(function(urls) {
        store.urls = urls;
        store.bindButton();
    });
};

DeleteController.prototype.bindButton = function() {
    var button = document.getElementById(this.buttonId);
    var store = this;

    button.addEventListener('click', function() {
        if (confirm("Are you sure you want to delete this entry?")) {
            store.delete();
        }
    });

};

DeleteController.prototype.delete = function() {
    var store = this;
    var data = {
        "id": store.queryId
    };
    var info = this.getObject('DELETE', this.urls["query"], data);
    this.service(info)
        .then(function(response) {
            if (response["status"] == 202) {
                //the query was deleted!
                window.location = store.urls["frontend"]["homepage"];
                return;
            }
            store.modalController.popConnectionError();
        }, function(response) {
            store.modalController.popConnectionError();
        });
};