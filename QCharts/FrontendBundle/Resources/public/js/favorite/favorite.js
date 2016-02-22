/**
 * @param baseApiUrl
 * @param service
 * @param modalId
 * @param loadingIconId
 * @constructor
 */
var FavoriteCommunicator = function (baseApiUrl, service, modalId, loadingIconId) {
    UrlFetcher.call(this, baseApiUrl, service, modalId, loadingIconId);
    this.urls = undefined;
};

FavoriteCommunicator.prototype = Object.create(UrlFetcher.prototype);
FavoriteCommunicator.prototype.constructor = FavoriteCommunicator;

/**
 * @param callback
 */
FavoriteCommunicator.prototype.init = function(callback) {
    var store = this;
    this.fetchUrls(function(urls) {
        store.urls = urls;
        //ok, ask for the favorites!
        if (callback != undefined) {
            callback();
        }
    });
};

FavoriteCommunicator.prototype.fetchFavorites = function(callback) {
    var data = this.getObject('get', this.urls["favorite"]["base"], {});
    var store = this;

    this.service(data)
        .then(function(response) {
            if (response["status"] == 200) {
                if (callback != undefined) {
                    callback(response["favorites"]);
                    return;
                }
                store.modalController.popConnectionError();
            }
        }, function() {
            //error in connection
            store.modalController.popConnectionError();
        });

};

/**
 * @param q
 * @param callback
 */
FavoriteCommunicator.prototype.removeFavorite = function(q, callback) {
    var data = {
        q: q
    };
    var info = this.getObject("DELETE", this.urls["favorite"]["base"], data);
    var store = this;
    console.log(info);
    this.service(info)
        .then(function(response) {
            if (response["status"] == 202) {
                if (callback != undefined) {
                    callback();
                }
                return;
            }
            store.modalController.popConnectionError();
        }, function() {
            //error
            store.modalController.popConnectionError();
        });

};

/**
 * @param q
 * @param callback
 */
FavoriteCommunicator.prototype.addFavorite = function(q, callback) {
    var data = {
        q: q
    };
    var info = this.getObject("POST", this.urls["favorite"]["base"], data);
    var store = this;

    this.service(info)
        .then(function(response) {
            if (response["status"] == 201) {
                if (callback != undefined) {
                    callback();
                }
                return;
            }

            store.modalController.popConnectionError();

        }, function() {
            store.modalController.popConnectionError();
        });

};

/**
 * @param containerId
 * @param service
 * @param baseApiUrl
 * @param config
 * @constructor
 */
var FavoriteController = function (containerId, service, baseApiUrl, config) {
    FavoriteCommunicator.call(this, baseApiUrl, service, config["modalId"], config["loadingIconId"]);
    this.favorites = [];
    this.containerId = containerId;
    this.directoryController = undefined;
};

FavoriteController.prototype = Object.create(FavoriteCommunicator.prototype);
FavoriteController.prototype.constructor = FavoriteController;

FavoriteController.prototype.initialize = function() {
    var store = this;
    console.log(this);
    if (this.urls == undefined) {
        console.log("not cahced urls");
        this.init(function() {
            store.fetchFavorites(function(favorites) {
                store.favorites = store.optimizeFavorites(favorites);
                store.renderFavorites();
            });
        });
    }else {
        console.log("cahced urls");
        this.fetchFavorites(function(favs) {
            store.favorites = store.optimizeFavorites(favs);
            store.renderFavorites();
        });
    }
};

/**
 * @param controller
 */
FavoriteController.prototype.setDirectoryController = function(controller) {
    this.directoryController = controller;
};

FavoriteController.prototype.renderFavorites = function() {
    var store = this;
    console.log(this.favorites);
    var container = document.getElementById(this.containerId);
    container.innerHTML = "";
    var list = this.createList(this.favorites, function(elem) {
        //Set content
        var icon = document.createElement('span');
        var link = document.createElement('a');
        var paragraph = document.createElement('p');

        icon.setAttribute("class", "glyphicon glyphicon-stats");

        var redirectURl = window.location.origin;
        redirectURl = redirectURl + store.urls["frontend"]["base"] + "/" + elem["id"];

        link.setAttribute("href", redirectURl);
        paragraph.setAttribute("class", "cell-main-text");
        paragraph.innerHTML = " " + elem["title"];
        link.appendChild(icon);
        link.appendChild(paragraph);

        var buttons = [
            {
                "class": "deletable btn btn-delete btn-sm",
                "icon": "glyphicon glyphicon-remove",
                "query-id": elem["id"],
                "action": function() {
                    store.removeFavorite(this.getAttribute('query-id'), function() {
                        store.initialize();
                        store.directoryController.renderView(false);
                    });
                }
            }
        ];

        var btnGroup = store.createButtonGroup(buttons.length, function(btn, qty, i) {

            var config = buttons[i];
            btn.setAttribute("class", config["class"]);
            btn.setAttribute("query-id", config["query-id"]);

            return btn;
        }, function(btn, qty, i) {

            var span = document.createElement("span");
            span.setAttribute("class", buttons[i]["icon"]);
            btn.addEventListener("click", buttons[i]["action"]);

            return span;

        });

        var row = store.getRow();
        var mainContent = store.createColumn("");
        var optionsColumn = store.createColumn("");

        mainContent.addEventListener("click", function() {
            window.location = redirectURl;
        });

        mainContent.setAttribute("class", "col-xs-10");
        optionsColumn.setAttribute("class", "col-xs-2 action-group");

        optionsColumn.appendChild(btnGroup);
        mainContent.appendChild(link);
        row.appendChild(mainContent);
        row.appendChild(optionsColumn);

        return row;

    }, function(li) {
        li.setAttribute("class", "list-group-item");
        return li;
    });

    if (Object.keys(this.favorites).length == 0) {
        console.log("empty");
        list = this.getEmptyList("We would show your favorites here but you have none!");
    }

    container.appendChild(list);

};

/**
 * @param list
 * @returns {{}}
 */
FavoriteController.prototype.optimizeFavorites = function(list) {
    var toReturn = {};

    for (var i = 0; i < list.length; i++) {
        var temp = list[i];
        toReturn[temp["id"]] = temp;
    }
    return toReturn;
};

/**
 * @param queryId
 * @returns {boolean}
 */
FavoriteController.prototype.isFavorite = function(queryId) {
    return (this.favorites[queryId] != undefined);
};