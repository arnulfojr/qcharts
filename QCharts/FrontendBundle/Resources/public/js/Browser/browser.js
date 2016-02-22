
//This class is responsable of calling everything to the server
/**
 * @param baseApiUrl
 * @param service
 * @param options
 * @constructor
 */
var Communicator = function(baseApiUrl, service, options) {
    ObjectBuilder.call(this);
    this.baseApiUrl = baseApiUrl;
    this.service = service;
    this.modalController = new ModalController(options["modal"]["id"]);
    this.loadingController = new LoadingIcon(options["loading"]["id"]);
    this.urls = undefined;
    this.options = options;
    this.responseCodes = undefined;
};

Communicator.prototype = Object.create(ObjectBuilder.prototype);
Communicator.prototype.constructor = Communicator;

Communicator.prototype.setUp = function() {
    //set up the ajax!
    var store = this;

    $(document).on({
        ajaxStart: function() {
            store.loadingController.setLoading(true)
        },
        ajaxStop: function() {
            store.loadingController.setLoading(false);
        }
    });

    this.responseCodes = {
        "ok": [200, 201, 202],
        "error": [402, 404, 500, 555]
    };

};

/**
 * @param successCallback
 * @param errorCallback
 */
Communicator.prototype.init = function(successCallback, errorCallback) {
    this.setUp();
    //just load the urls
    var store = this;
    var info = {};
    var data = this.getObject('get', this.baseApiUrl, info);

    //call the urls
    this.service(data)
        .then(function(response) {
            if (response["status"] == 200) {
                store.urls = response["urls"];
                if (successCallback != undefined) {
                    successCallback(response["urls"]);
                }
                return;
            }
            if (errorCallback != undefined) {
                errorCallback(response);
            }
        }, function(response) {
            store.modalController.popConnectionError();
            errorCallback(response);
        });
};

/**
 * @returns {undefined|{}}
 */
Communicator.prototype.getUrls = function() {
    return this.urls;
};

/**
 * @param {*} data
 * @param method
 * @param url
 * @param {*} options
 *
 * options parameter can hold successCallback, errorCallback and dataKey
 *
 */
Communicator.prototype.doAjax = function (method, data, url, options) {
    if (this.getUrls() == undefined) {
        throw "Urls are not fetched, initialize properly the Fetcher";
    }

    var store = this;
    var info = this.getObject(method, url, data);

    this.service(info)
        .then(function(response) {
            console.log(response);
            if (store.responseCodes["ok"].indexOf(response["status"]) >= 0) {
                //ok, success
                if (options != undefined && ('successCallback' in options)) {
                    var dataKey = (options != undefined && ('dataKey' in options)) ?
                        options['dataKey'] : 'data';
                    options['successCallback'](response[dataKey]);
                }
                //nothing to do))
                return;
            }

            if (options != undefined && ("errorCallback" in options)) {
                //error!
                options["errorCallback"](response);
            }

        }, function(response) {
            //error in connection
            store.modalController.popConnectionError();
            if (options != undefined && ("errorCallback" in options)) {
                options["errorCallback"](response);
            }
        });
};

/**
 * @param baseApiUrl
 * @param service
 * @param options
 * @constructor
 */
var FileFetcher = function (baseApiUrl, service, options) {
    Communicator.call(this, baseApiUrl, service, options);
    this.folders = undefined;
    this.files = undefined;
};

FileFetcher.prototype = Object.create(Communicator.prototype);
FileFetcher.prototype.constructor = FileFetcher;

FileFetcher.prototype.init = function() {
    //call parent init
    var store = this;
    Communicator.prototype.init.call(this, function() {

        store.fetchContents(undefined);

    });
};

/**
 * @returns {{files: *, folders: *}}
 */
FileFetcher.prototype.getContent = function() {
    return {
        files: this.files,
        folders: this.folders
    };
};

/**
 * @param rootId
 * @param {*} callbacks
 */
FileFetcher.prototype.fetchDirectories = function(rootId, callbacks) {

    if (this.urls == undefined) {
        throw "Urls are not yet loaded";
    }
    var urls = this.getUrls();

    var data = {
        currentDirectory: (rootId == undefined) ? 0 : rootId
    };
    callbacks["dataKey"] = "directories";
    this.doAjax('get', data, urls["directory"]["base"], callbacks);
};

/**
 * @param rootId
 * @param callbacks
 */
FileFetcher.prototype.fetchFiles = function(rootId, callbacks) {

    if (this.urls == undefined) {
        throw "Urls are not yet fetched";
    }

    var urls = this.getUrls();
    var data = {
        dir: (rootId == undefined) ? "" : rootId
    };

    callbacks["dataKey"] = "queries";
    this.doAjax('get', data, urls["directory"]["query"], callbacks);

};

/**
 * @param rootId
 * @param callbacks
 */
FileFetcher.prototype.fetchContents = function (rootId, callbacks) {
    var store = this;

    store.fetchFiles(rootId, {
        successCallback: function(f) {
            store.files = f;
            if (callbacks && ("files" in callbacks) && ("successCallback" in callbacks["files"])) {
                callbacks["files"]["successCallback"](f);
            }
        },
        errorCallback: function(r) {
            console.log(r);
            store.modalController.popConnectionError();
            if (callbacks && ("files" in callbacks) && ("errorCallback" in callbacks["files"])) {
                callbacks["files"]["errorCallback"](r);
            }
        }
    });

    store.fetchDirectories(rootId, {
        successCallback: function(d) {
            store.folders = d;
            if (callbacks && ("directories" in callbacks) && ("successCallback" in callbacks["directories"])) {
                callbacks["directories"]["successCallback"](f);
            }
        },
        errorCallback: function(r) {
            console.log(r);
            store.modalController.popConnectionError();
            if (callbacks && ("directories" in callbacks) && ("errorCallback" in callbacks["directories"])) {
                callbacks["directories"]["errorCallback"](r);
            }
        }
    });

};