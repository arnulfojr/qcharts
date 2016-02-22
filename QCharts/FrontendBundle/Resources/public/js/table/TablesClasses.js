/**
 * Created by arnulfosolis on 1/23/16.
 */


/*
* This is the concept of what this TableController has to do:
*   This controller has to control the table
*   Bind with the select input for changing the connection.
*   Bind a select to select the schema to use
*   and fetch the table's information of the selection schema under the
*   selected connection.
*   conserving the current functionality of the click and double click!
 */


/**
 * @param selectId
 * @constructor
 */
var SelectOptionsUpdater = function(selectId) {
    this.selectId = selectId;
    this.select = document.getElementById(this.selectId);
};

SelectOptionsUpdater.prototype.setSelectId = function(selectID) {
    this.selectId = selectID;
    this.select = document.getElementById(selectID);
};

SelectOptionsUpdater.prototype.updateSelect = function (data) {

    if (this.select == undefined) {
        this.select = document.getElementById(this.selectId);
    }

    this.select.options.length = 0;

    for (var a = 0; a < data.length; a++) {
        var current = data[a];
        var opt = this.createOption(current["value"], current["displayValue"]);
        this.select.appendChild(opt);
    }
};

SelectOptionsUpdater.prototype.createOption = function (value, displayValue) {
    var option = document.createElement('option');
    option.setAttribute('value', value);
    option.innerHTML = displayValue;
    return option;
};

/**
 * @param urls
 * @param service
 * @param modalId
 * @constructor
 */
var DatabaseInfoFetcher = function (urls, service, modalId) {
    ObjectBuilder.call(this, undefined);
    this.urls = urls;
    this.service = service;
    this.loadingIconController = new LoadingIcon('homeBtn');
    this.modalController = new ModalController((modalId == undefined) ? 'modal' : modalId);
};

DatabaseInfoFetcher.prototype = Object.create(ObjectBuilder.prototype);
DatabaseInfoFetcher.prototype.constructor = DatabaseInfoFetcher;

DatabaseInfoFetcher.prototype.setUrls = function(urls) {
    this.urls = urls;
};

DatabaseInfoFetcher.prototype.getSchemas = function (connectionName, callback) {
    var store = this;
    var configuration = this.getObject('get', store.urls["database"]["schemas"], {
        "connection":connectionName
    });

    this.loadingIconController.setLoading(true);

    this.service(configuration).then(function(response) {

        store.loadingIconController.setLoading(false);

        if (response.status == 200) {
            // ok
            console.log(response);
            callback(response["schemas"]);
            return;
        }

        store.modalController.init();
        store.modalController.setModal({
            title: "Error in server",
            body: response["textStatus"],
            footer: ""
        });
        store.modalController.pop();

    }, function(response) {
        store.modalController.init();
        store.modalController.setSmallModal();
        store.modalController.setModal({
            title: "Error in connection",
            body: response["textStatus"],
            footer: ""
        });
        store.modalController.pop();
        store.loadingIconController.setLoading(false);
    });
};

DatabaseInfoFetcher.prototype.getTables = function (options, callback) {
    var store = this;
    var configuration = this.getObject('get', this.urls["database"]["tables"], {
        connection: options["connection"],
        schema: options["schema"]
    });

    this.loadingIconController.setLoading(true);

    this.service(configuration).then(function(response) {

        store.loadingIconController.setLoading(false);
        if (response["status"] == 200) {

            if (callback != undefined) {
                callback(response["tables"]);
                return;
            }
        }

        store.modalController.init();
        store.modalController.setModal({
            title: "Error in server",
            body: response["textStatus"],
            footer: ""
        });
        store.modalController.pop();

    }, function() {
        // error
        store.loadingIconController.setLoading(false);
    });

};

/**
 * @param tableId
 * @param formId
 * @param connectionSelectId
 * @param schemasSelectId
 * @param service
 * @param baseApiUrl
 * @param modalId
 * @constructor
 */
var TableController = function (tableId, formId, connectionSelectId, schemasSelectId, service, baseApiUrl, modalId) {
    ObjectBuilder.call(this, formId);
    this.tableId = tableId;
    this.connectionSelectId = (connectionSelectId == undefined) ? 'edit_connection' : connectionSelectId;
    this.schemasSelectionId = (schemasSelectId == undefined) ? 'schemaSelect' : schemasSelectId;
    this.databaseTable = undefined;
    this.service = service;
    this.baseApiUrl = (baseApiUrl == undefined) ? '/api/urls' : baseApiUrl;
    this.urls = {};
    var modalFinalId = (modalId == undefined) ? 'modal' : modalId;
    this.modalController = new ModalController(modalFinalId);
    this.configuration = {};
    this.fetcher = new DatabaseInfoFetcher(this.urls, service, modalFinalId);
    this.loadIcon = new LoadingIcon('homeBtn');
    this.editor = undefined;
    this.coms = undefined;
};

TableController.prototype = Object.create(ObjectBuilder.prototype);
TableController.prototype.constructor = TableController;

/**
 * @param dynamicTableId
 */
TableController.prototype.init = function(dynamicTableId) {

    var store = this;
    this.configuration["dynamicTable"] = dynamicTableId;

    this.bindWithSelect(this.connectionSelectId, "connection", function() {
        store.fetcher.getSchemas(store.configuration['connection'], function(s) {
            store.setSchemas(s);
        });
    });

    this.fetchUrls(function() {
        store.fetcher.getSchemas(store.configuration["connection"], function(s) {
            store.setSchemas(s);
        });
    });

    this.bindWithSelect(this.schemasSelectionId, "schema", function() {
        store.fetcher.getTables(store.configuration, function(t) {
            store.setTable(t);
        });
    });

    this.bindClicks();

    this.bindDynamicTableWithEditor(this.configuration["dynamicTable"]);

    this.setTable();
};

/**
 * @param editor
 */
TableController.prototype.setEditor = function (editor) {
    this.editor = editor;
};

/**
 * @returns {undefined|EditorManager}
 */
TableController.prototype.getEditor = function() {
    return this.editor;
};

/**
 * @param tables
 */
TableController.prototype.setTable = function(tables) {

    var configuration = {
        data: tables,
        columns: [{data: "TABLE_NAME", title: "Table Name"}]
    };

    if (this.databaseTable != undefined) {
        this.databaseTable.destroy();
        $("#" + this.tableId).empty();
    }

    this.databaseTable = $("#" + this.tableId).DataTable(configuration);
};

/**
 * @param callback
 */
TableController.prototype.fetchUrls = function(callback) {
    var store = this;

    var configuration = this.getObject('get', this.baseApiUrl, {});

    this.loadIcon.setLoading(true);

    this.service(configuration).then(function(response) {
        store.loadIcon.setLoading(false);
        if (response.status == 200) {
            store.setUrls(response.urls);
            if (callback != undefined) {
                callback();
            }
            return;
        }

        store.modalController.init();
        store.modalController.setSmallModal();
        store.modalController.setModal({
            title:"Error fetching the urls",
            body: response.data["textStatus"],
            footer: ""
        });

        store.modalController.pop();

    }, function(response) {
        // response error
        store.modalController.init();
        store.modalController.setSmallModal();
        store.modalController.setModal({
            title:"Error in the connection",
            body: response.data,
            footer: ""
        });
        store.loadIcon.setLoading(false);
        store.modalController.pop();
    });

};

/**
 * @param schemas
 */
TableController.prototype.setSchemas = function(schemas) {

    console.log(this.schemasSelectionId);

    var selectController = new SelectOptionsUpdater(this.schemasSelectionId);

    var data = [];
    for (var i = 0; i < schemas.length; i++) {
        data.push({
            value: schemas[i]["Database"],
            displayValue: schemas[i]["Database"]
        })
    }

    selectController.updateSelect(data);

};

/**
 * @param root
 */
TableController.prototype.handleClick = function(root) {
    var tableName = root.configuration["table"];
    var schema = root.configuration["schema"];
    if (root.editor != undefined) {
        root.editor.addText(schema + "." + tableName);
    }
};

/**
 * @param root
 */
TableController.prototype.handleDoubleClick = function(root) {
    console.log(this);
    console.log(root);

    var url = root.urls["frontend"]["tableInfo"];
    var tableName = root.configuration["table"];

    if (TableController.openInNewWindow(tableName, url)) return;

    //show Modal
    var titles = [
        "COLUMN_NAME",
        "IS_NULLABLE",
        "DATA_TYPE",
        "COLUMN_TYPE",
        "COLUMN_KEY"
    ];

    var tableId = root.configuration["dynamicTable"];
    var table = root.createTable(titles, tableId);
    var row = root.getRow();
    var column = root.getColumn();

    column.appendChild(table);
    row.appendChild(column);

    var modalContent = {
        title: "Table Information",
        body: row,
        footer: root.createCheckBox('addMultiple')
    };

    root.modalController.init();
    root.modalController.setLargeModal();
    root.modalController.setModal(modalContent);
    root.modalController.pop();

    var apiUrl = root.urls["database"]["tableInfo"] + "?tableName=" + tableName + "&connection=" + root.configuration["connection"];
    console.log(apiUrl);
    var dataTable = $("#" + tableId).DataTable({
        ajax: apiUrl,
        dataSrc: "tables",
        responsive: true,
        columns: [
            {data: "COLUMN_NAME"},
            {data: "IS_NULLABLE"},
            {data: "DATA_TYPE"},
            {data: "COLUMN_TYPE"},
            {data: "COLUMN_KEY"}
        ]
    });

};

/**
 * @param tableName
 * @param url
 * @returns {*}
 */
TableController.openInNewWindow = function(tableName, url) {
    var checkboxElement = $("#openInTableInformationNewWindow");
    var root = this;
    if (checkboxElement.is(":checked")) {
        console.log(this);
        window.open(window.location.origin + url + "?tableName=" + tableName);
    }
    return checkboxElement.is(":checked");

};

/**
 * @param tableId
 */
TableController.prototype.bindDynamicTableWithEditor = function(tableId) {
    var root = this;
    $(document).on("click", "#" + tableId + " tbody tr td", function() {
        if (root.editor != undefined) {
            if ($("#addMultiple").is(":checked")) {
                if (!$(this).hasClass("info")) {
                    $(this).addClass("info");
                    var textToAdd = root.configuration["table"] + "." + $(this).html() + ",\n";
                    root.editor.addText(textToAdd);
                }
            }else{
                root.editor.addText(root.configuration["table"] + "." + $(this).html());
                root.modalController.hide();
            }
        }
    });
};

/**
 * source: http://stackoverflow.com/questions/1067464/need-to-cancel-click-mouseup-events-when-double-click-event-detected/1067484#1067484
 */
TableController.prototype.bindClicks = function () {
    var root = this;

    $("#" + this.tableId).on('click', 'tbody tr', function(e) {
        var store = this;
        setTimeout(function() {
            var dblclick = parseInt($(store).data('double'), 10);

            if (dblclick > 0)
            {
                $(store).data('double', dblclick - 1);
            }else{
                root.configuration["table"] = $(store).find('td').text();
                root.handleClick.call(e, root);
            }

        }, 300);

    }).on('dblclick', 'tbody tr', function(e) {
        $(this).data("double", 2);
        root.configuration["table"] = $(this).find('td').text();
        root.handleDoubleClick.call(e, root);
    });
};


/**
 * @param selectId
 * @param configuration
 * @param callback
 */
TableController.prototype.bindWithSelect = function(selectId, configuration, callback) {
    var select = document.getElementById(selectId);
    var store = this;

    this.configuration[configuration] = select.value;

    select.addEventListener('change', function(event) {
        // set the connection
        store.configuration[configuration] = select.value;

        if (callback != undefined) {
            callback();
        }

        event.preventDefault();
    });

};

/**
 * @param urls
 */
TableController.prototype.setUrls = function(urls) {
    this.urls = urls;
    this.fetcher.setUrls(urls);
};

/**
 * @returns {{}|*}
 */
TableController.prototype.getUrls = function() {
    return this.urls;
};

/**
 * @param baseUrl
 */
TableController.prototype.setBaseApiUrl = function(baseUrl) {
    this.baseApiUrl = baseUrl;
};

/**
 * @returns {string|*}
 */
TableController.prototype.getBaseApiUrl = function() {
    return this.baseApiUrl;
};

TableController.prototype.setSelectId = function(selectId) {
    this.selectId = selectId;
};

/**
 * @param coms
 */
TableController.prototype.bindCommunicationService = function(coms) {
    this.coms = coms;
};