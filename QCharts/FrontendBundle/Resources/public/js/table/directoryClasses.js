/**
 * @param baseApiUrl
 * @param service
 * @param modalId
 * @param loadingIconId
 * @constructor
 */
var DirectoryFetcher = function (baseApiUrl, service, modalId, loadingIconId) {
    UrlFetcher.call(this, baseApiUrl, service, modalId, loadingIconId);
    this.urls = {};
};

DirectoryFetcher.prototype = Object.create(UrlFetcher.prototype);
DirectoryFetcher.prototype.constructor = DirectoryFetcher;

/**
 * @param dirId
 * @param callback
 * @param dirCallback
 * @param contentCallback
 */
DirectoryFetcher.prototype.init = function(dirId, callback, dirCallback, contentCallback) {
    var store = this;
    this.fetchUrls(function(urls) {
        store.urls = urls;
        store.fetchContentFromDirectory(dirId, dirCallback);
        store.fetchQueriesInDirectory(dirId, contentCallback);
        if (callback != undefined) {
            callback();
        }
    });

};

/**
 * @param directoryId
 * @param callback
 */
DirectoryFetcher.prototype.fetchContentFromDirectory = function(directoryId, callback) {
    var content = {
        currentDirectory: directoryId
    };

    var data = this.getObject('get', this.urls["directory"]["base"], content);
    //console.log(data);
    var store = this;

    this.loadingIcon.setLoading(true);

    this.service(data).then(function(response) {
        //ok

        if (callback != undefined) {
            callback(response["directories"]);
            store.loadingIcon.setLoading(false);
            return;
        }

        store.loadingIcon.setLoading(false);
        store.modalController.popConnectionError("While fetching the directories");
    }, function(response) {
        //handle error
        console.log(response);
        store.modalController.popConnectionError(response["textStatus"]);
        store.loadingIcon.setLoading(false);
    });

};

/**
 * @param directoryId
 * @param callback
 */
DirectoryFetcher.prototype.fetchQueriesInDirectory = function(directoryId, callback)
{
    var content = {
        _format: "json",
        dir: directoryId
    };

    var data = this.getObject('get', this.urls["directory"]["query"], content);

    //console.log(data);
    var store = this;

    this.service(data).then(function(r) {
        store.loadingIcon.setLoading(false);
        if (callback != undefined) {
            callback(r);
        }
    }, function(r) {
        //error
        console.log("error while fetching the content");
        console.log(r["statusText"]);
        store.modalController.popConnectionError(r);
        store.loadingIcon.setLoading(false);
    });

};

/**
 * @param dirId
 * @param okCallback
 * @param errorCallback
 */
DirectoryFetcher.prototype.deleteDirectory = function(dirId, okCallback, errorCallback) {
    var content = {
        "id": dirId
    };

    var data = this.getObject('DELETE', this.urls["directory"]["base"], content);
    this.service(data).then(function(r) {
        if (okCallback != undefined) {
            okCallback(r);
        }
    }, function(r) {
        if (errorCallback != undefined) {
            errorCallback(r["textStatus"]);
        }
    });

};

/**
 * @param dirId
 * @param name
 * @param successCallback
 * @param errorCallback
 */
DirectoryFetcher.prototype.putDirectory = function(dirId, name, successCallback, errorCallback) {
    var content = {
        "edit[id]": dirId,
        "edit[name]": name
    };

    var data = this.getObject('PUT', this.urls["directory"]["base"], content);

    this.service(data).then(function(r) {
        //ok
        if (successCallback != undefined) {
            successCallback(r["textStatus"]);
        }
    }, function(r) {
        //error
        if (errorCallback != undefined) {
            errorCallback(r["textStatus"]);
        }
    });

};

/**
 * @returns {{}|*}
 */
DirectoryFetcher.prototype.getUrls = function() {
    return this.urls;
};

/**
 * @param containerId
 * @param pathContainer
 * @param formId
 * @param modalId
 * @param loadingIconId
 * @param service
 * @param baseApiUrl
 * @constructor
 */
var DirectoryController = function(containerId, pathContainer, formId, modalId, loadingIconId, service, baseApiUrl) {
    ObjectBuilder.call(this, formId);
    this.containerId = containerId;
    this.service = service;
    this.isEditable = false;
    this.onlyTimeMachineFiles = false;
    this.pathContainerId = pathContainer;
    this.configuration = {};
    this.dirInputId = "";
    //this.formToBindId = "";
    this.configuration["history"] = [{id:undefined, name:"."}];
    this.modalController = new ModalController(modalId);
    this.loadingIcon = new LoadingIcon(loadingIconId);
    this.baseApiUrl = baseApiUrl;
    this.directoryFetcher = new DirectoryFetcher(baseApiUrl, service, modalId, loadingIconId);
    this.currentDir = undefined
    ;
    this.formController = new FormManager(formId, "", "POST", service, '', undefined);
    this.favoriteController = undefined;
    this.cellCallback = undefined;
};

DirectoryController.prototype = Object.create(ObjectBuilder.prototype);
DirectoryController.prototype.constructor = DirectoryController;

// TODO: add a button to refresh the list of snapshots!
DirectoryController.prototype.init = function() {
    var store = this;
    var content = {};
    this.isInit = true;
    this.directoryFetcher.init(this.currentDir, function() {
        store.configuration["urls"] = store.directoryFetcher.getUrls();
        var formId = store.formController.getFormId();
        if (formId != undefined && formId != "") {
            store.bindForm();
        }
    }, function(directories) {
        content = directories;
        store.setUp(content);
    }, function(queries) {
        store.addObjects(queries);
    });
};

/**
 * @param editable
 */
DirectoryController.prototype.setEditable = function(editable) {
    this.isEditable = editable;
};

/**
 * @param status
 */
DirectoryController.prototype.setOnlyTimeMachine = function(status) {
    this.onlyTimeMachineFiles = status;
};

/**
 * @param controller
 */
DirectoryController.prototype.setFavoriteController = function(controller) {
    this.favoriteController = controller;
    this.favoriteController.setDirectoryController(this);
};

DirectoryController.prototype.bindForm = function() {
    //bind the parent input with the current dir id
    var store = this;
    this.formController.setUrl(this.configuration["urls"]["directory"]["base"]);
    this.formController.setHTTPMethod("POST");

    this.formController.bindFormWithService(function(request) {
        request.then(function(r) {
            //ok
            console.log(r);
            if (r.status == 201 || r.status == 200) {
                //OK
                store.updateView(store.currentDir);
                store.formController.clearForm();
                return;
            }

            //error, most probably the name already exists!
            store.modalController.init();
            store.modalController.setSmallModal();
            var content = {
                title: "Oups!",
                body: r["textStatus"],
                footer: ""
            };
            store.modalController.setModal(content);
            store.modalController.pop();

        }, function(r) {
            //on error
            console.log(r);
            store.modalController.popConnectionError("");
        });
    });

};

/**
 * @param animate
 */
DirectoryController.prototype.renderView = function(animate) {
    this.updateView(this.currentDir, animate);
};

/**
 * @param directoryId
 * @param animate
 */
DirectoryController.prototype.updateView = function(directoryId, animate) {
    var store = this;
    var container = document.getElementById(this.containerId);
    var bcContainer = document.getElementById(this.pathContainerId);

    this.clearView(container, bcContainer, function() {
        //update the form for registering new folders
        store.updateRegisterForm(directoryId);
        store.directoryFetcher.fetchContentFromDirectory(directoryId, function(response) {
            store.setUp(response, animate);
        });
        store.directoryFetcher.fetchQueriesInDirectory(directoryId, function(r) {
            store.addObjects(r, store.cellCallback);
        });
    });

};

/**
 * @param directoryId
 */
DirectoryController.prototype.updateRegisterForm = function(directoryId) {
    if (this.formId != undefined && this.formId != "") {
        var query = "#" + this.formId + " input[name='directory[parent]']";
        var input = $(query);
        input.val(directoryId);
    }
};

/**
 * @param content
 * @returns {Array}
 */
DirectoryController.prototype.filterObjects = function (content) {
    //now only the tm filter
    var results = [];

    for (var i in content) {
        if (content.hasOwnProperty(i)) {
            if (content[i]["config"]["isCached"] == 2) {
                results.push(content[i]);
            }
        }
    }

    return results;
};

/**
 * @param content
 * @param cellCallback
 */
DirectoryController.prototype.addObjects = function(content, cellCallback) {
    //console.log(content);
    var store = this;

    if (this.onlyTimeMachineFiles) {
        content["queries"] = this.filterObjects(content["queries"]);
    }

    var list = this.createList(content["queries"],
        function(elem) {
            //set content
            var icon = document.createElement("span");
            var link = document.createElement("a");

            var redirectUrl = window.location.origin;
            redirectUrl = redirectUrl + store.configuration["urls"]["frontend"]["base"] + "/" + elem["id"];

            var paragraph = document.createElement("p");

            //link.setAttribute("href", redirectUrl);
            icon.setAttribute("class", "glyphicon glyphicon-stats");
            paragraph.setAttribute("class", "cell-main-text");
            paragraph.innerHTML = " " + elem["title"] + " - " + "<small>created by: " + elem["createdBy"]["name"] + "</small>";
            link.appendChild(icon);
            link.appendChild(paragraph);
            var row = store.getRow();

            var left = store.createColumn("col-xs-10");

            left.addEventListener("click", function() {
                if (cellCallback === undefined) {
                    window.location = redirectUrl;
                } else {
                    cellCallback(elem);
                }
            });

            var buttons = [{
                "class": "btn btn-sm btn-love",
                "icon": "glyphicon glyphicon-heart",
                "query-id": elem["id"],
                "action": function() {
                    store.favoriteController.addFavorite(this.getAttribute("query-id"), function() {
                        // callback when success!
                        console.log(store.currentDir);
                        store.favoriteController.initialize();
                        store.renderView(false);
                    });
                }
            }];

            var buttonGroup = store.createButtonGroup(buttons.length, function(btn, qty, i) {
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

            var right = store.createColumn("col-xs-2");

            if (store.favoriteController != undefined && store.favoriteController.isFavorite(elem["id"])) {
                //change the content from the right section!
                var heart = store.createBeatingHeart(elem["id"], function() {
                    //clicked on the heart!
                    //console.log("clicked");
                    var queryId = this.getAttribute("query-id");
                    store.favoriteController.removeFavorite(queryId, function() {
                        //update view
                        delete store.favoriteController.favorites[queryId];
                        store.renderView(false);
                        store.favoriteController.renderFavorites()
                    });
                });
                right.appendChild(heart);
                right.setAttribute("class", "col-xs-2");
            }else {
                right.appendChild(buttonGroup);
                right.setAttribute("class", "col-xs-2 action-group");
            }
            left.appendChild(link);

            row.appendChild(left);
            row.appendChild(right);

            return row;
        },
        function(li) {
            li.setAttribute("class", "list-group-item");
            return li;
        });
    var container = document.getElementById(this.containerId);
    container.appendChild(list);
};

/**
 * @param callback
 */
DirectoryController.prototype.setCellCallback = function(callback) {
    this.cellCallback = callback;
};

/**
 * @param response
 * @param animate
 */
DirectoryController.prototype.setUp = function(response, animate) {

    var content = response["directories"];

    //set up the breadcrumbs
    //console.log("current folder on setup:" + this.currentDir);

    //console.log(response);
    var store = this;

    this.configuration["cache"] = content;

    if (this.isInit) {
        //then load it from the cache/content
        this.updateRegisterForm(this.currentDir);
        var path = [];
        var current = response["root"];
        while (current != undefined) {
            path.unshift({
                "name": current["name"],
                "id": current["id"]
            });
            current = current["parent"];
        }
        path.unshift({id:undefined, name:"."});
        this.configuration["history"] = path;
        //console.log(this.configuration);
        this.isInit = false;
    }

    var breadCrumbs = this.createBreadCrumbs(this.configuration["history"], function(elem) {
        var paragraph = document.createElement('a');
        paragraph.innerHTML = elem["name"];
        return paragraph;
    }, function(li, elem) {
        var content = {
            name: elem["name"],
            id: elem["id"]
        };

        li.addEventListener('click', function(e) {
            console.log("clicked path");
            store.breadClick(e, content);
        });

        return li;
    });

    var container = document.getElementById(this.containerId);
    var bcContainer = document.getElementById(this.pathContainerId);

    // TODO: present an empty folder view!
    var list = this.createList(content, function(elem) {
        //set content
        var icon = document.createElement('span');
        icon.setAttribute("class", "glyphicon glyphicon-folder-open");
        var paragraph = document.createElement('p');
        paragraph.innerHTML = elem["name"];

        var btns = [{
            "class": "editable btn btn-sm btn-default-arny",
            "icon": "glyphicon glyphicon-pencil",
            "directory-id": elem["id"],
            "action": function() {
                store.editFolder(this.getAttribute('directory-id'), this);
            }
        }, {
            "class": "deletable btn btn-sm btn-delete",
            "icon":"glyphicon glyphicon-trash",
            "directory-id": elem["id"],
            "action": function() {
                store.deleteFolder(this.getAttribute('directory-id'), this);
            }
        }
        ];

        var btnGroup = store.createButtonGroup(btns.length,
            function(btn, qty, i) {
                //attributes
                var config = btns[i];
                btn.setAttribute("class", config["class"]);
                btn.setAttribute("directory-id", config["directory-id"]);
                return btn;
            },
            function(btn, qty, i) {
                //content
                var span = document.createElement("span");
                span.setAttribute("class", btns[i]["icon"]);

                btn.addEventListener("click", btns[i]["action"]);

                return span;
            });

        var row = store.getRow();
        var mainContent = store.createColumn("");

        mainContent.setAttribute("directory", elem["id"]);
        mainContent.setAttribute("directory-name", elem["name"]);

        mainContent.addEventListener('click', function(e) {
            var element = this;
            var content = {
                id: element.getAttribute("directory"),
                name: element.getAttribute("directory-name")
            };
            store.cellClick(e, content);
        });

        if (store.isEditable) {
            mainContent.setAttribute("class", "col-xs-6 col-sm-8");
            var right = store.createColumn("col-xs-6 col-sm-4 action-group");
            right.appendChild(btnGroup);
            mainContent.appendChild(icon);
            mainContent.appendChild(paragraph);
            row.appendChild(mainContent);
            row.appendChild(right);

        }else{
            mainContent.setAttribute("class", "col-sm-12");
            mainContent.appendChild(icon);
            mainContent.appendChild(paragraph);
            row.appendChild(mainContent);
        }

        return row;

    }, function(li, elem) {
        //set attributes
        li.setAttribute("directory", elem["id"]);
        li.setAttribute("directory-name", elem["name"]);
        li.setAttribute("class", "list-group-item");
        return li;
    });

    if (this.backButton == undefined) {
        this.backButton = this.getBackButton();
        this.backButton.addEventListener('click', function() {
            store.goBack();
        });
    }

    if (this.configuration["history"].length >= 2) {
        this.backButton.removeAttribute("disabled");
    }else{
        this.backButton.setAttribute("disabled", "disabled");
    }
    bcContainer.appendChild(this.backButton);
    container.appendChild(list);
    bcContainer.appendChild(breadCrumbs);

    if (animate == undefined || animate === true) {
        this.animate();
    }
};

DirectoryController.prototype.animate = function() {
    var container = document.getElementById(this.containerId);
    var bcContainer = document.getElementById(this.pathContainerId);
    $(bcContainer).removeClass("animated fadeInDown");
    $(bcContainer).addClass("animated fadeInDown");
    $(container).addClass("animated fadeInRight");
};

/**
 * @param folderId
 * @returns {*}
 */
DirectoryController.prototype.getFolder = function(folderId) {
    for (var i = 0; i < this.configuration["cache"].length; i++) {
        if (this.configuration["cache"][i]["id"] == folderId) {
            return this.configuration["cache"][i];
        }
    }
    return undefined;
};

/**
 * @param folderId
 */
DirectoryController.prototype.editFolder = function(folderId) {
    var inputs = [
        {
            input: {
                "class": "form-control",
                "id": "edit_name",
                name: "edit[name]",
                type:"input",
                value: this.getFolder(folderId)["name"],
                btnType: "text"
            },
            label: {
                "class": "control-label col-sm-2",
                "text": "name"
            },
            inputContainer: {
                "class": "col-sm-10"
            },
            container: {
                "class": "form-group"
            }
        }, {
            input: {
                "class": "form-control",
                "id": "edit_id",
                name: "id",
                type:"input",
                value: folderId,
                btnType: "hidden"
            },
            label: {
                "class": "control-label col-sm-2",
                "text": ""
            },
            inputContainer: {
                "class": "col-sm-10"
            },
            container: {
                "class": "form-group"
            }
        }, {
            input: {
                "class": "btn btn-default-arny btn-sm",
                "id": "edit_save",
                name: "edit[save]",
                type: "button",
                value: "save",
                text: "Save",
                "btnType": "submit"
            },
            label: {
                "class": "",
                "text": ""
            },
            inputContainer: {
                "class": "col-sm-12"
            },
            container: {
                "class": "form-group"
            }
        }
    ];

    console.log(this.configuration);
    var store = this;
    var formFields = {
        action: store.configuration["urls"]["directory"]["base"],
        name: "edit",
        method: "PUT",
        id: "edit_form_dir",
        "class":"form-horizontal",
        "dirId": folderId
    };

    var form = this.createForm(formFields, inputs);
    //bind form
    this.showForm(form, formFields);
};

/**
 * @param folderId
 */
DirectoryController.prototype.deleteFolder = function(folderId) {
    var store = this;
    this.directoryFetcher.deleteDirectory(folderId,
        function(r) {
            if (r["status"] == 202) {
                store.updateView(r["parentId"]);
            }else{
                store.modalController.init();
                store.modalController.setSmallModal();
                store.modalController.setModal({
                    title: "Oups!",
                    body: r["textStatus"],
                    footer: ""
                });
                store.modalController.pop();
            }
        }, function(r) {
            console.log(r);
        });
};

/**
 * @param form
 * @param formFields
 */
DirectoryController.prototype.showForm = function(form, formFields) {
    var store = this;

    this.modalController.init();
    this.modalController.setSmallModal();
    this.modalController.setDOM({
        title: "Edit the folder",
        body: form,
        footer: ""
    });
    this.modalController.pop();
    var formManager = new FormManager(formFields["id"], formFields["action"], formFields["method"], store.service);
    formManager.queryId = formFields["dirId"];
    var callbacks = {
        onDone: function(r) {
            if (r["status"] == 200 || r["status"] == 202) {
                //ok
                store.modalController.hide();
                store.updateView(r["directoryId"]);
                return;
            }
            store.modalController.setModal({
                title:"Oups",
                body:r["textStatus"],
                footer: ""
            });
        },
        onFail: function() {
            store.modalController.popConnectionError();
        }
    };
    formManager.setCallBacks(callbacks);
    formManager.bindFormWithService();
};

DirectoryController.prototype.goBack = function() {
    if (this.configuration["history"].length == 1) {
        return;
    }
    this.configuration["history"].pop();
    var back2 = this.configuration["history"].pop();
    console.log(back2);
    this.configuration["history"].push(back2);
    this.currentDir = back2["id"];
    this.updateCurrentDirectoryInput(this.dirInputId);
    this.updateView(back2["id"]);
};

/**
 * @returns {string}
 */
DirectoryController.prototype.getPathAsString = function() {
    var path = "";

    var store = this;

    for (var i = 0; i < this.configuration["history"].length; i++) {
        path += ("" + store.configuration["history"][i]["name"] + "/");
    }

    return path;

};

/**
 * @param e
 * @param content
 */
DirectoryController.prototype.breadClick = function(e, content) {
    console.log(content);
    this.currentDir = content["id"];
    this.updateCurrentDirectoryInput(this.dirInputId);

    for (var i = this.configuration["history"].length - 1; i >= 0; i--) {
        if (this.configuration["history"][i]["id"] == content["id"]) {
            break;
        }
        this.configuration["history"].pop();
    }

    this.updateView(content["id"]);
};


/**
 * @param inputId
 */
DirectoryController.prototype.updateCurrentDirectoryInput = function(inputId) {
    var input = document.getElementById(inputId);
    if (input != undefined) {
        input.value = this.currentDir;
    }
};

DirectoryController.prototype.setInputFromForm = function(formId) {
    console.log(formId);
    var form = document.getElementById(formId);
    if (form != undefined) {
        var name = form.getAttribute("name");
        var inputId = name + "_directory";
        console.log(inputId);
        this.dirInputId = inputId;
        //update the view
        var input = document.getElementById(this.dirInputId);
        if (input.value > 0) {
            this.currentDir = input.value;
        }
    }else{
        this.dirInputId = "";
    }
};

/**
 * @param e
 * @param content
 */
DirectoryController.prototype.cellClick = function (e, content) {
    this.currentDir = content["id"];
    this.updateCurrentDirectoryInput(this.dirInputId);
    this.configuration["history"].push(content);
    this.updateView(content["id"]);

};

/**
 * @param container
 * @param breadcrumbsContainer
 * @param callback
 */
DirectoryController.prototype.clearView = function(container, breadcrumbsContainer, callback) {

    var children = container.childNodes;

    for (var i = 0; i < children.length; i++) {
        //remove the event Listener
        var currentLi = children[i];
        currentLi.removeEventListener('click', this.cellClick);
    }

    breadcrumbsContainer.innerHTML = "";
    $(container).removeClass("animated fadeInRight");

    container.innerHTML = "";
    if (callback != undefined) {
        callback();
    }

};