/**
 * @param containerName
 * @param theme
 * @param mode
 * @param inputId
 * @constructor
 */
var EditorManager = function (containerName, theme, mode, inputId) {
    this.editorName = containerName; //containerName represents the input on which to insert
    this.editor = null;
    this.editorTheme = theme;
    this.editorMode = mode;
    this.inputText = document.getElementById(inputId);
};

EditorManager.prototype.initializeEditor = function () {
    this.editor = ace.edit(this.editorName);
    this.editor.setTheme(this.editorTheme);
    this.editor.getSession().setMode(this.editorMode);
    document.getElementById(this.editorName).style.fontSize = '14px';
};

EditorManager.prototype.setUpTestRunCommand = function(commService) {
    var commandOptions = {
        name:"testRun",
        bindKey: {win: 'Ctrl-r', mac: 'Command-r'},
        exec: function(editor) {
            commService.getResultsFromQuery(commService.query, commService.limit);
        }
    };
    this.editor.commands.addCommand(commandOptions);
};

/**
 * @param value
 */

EditorManager.prototype.setEditorValue = function (value) {
    //this.editor.setValue(this.removeFeedLines(value));
    this.editor.setValue(value);
};

EditorManager.prototype.getEditorValue = function () {
    //return this.removeFeedLines(this.editor.getValue());
    return this.editor.getValue();
};

EditorManager.prototype.addText = function(valueToAppend) {
    this.editor.insert(valueToAppend);
};

EditorManager.prototype.bindWithInputById = function () {
    this.syncWithInput();
    var store = this;
    this.editor.getSession().on('change', function() {
        //store.inputText.setAttribute('value', store.removeFeedLines(store.getEditorValue()));
        store.inputText.setAttribute('value', store.getEditorValue());
        console.log('input value after: ' + store.inputText.value);
    });
};

EditorManager.prototype.bindWithVariable = function (objInstance) {
    var store = this;
    objInstance.setQuery(this.getEditorValue());
    this.editor.getSession().on('change', function () {
        objInstance.setQuery(store.getEditorValue());
    });
};

EditorManager.prototype.syncWithInput = function () {
    this.setEditorValue(this.inputText.value);
};

/**
 * @param formId
 * @constructor
 */
var ObjectBuilder = function (formId) {
    this.formId = formId;
    this.myForm = document.getElementById(formId);
};

ObjectBuilder.prototype.getObjectFromForm = function() {
    var query = '#' + this.formId + ' :input';
    var inputs = $(query);
    var results = {};
    inputs.each(function() {
        console.log(this.getAttribute("type"));
        if (this.getAttribute("type") === "checkbox") {
            results[this.name] = ($(this).is(":checked") ? "1" : "0");
        }else{
            results[this.name] = $(this).val();
        }
        console.log(this.name + "-" + $(this).val());
    });
    return results;
};

/**
 * @param headers
 * @param tableId
 * @returns {Element}
 */
ObjectBuilder.prototype.createTable = function(headers, tableId) {
    var table = document.createElement("table");
    table.setAttribute("id", tableId);
    table.setAttribute("class", "table table-condensed table-striped");
    var header = document.createElement("thead");
    var content = document.createElement("tbody");

    var tRow = document.createElement("tr");

    for (var i = 0; i < headers.length; i++) {
        var temp = document.createElement("th");
        temp.innerHTML = headers[i];
        tRow.appendChild(temp);
    }

    header.appendChild(tRow);
    table.appendChild(header);
    table.appendChild(content);

    return table;

};

/**
 * @param attributes
 * @param content
 * @returns {Element}
 */
ObjectBuilder.prototype.createButton = function(attributes, content) {
    var button = document.createElement("button");
    if (attributes != undefined) {
        button = attributes(button);
    }
    if (content != undefined) {
        button.appendChild(content(button));
    }
    return button;
};

/**
 * @param list
 * @param content
 * @param configuration
 * @returns {Element}
 */
ObjectBuilder.prototype.createBreadCrumbs = function(list, content, configuration) {
    var ol = document.createElement('ol');
    ol.setAttribute('class', 'breadcrumb');

    for (var i = 0; i < list.length; i++) {
        var currentElement = list[i];
        var li = document.createElement('li');

        if (i == list.length - 1) {
            li.setAttribute("class", "active");
        }

        li = configuration(li, currentElement);

        li.appendChild(content(currentElement));

        ol.appendChild(li);
    }

    return ol;
};

/**
 * @param qty
 * @param btnAttributes
 * @param btnContent
 * @returns {Element}
 */
ObjectBuilder.prototype.createButtonGroup = function(qty, btnAttributes, btnContent) {
    var container = document.createElement("div");
    container.setAttribute("class", "btn-group pull-right");
    container.setAttribute("role", "group");

    for (var i = 0; i < qty; i++) {
        var button = document.createElement("button");
        button = btnAttributes(button, qty, i);
        button.appendChild(btnContent(button, qty, i));
        container.appendChild(button);
    }

    return container;
};

/**
 * @param list
 * @param content
 * @param attributes
 * @returns {Element}
 */
ObjectBuilder.prototype.createList = function(list, content, attributes) {
    var ul = document.createElement("ul");
    ul.setAttribute("class", "list-group");
    for (var i in list) {
        var li = document.createElement('li');
        var element = list[i];
        li = attributes(li, element);
        li.appendChild(content(element));
        ul.appendChild(li);
    }
    return ul;
};

/**
 * @param description
 * @returns {Element}
 */
ObjectBuilder.prototype.getEmptyList = function(description) {
    var container = this.getRow();
    var column = this.createColumn("col-sm-12");

    var h = document.createElement("h3");
    h.setAttribute("class", "empty-title");
    h.innerHTML = description;

    column.appendChild(h);

    container.appendChild(column);
    return container;
};

/**
 * @param config
 * @returns {Element}
 */
ObjectBuilder.prototype.createFormGroup = function(config) {
    var div = document.createElement("div");
    var label = document.createElement("label");
    label.setAttribute("class", config["label"]["class"]);
    label.innerHTML = config["label"]["text"];
    div.setAttribute("class", config["container"]["class"]);
    var divContainer = document.createElement("div");
    divContainer.setAttribute("class", config["inputContainer"]["class"]);
    var input = document.createElement(config["input"]["type"]);

    if (config["input"]["type"] == "button") {
        input.innerHTML = config["input"]["text"];
    }

    input.setAttribute("type", config["input"]["btnType"]);
    input.setAttribute("class", config["input"]["class"]);
    input.setAttribute("id", config["input"]["id"]);
    if (config["input"]["type"] != "button") {
        input.setAttribute("value", config["input"]["value"]);
        input.setAttribute("name", config["input"]["name"]);
    }
    divContainer.appendChild(input);
    div.appendChild(label);
    div.appendChild(divContainer);
    return div;
};

/**
 * @param fields
 * @param inputs
 * @returns {Element}
 */
ObjectBuilder.prototype.createForm = function(fields, inputs) {
    var form = document.createElement("form");
    form.setAttribute("method", fields["method"]);
    form.setAttribute("action", fields["action"]);
    form.setAttribute("id", fields["id"]);
    form.setAttribute("name", fields["name"]);
    form.setAttribute("class", fields["class"]);

    for (var i = 0; i < inputs.length; i++) {
        form.appendChild(this.createFormGroup(inputs[i]));
    }

    return form;
};

/**
 * @param checkboxId
 * @returns {Element}
 */
ObjectBuilder.prototype.createCheckBox = function(checkboxId) {
    var container = document.createElement('div');
    container.setAttribute("class", "checkbox");
    var label = document.createElement("label");
    var paragraph = document.createElement("p");
    paragraph.innerHTML = "Add mulitple names";
    paragraph.setAttribute("class", "details");

    var checkbox = document.createElement("input");
    checkbox.setAttribute("id", checkboxId);
    checkbox.setAttribute("type", "checkbox");
    label.appendChild(checkbox);
    label.appendChild(paragraph);
    container.appendChild(label);
    var col = document.createElement("div");
    col.setAttribute("class", "col-sm-12");
    col.appendChild(container);

    return col;
};

/**
 * @returns {Element}
 */
ObjectBuilder.prototype.createBeatingHeart = function(id, action) {
    var button = [{
        "class": "btn btn-sm btn-love-o",
        "icon": "pull-right glyphicon glyphicon-heart animated pulse fav-icon",
        "query-id": id,
        "action": action
    }];

    return this.createButtonGroup(button.length, function(btn, qty, i) {
        var config = button[i];
        btn.setAttribute("class", config["class"]);
        btn.setAttribute("query-id", config["query-id"]);
        return btn;
    }, function(btn, qty, i) {
        //content
        var span = document.createElement("span");
        span.setAttribute("class", button[i]["icon"]);
        btn.addEventListener("click", button[i]["action"]);
        return span;
    });
};

/**
 * @param classes
 * @returns {Element}
 */
ObjectBuilder.prototype.createColumn = function(classes) {
    var column = this.getColumn();
    column.setAttribute("class", classes);
    return column;
};

/**
 * @returns {Element}
 */
ObjectBuilder.prototype.getColumn = function() {
    var column = document.createElement("div");
    column.setAttribute("class", "col-sm-12 table-responsive");
    return column;
};

/**
 * @returns {Element}
 */
ObjectBuilder.prototype.getRow = function() {
    var row = document.createElement("div");
    row.setAttribute("class", "row");
    return row;
};

ObjectBuilder.prototype.getBackButton = function() {
    var button = document.createElement("button");
    button.setAttribute("type", "button");
    button.setAttribute("class", "btn btn-sm btn-default-arny pull-right");
    button.setAttribute('id', 'path-back-button');
    var span = document.createElement("span");
    span.innerHTML = "Back";
    span.setAttribute("class", "glyphicon glyphicon-chevron-left");
    button.appendChild(span);
    return button;
};

/**
 * @param url
 * @param method
 * @param query
 * @param limit
 * @returns {{method: *, url: *, data: {query: *, limit: *}}}
 */
ObjectBuilder.prototype.getObjectToPostFromQuery = function (url, method, query, limit) {
    return {
        'method': method,
        'url': url,
        'data': {
            'query': query,
            'limit': limit
        }
    };
};
/**
 * @param method
 * @param url
 * @param data
 * @returns {{method: *, url: *, data: *}}
 */
ObjectBuilder.prototype.getObject = function(method, url, data) {
    return {
        'method': method,
        'url':url,
        'data':data
    };
};

/**
 * @param baseApiUrl
 * @param service
 * @param modalId
 * @param loadingIconId
 * @constructor
 */
var UrlFetcher = function(baseApiUrl, service, modalId, loadingIconId) {
    ObjectBuilder.call(this, undefined);
    this.baseApiUrl = baseApiUrl;
    this.service = service;
    this.modalId = modalId;
    this.loadingIconId = loadingIconId;
    this.modalController = new ModalController(modalId);
    this.loadingIcon = new LoadingIcon(loadingIconId);
};

UrlFetcher.prototype = Object.create(ObjectBuilder.prototype);
UrlFetcher.prototype.constructor = UrlFetcher;

/**
 * @param callback
 */
UrlFetcher.prototype.fetchUrls = function(callback) {
    var store = this;
    var configuration = this.getObject('get', this.baseApiUrl, {});
    this.loadingIcon.setLoading(true);

    this.service(configuration).then(function(response) {
        //success
        store.loadingIcon.setLoading(false);
        if (response["status"] == 200) {
            if (callback != undefined) {
                callback(response["urls"]);
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
        //on error
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
 * @param service
 * @param formId
 * @param runApiUrl
 * @param modalId
 * @constructor
 */
var CommunicatorService = function (service, formId, runApiUrl, modalId) {
    this.service = service;
    this.objectBuilder = new ObjectBuilder(formId);
    this.query = "";
    this.limit = 0;
    this.databaseConnection = 'default';
    this.table = undefined;
    this.runApiUrl = (runApiUrl == undefined) ? '/api/run' : runApiUrl;
    this.modal = new ModalController((modalId == undefined) ? 'modal' : modalId);
};

CommunicatorService.prototype.setLimit = function (limit) {
    this.limit = parseInt(limit);
};

CommunicatorService.prototype.setQuery = function (query) {
    this.query = query;
};

CommunicatorService.prototype.bindWithButton = function (buttonID) {
    var button = document.getElementById(buttonID);
    var store = this;
    button.addEventListener('click', function (event) {
        event.preventDefault();
        //do the request
        console.log("Query to send: " + store.query);
        store.getResultsFromQuery(store.query, store.limit);
    });
};

CommunicatorService.prototype.bindInputForConnectionById = function (inputId) {
    var input = document.getElementById(inputId);
    var store = this;

    input.addEventListener('change', function(event) {
        event.preventDefault();
        store.databaseConnection = input.value;
        console.log(input.value);
    });
};

CommunicatorService.prototype.bindInputForLimitById = function (inputId) {
    var input = document.getElementById(inputId);
    var store = this;
    this.setLimit(input.value);
    input.addEventListener('change', function(event) {
        event.preventDefault();
        store.setLimit(input.value);
        console.log(input.value);
    });
};

CommunicatorService.prototype.setSchema = function(schema) {
    this.schema = schema;
};

CommunicatorService.prototype.setConnection = function(con) {
    this.databaseConnection = con;
};

CommunicatorService.prototype.getResultsFromQuery = function () {
    var loadingIcon = new LoadingIcon('homeBtn');
    loadingIcon.setLoading(true);
    var store = this;

    var dataToWrap =  {
        query: store.query,
        limit: store.limit,
        connection: store.databaseConnection
    };

    var dataToSend = this.objectBuilder.getObject('post', store.runApiUrl, dataToWrap);

    this.service(dataToSend).then(function (data) {
        if (data['status'] == 200) {
            //put the results in table

            var isPieChartCompatibleQuery = "#isPieChartCompatible";

            document.getElementById('queryDuration').innerHTML = (data['queryDuration'] + ' seconds');
            document.getElementById('isPieChartCompatible').innerHTML = data['isPieChartCompatible'];
            if (!data['isPieChartCompatible'])
            {
                $(isPieChartCompatibleQuery)
                    .addClass("twoTimes animated flash")
                    .one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function() {
                        $(this).removeClass("flash");
                    });
            }

            var keys = Object.keys(data['results'][0]);
            var columns = [];
            for (var i = 0; i < keys.length; i++) {
                columns.push({
                    "data": keys[i],
                    "title": keys[i]
                });
            }


            var resultsTableQuery = "#resultsTable";


            if (store.table != undefined) {
                store.table.destroy();
                $(resultsTableQuery).empty(); //maybe column change
            }

            store.table = $(resultsTableQuery).DataTable({
                data: data["results"],
                columns: columns,
                fixedHeader: true,
                fixedColumns: true
                //responsive: true,
            });

        }else {
            store.modal.init();
            store.modal.setLargeModal();
            store.modal.setModal({
                title: "Oups!",
                body: data["textStatus"],
                footer: ""
            });
            store.modal.pop();
        }

        loadingIcon.setLoading(false);
    }, function() {
        alert('Error');
        loadingIcon.setLoading(false);
    });
};

/**
 *
 * @param modalId
 * @constructor
 */
var ModalController = function(modalId) {
    this.modalQuery = "#" + modalId;
    this.modal = undefined;
};

ModalController.prototype.init = function() {
    this.modal = $(this.modalQuery);
};

ModalController.prototype.setSmallModal = function () {
    var content = this.modal.find('div.modal-dialog');
    content.removeClass("modal-lg");
    content.addClass("modal-sm");
};

ModalController.prototype.setLargeModal = function() {
    var content = this.modal.find('div.moda-dialog');
    content.removeClass("modal-sm");
    content.addClass("modal-lg");
};

/**
 * @param data
 */
ModalController.prototype.setDOM = function(data) {
    var modalTitle = this.modal.find("h4.modal-title");
    var modalBody = this.modal.find("div.modal-body");
    var footer = this.modal.find("div.modal-footer");

    modalTitle.html(data.title);
    modalBody.html("");
    modalBody.append(data.body);
    if (data.footer != "" && data.footer != undefined) {
        footer.show();
        footer.html(data.footer);
    }else{
        footer.hide();
    }
};

ModalController.prototype.setModal = function(data) {

    var modalTitle = this.modal.find("h4.modal-title");
    var modalBody = this.modal.find("div.modal-body");
    var footer = this.modal.find("div.modal-footer");

    modalTitle.html(data.title);
    modalBody.html(data.body);

    if (data.footer != "" && data.footer != undefined) {
        footer.show();
        footer.html(data.footer);
    }else{
        footer.hide();
    }

};

/**
 * @param extra
 */
ModalController.prototype.popConnectionError = function(extra) {
    this.init();
    this.setLargeModal();

    var content = {
        title: "Connection Error",
        body: "There was an unexpected error in the connection. " + extra,
        footer: ""
    };

    this.setModal(content);
    this.pop();
};

ModalController.prototype.pop = function () {
    if (this.modal != undefined && this.modal != null)
    {
        console.log(this.modal);
        this.modal.modal('show');
    }
};

ModalController.prototype.hide = function() {
    this.modal.modal('hide');
};

ModalController.prototype.toggle = function() {
    this.modal.modal('toggle');
};

/**
 * @param formId
 * @param apiUrl
 * @param method
 * @param service
 * @param redirectBaseUrl
 * @param callbacks
 * @param queryId
 * @constructor
 */
var FormManager = function (formId, apiUrl, method, service, redirectBaseUrl ,callbacks, queryId) {
    //inherit from ObjectBuilder
    ObjectBuilder.call(this, formId);
    this.url = apiUrl;
    this.httpMethod = method;
    this.service = service;
    this.queryId = queryId;
    this.request = null;

    this.redirectBaseUrl = (redirectBaseUrl == undefined) ? "/query/" : redirectBaseUrl;

    this.modalController = new ModalController('modal');

    var store = this;
    this.defaultCallbacks = {
        onDone: function(data, textStatus) {
            console.log("onDone");
            //console.log(data);
            if (data["status"] == 200) {
                //alert(data["textStatus"]);

                var queryId = data["queryId"];

                var modalData = {
                    title: "Response",
                    body: data["textStatus"],
                    footer: "<a href='" + store.redirectBaseUrl + "' class='btn btn-redirect'>Go to Query's page</a>"
                };

                store.modalController.init();
                store.modalController.setSmallModal();
                store.modalController.setModal(modalData);
                store.modalController.toggle();
                //window.location.replace(store.redirectBaseUrl + data["queryId"]);
            }else if(data["status"] == 202) {
                alert(data["textStatus"]);
                window.location.replace(store.redirectBaseUrl);
            }else{
                alert(data['textStatus']);
            }
        },
        onFail: function (jqXHR, textStatus) {
            alert('Error in connection, ' + textStatus);
        }
    };

    this.callbacks = (callbacks == undefined) ? this.defaultCallbacks : callbacks;
};

FormManager.prototype = Object.create(ObjectBuilder.prototype);
FormManager.prototype.constructor = FormManager;

FormManager.prototype.getAction = function() {
    if (this.httpMethod == "DELETE") {
        return "delete";
    }else if(this.httpMethod == "PUT") {
        return "edit";
    }else if(this.httpMethod == "POST") {
        return "register";
    }else {
        return "fetch";
    }
};

FormManager.prototype.getFormId = function() {
    return this.formId;
};

FormManager.prototype.bindFormWithService = function(setUp) {
    var store = this;
    this.myForm.addEventListener('submit', function (event) {
        event.preventDefault();
        //code... execute AJAX
        if (confirm("Are you sure you want to " + store.getAction() + " this entry?")) {
            var data = store.getEncapsulatedData();
            console.log(data);
            if (setUp == undefined) {
                store.service(data)
                    .then(store.callbacks.onDone,
                    store.callbacks.onFail);
            }else{
                setUp(store.service(data));
            }
        }

    });
};

FormManager.prototype.clearForm = function() {
    var query = "#" + this.formId + " :input";
    var inputs = $(query);
    inputs.each(function() {
        $(this).val("");
    });

    console.log("form was cleared");
};

FormManager.prototype.setCallBacks = function (callbacks) {
    if (callbacks != undefined && callbacks.onDone != undefined && callbacks.onFail != undefined)
        this.callbacks = callbacks;
    else
        this.callbacks = this.defaultCallbacks;
};

FormManager.prototype.getDefaultCallBacks = function () {
    return this.defaultCallbacks;
};

FormManager.prototype.getRequest = function() {
    return this.request;
};

FormManager.prototype.setHTTPMethod = function(method) {
    this.httpMethod = method;
};

FormManager.prototype.setUrl = function(url) {
    this.url = url;
};

FormManager.prototype.getEncapsulatedData = function (overRide) {
    if (overRide == undefined) {
        var data = {
            url: this.url,
            method: this.httpMethod,
            data: this.getObjectFromForm()
        };
        data["data"]["id"] = this.queryId;
        return data;
    }else{
        return overRide();
    }
};

/**
 *
 * @param elementId
 * @constructor
 */
var LoadingIcon = function(elementId) {
    this.domElement = document.getElementById(elementId);
    //this.elementId = elementId;
    this.originalClass = "glyphicon glyphicon-home";
    this.loadingIconClass = "glyphicon glyphicon-cog";
};

/**
 * @param status
 */
LoadingIcon.prototype.setLoading = function(status) {
    if (status) {
        this.setTheLoadingIcon();
        $(this.domElement).addClass("spin");
    }else{
        this.resetTheLoadingIcon();
        $(this.domElement).removeClass("spin");
    }
};

LoadingIcon.prototype.setTheLoadingIcon = function() {
    this.originalClass = this.domElement.className;
    $(this.domElement).removeClass(this.originalClass);
    $(this.domElement).addClass(this.loadingIconClass);
};

LoadingIcon.prototype.resetTheLoadingIcon = function() {
    $(this.domElement).removeClass(this.loadingIconClass);
    $(this.domElement).addClass(this.originalClass);
};

/**
 * @param service
 * @param queryId
 * @param chartType
 * @param url
 * @constructor
 */
var ResultsFetcher = function(service, queryId, chartType, url) {
    this.service = service;
    this.objectBuilder = new ObjectBuilder();
    this.queryId = queryId;
    this.url = (url == undefined) ? '/api/chartData' : url;
    this.chartData = {};
    this.chartType = chartType;
    this.options = {};
    this.snapshotUsed = "";
    this.snapshot = undefined;
};

/**
 * @param snap
 */
ResultsFetcher.prototype.setSnapshot = function(snap) {
    this.snapshot = snap;
};

/**
 * @returns {string|*}
 */
ResultsFetcher.prototype.getSnapshot = function() {
    return this.snapshot;
};

/**
 * @param newChartType
 */
ResultsFetcher.prototype.setChartType = function(newChartType) {
    this.chartType = newChartType;
};

/**
 * @param callback
 */
ResultsFetcher.prototype.getResults = function(callback) {
    var opts = {
        q: this.queryId,
        type: this.chartType
    };

    if (this.snapshot != undefined) {
        opts["snapshot"] = this.snapshot;
    }
    var store = this;

    this.service(this.objectBuilder.getObject('get', store.url, opts))
        .then(function(response) {
            if (response.status == 200) {
                callback(response);
            }else {
                alert(response["textStatus"]);
            }
        }, function(response) {
            alert(response.status);
        });

};

/**
 * @param callback
 */
ResultsFetcher.prototype.fetchData = function(callback) {
    var opts = {
        q : this.queryId,
        type: this.chartType
    };

    if (this.snapshot != undefined) {
        opts["snapshot"] = this.snapshot;
    }

    var store = this;
    this.service(this.objectBuilder.getObject('get', store.url, opts))
        .then(function(response) {
            if (response.status == 200)
            {
                store.chartData = response.chartData;
                store.snapshotUsed = response["snapshot"];
                console.log(store.snapshotUsed);
                store.setOptions(callback);
            }else if(response.status == 405)
            {
                //formatting not allowed
                console.log(response);
            }
            else{
                console.log(response);
                alert(response["textStatus"]);
            }
        }, function(response) {
            alert(response.status);
        });
};

/**
 * @param list
 * @returns {*}
 */
ResultsFetcher.prototype.parseFloatArray = function(list) {
    return list.map(parseFloat);
};

ResultsFetcher.prototype.setOptionsForPieChart = function(callback) {
    var store = this;

    this.parseNumbersFromData();

    //console.log(this.chartData);

    this.options = {
        chart: store.chartData['chart']
        , title: {
            text: store.title,
            x: -20
        }, subtitle: {
            text: store.descr,
            x: -20
        }, plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: "<b>{point.name} = {point.percentage:.1f}%</b>"
                },
                showInLegend: true
            }
        }, tooltip: {
            pointFormat: '<b>{point.y} {series.name} = {point.percentage:.1f}%</b>'
        },
        series: [store.chartData.series]
    };

    //console.log(store.options);

    if (callback != undefined) {
        callback();
    }
};

ResultsFetcher.prototype.parseNumbersFromData = function() {
    if (this.chartType == 'pie')
    {
        this.parseNumericValuesForPie();
    }else{
        this.parseNumericValuesForUniversal();
    }
};

ResultsFetcher.prototype.parseNumericValuesForUniversal = function() {
    for (var i = 0; i < this.chartData.series.length; i++) {
        var current = this.chartData.series[i];
        this.chartData.series[i].data = this.parseFloatArray(current.data);
    }
};

ResultsFetcher.prototype.parseNumericValuesForPie = function() {
    for (var i = 0; i < this.chartData.series.data.length; i++) {
        var current = this.chartData.series.data[i];
        this.chartData.series.data[i].y = parseFloat(current.y);
    }
};

/**
 * @param callback
 */
ResultsFetcher.prototype.setOptionsForUniversalChart = function(callback) {

    this.parseNumbersFromData();

    var store = this;

    //console.log(this.chartData);

    this.options = {
        exporting: {
            chartOptions: {
                plotOptions: {
                    series: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                }
            },
            fallbackToExportServer: false
        },
        chart: store.chartData.chart
        , title: {
            text: store.title,
            x: -20
        }, subtitle: {
            text: store.descr,
            x: -20
        }, yAxis: store.chartData.yAxis,
        plotOptions: {
            /*line: {
                dataLabels:{
                    enabled: true
                }
            }*/
        },
        tooltip: {
            shared: true
        }, series: store.chartData.series,
        xAxis: store.chartData.xAxis
    };

    //console.log(this.options);

    if (callback != undefined) {
        callback();
    }
};

/**
 * @param callback
 */
ResultsFetcher.prototype.setOptions = function(callback) {

    if (this.chartType == 'pie') {
        this.setOptionsForPieChart(callback);
    }else{
        this.setOptionsForUniversalChart(callback);
    }

};

/**
 * @param divId
 * @param title
 * @param descr
 * @param service
 * @param queryId
 * @param chartType
 * @param url
 * @param snapshotUsedId
 * @constructor
 */
var ChartManager = function(divId, title, descr, service, queryId, chartType, url, snapshotUsedId) {
    ResultsFetcher.call(this, service, queryId, chartType, url);
    this.container = $("#" + divId);
    this.title = title;
    this.descr = descr;
    this.snapshotUsedContainerId = snapshotUsedId;
};

ChartManager.prototype = Object.create(ResultsFetcher.prototype);
ChartManager.prototype.constructor = ChartManager;

ChartManager.prototype.initializeChart = function() {
    var store = this;
    if (this.chartType != 'table') {
        this.fetchData(function() {
            //console.log(store.options);
            store.updateSnapshotUsedText();
            store.container.highcharts(store.options);
        });
    }
    this.updateSnapshotUsedText();
};

ChartManager.prototype.updateSnapshotUsedText = function()
{
    console.log("snapshotUsedText called");
    var store = this;
    var snapshotContainer = document.getElementById(store.snapshotUsedContainerId);
    if (snapshotContainer != undefined) {
        snapshotContainer.innerHTML = store.snapshotUsed;
    }
};

/**
 * @param buttonId
 * @param service
 * @param baseApiUrl
 * @param queryId
 * @param options
 * @constructor
 */
var SnapshotFileDownload = function (buttonId, service, baseApiUrl, queryId, options) {
    UrlFetcher.call(this, baseApiUrl, service, options["modal"]["id"], options["loading"]["id"]);
    this.urls = undefined;
    this.buttonId = buttonId;
    this.button = undefined;
    this.snapshotId = undefined;
    this.queryId = queryId;
};

SnapshotFileDownload.prototype = Object.create(UrlFetcher.prototype);
SnapshotFileDownload.prototype.constructor = SnapshotFileDownload;

SnapshotFileDownload.prototype.init = function() {

    var store = this;
    this.fetchUrls(function(urls) {
        store.urls = urls;
        //set up
        store.setUpButton();
    });
};

SnapshotFileDownload.prototype.setUpButton = function() {
    var store = this;
    store.button = document.getElementById(store.buttonId);

    if (store.button != undefined) {
        store.bindButton();
    }
};

/**
 * @param snapshot
 */
SnapshotFileDownload.prototype.setSnapshot = function(snapshot) {
    this.snapshotId = snapshot;
};

/**
 * @param queryId
 */
SnapshotFileDownload.prototype.setQueryId = function(queryId) {
    this.queryId = queryId;
};

SnapshotFileDownload.prototype.bindButton = function() {

    var store = this;

    this.button.addEventListener("click", function() {
        //download the file!

        var snapshotUrl = (store.snapshotId != undefined) ? "&snapshot=" + store.snapshotId : "";
        var url = store.urls["snapshots"]["download"];
        url += "?q=" + store.queryId + snapshotUrl;
        console.log(url);
        window.open(url);
    });

};


/**
 *
 * @param service
 * @param userTable
 * @param username
 * @param apiUrl
 * @constructor
 */
var UserManager = function(service, userTable, username, apiUrl) {
    this.username = username;
    this.service = service;
    this.table = userTable;
    this.url = (apiUrl == undefined) ? "/api/user/role" : apiUrl;
};

UserManager.prototype = Object.create(ObjectBuilder.prototype);
UserManager.prototype.constructor = UserManager;

UserManager.prototype.initTable = function() {

    var store = this;

    this.table = $("#users").DataTable({
        ajax: {
            url: store.url,
            dataSrc: ''
        },
        "createdRow": function(row, data) { //receives a third parameter index!
            if (data["developer"]) {
                $('td', row).eq(3).html("<span class='glyphicon glyphicon-ok-circle'></span>");
            }else{
                $('td', row).eq(3).html("<span class='glyphicon glyphicon-unchecked'></span>");
            }
        },
        fixedHeader: true,
        select: true,
        responsive: true,
        columns: [
            {
                data: 'name',
                title: 'Name'
            },
            {
                data: 'username',
                title: 'Username'
            },
            {
                data: 'email',
                title: 'E-Mail'
            },
            {
                data: 'developer',
                title: 'isDeveloper'
            }
        ]
    });

};

UserManager.prototype.setUsername = function (u) {
    this.username = u;
};

UserManager.prototype.getUsername = function () {
    return this.username;
};

UserManager.prototype.redrawTable = function() {
    if (this.table != undefined) {
        this.table.destroy();
        $("#users").empty();
        this.initTable();
    }
};

UserManager.prototype.bindClick = function() {
    var store = this;

    this.table.on('select', function(e, dt, type, indexes) {

        if (type === 'row') {
            var data = dt.row({selected:true}).data();
            store.setUsername(data.username);
            if (data.developer)
            {
                store.demote();
            }else{
                store.promote();
            }
        }
    });
};

UserManager.prototype.promote = function() {
    this.callService('promote', 'post');
};

UserManager.prototype.demote = function () {
    this.callService('demote', 'delete');
};

UserManager.prototype.callService = function(action, method, callbacks) {
    if (confirm('Are you sure you want to ' + action + ' the user ' + this.getUsername() + '?'))
    {
        var store = this;
        this.service(store.getObject(method, store.url, {username:store.getUsername()}))
            .then(function(response) {
                console.log(response);
                if (response.status == 200 || response.status == 201) {
                    //is ok, reload the table
                    store.redrawTable();
                }
            }, function (response) {
                console.log(response);
            });
    }
};