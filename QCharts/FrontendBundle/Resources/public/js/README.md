JS Documentation
================

- JS in ```/general``` directory
    + ```Classes.js```
        - Contains all the classes that help to use with ease the Highcharts, Ace code editor
        and to handle the communication with server to display the results.
    + ```ajquery.min.js```
        + Version 2.1.4
- JS in ```/main```
    + Contains the JS to toggle the panels that contain the information of
     the Queries displayed in the main page path.
- JS in ```/highChartsTheme```
    + Optional theme for Highcharts.
- UrlFetcher Class, located in the ```Classes.js``` file.
    + This class is use to fetch all the necessary urls from the current project, since it was recommended to add a URL
    prefix to QCharts in the routing, the urls to the API and the redirects vary. This class comes in play by fetching the relative urls
    from the server, so only the base API url needs to be defined.
        * ```qcharts.api.urls``` is the name of the routing that returns all relative urls, with the prefix if applied.
- JS Samples
    - Sample (1) code for initializing the code Editor in either the Edit or Registration Form using ```EditorManager``` class, this following code will also
    sync the input from the form and the code editor.
    - Sample (2) code to manage the form on its submit using the ```FormManager```, this sample code will do the HTTP request with the given method.
    The ```$.ajax``` represents the service to use, all services that use the ```deferred.then(...)``` interface are supported.
    - Sample (3) code to fetch the "Test Run" in the edit of the requested Query using the ```CommunicatorService```, this sample will fetch the results from the
    server and display them in the table, using the limit from the form and binding a Button that will trigger the fetch.
    In this sample you can also see how the ```EditorManager``` can bind also a variable.
    - Sample (4) code will show how to override the callbacks if required from the ```FormManager```.
    - Sample (5) code shows the ```ChartManager```, this class is used to unify the service of initializing the chart
    from HighCharts and the fetching of the results from the server.
    - Sample (6) code shows the ```SnapshotFileDownload``` in action, this class manages the download file of the
    snapshot file.
    - Sample (7) uses the class ```DeleteController```, this class binds a button with the action of deleting the query
    from the Server, this class is only used when editing the Query.
    - Sample (8) code shows the use of the ```TableController``` which is used for controlling the table in the
    Edit and Register view for the display of the Databases' schemas and tables.
    - Sample (9) codes shows how to display the Folder Browser, in other words Directory browser, which is handled by
    the class ```DirectoryController``` in file ```js/table/directoryClasses.js```.
    - Sample (10) code shows the usage of ```CronController```, this is used to display a more user friendly interface
    when registering the Cron. This attaches to the form input from the Query that holds the cron expression.
    - Sample (11) code shows the usage of the ```SnapshotTableController```, this class is responsible of controlling
    the Table when showing the data from a snapshot. This table is only used when showing the results from the query.
    - Sample (12) code shows the functionality of the ```CalendarController```. This Controller handles the behaviour
    of the Calendar displaying the different snapshots in a calendar view. This class is found in
    the ```js/fullCalendar/zcalendarController.js```.
        + This controller handles the update of the snapshot of the instances of ```SnapshotFileDownload```,
        ```ChartManager``` and ```SnapshotTableController```.
    - Sample (13) code shows how the ```SnapshotConsoleController``` is set up, this file can be found in
    ```/js/SnapshotConsole/snapshot_console.js``` file.
        + This class has a dependency from ```DirectoryController``` and ```CalendarController```
            - ```DirectoryController``` is used in a different way, since the files to be shown have been filtered, to
            only display the files in mode Time Machine.
            - ```CalendarController``` is used to display the snapshots in a more intuitive way.
    
```Javascript
            /*Sample 1*/
            var editor = new EditorManager('containerId', 'ace/theme/xcode', 'ace/mode/sql', 'inputIdToBindWith');
            editor.initializeEditor(); //sets up the editor
            editor.bindWithInputById(); //syncs the text from editor or input, to have the same value and binds them
```

```Javascript
            /*Sample 2*/
            var editFormManager = new FormManager('formId', '/url-to-PUT', 'PUT', $.ajax, '/url-to-redirect-on-success');
            editFormManager.bindFormWithService(); //bind the passed service ($.ajax) to the form
```

```Javascript
            /*Sample 3*/
            var communicator = new CommunicatorService($.ajax, "formIdToGetDataFrom", "/url-to-POST", "modalId"); //the service will use ajax to fetch the data
            //using the code editor from the first sample
            editor.bindWithVariable(communicator);
            editor.setUpTestRunCommand(communicator); //(Optional) When Ace is focus/active Ctrl+r will execute the "Test Run"
            communicator.bindWithButton('buttonId'); //button to use when pressed
            communicator.bindInputForLimitById('inputLimitId'); //the limit of rows
            communicator.bindInputForConnectionById('inputConnectionId'); //input that holds the connection to use
```

```Javascript
            /*Sample 4*/
            var callbacks = {
                onDone: function (data, b, c) {
                    //do something else with the response
                },
                onFail: function(b, c) {
                    //complain or check your connection
                }
            };
            //the service ($.ajax in this case) uses the deferred.then(...); interface
            var registerFormManager = new FormManager('formId', '/url-to-post', 'POST', $.ajax, callbacks);
            registerFormManager.bindFormWithService();
            //or it can be omitted, passing the callbacks in the constructor, and pass the callbacks in the binding
            registerFormManager.bindFormWithService(callbacks);
```

```Javascript
            /* Sample 5 */
            var chart = new ChartManager(
                'graphContainerId', //container Id in which the chart will be rendered
                'Chart Title',
                'Chart description',
                $.ajax,
                45, //queryRequest Id
                'spline', //chart type
                '/url-to-request-get', //url to perform the Get method and fetch the results, by default is /chartData
                'snapshotUsedContainerId' //this container is used to present the snapshot used in a friendly format way
                );
            chart.initializeChart(); //calling this function will fetch data from server and build the chart
            // In this way the ChartManager can change of chart type on the fly and just calling initializeChart function
            //will update the chart with the corresponding snapshot, if applies
            chart.setChartType('area');
            chart.initializeChart(); //chart renders with results from the selected snapshot
```

```Javascript
    /* Sample 6 */
    
    var options = {
        modal: {
            id: 'modalId' //used in ModalController
        },
        loading: {
            id: 'loadingIconId' //used in the LoadingIconController
        }
    };
    
    var snapshotDownload = new SnapshotFileDownload(
        'buttonId', //this button is the one that triggers the file download of the selected snapshot
        $.ajax, //in same concept as previous classes
        '/base-api-url', //see UrlFetcher class description!
        24, //queryId
        options //options holding the modal and loading descriptions
    );
    snapshotDownload.init(); //first fetches the urls, when succeded sets the button for use
    snapshotDownload.setSnapshot(snapshotId); //sets the snapshot to be use to query the file download
    
```

```Javascript
    /* Sample 7 */
    var deleteQueryController = new DeleteController(
        2, //query Id
        'buttonId', //button to bind the action with
        $.ajax,
        '/base-api-url', //see UrlFetcher
        options //
    );
    deleteQueryController.initialize();
```

```Javascript
    /* Sample 8 */
    var tableController = new TableController(
        "tableId", //table Id, only the skeleton of a table
        "formId", //form that contains the information
        "selectIdForConnection", //select which holds the connection value to use for fetching the schema information
        $.ajax,
        "/base-aip-url", //fetches the urls from the server
        "modalId" //uses ModalController
    );
    tableController.setEditor(editor); //from code Sample 1
    //setEditor(editor) functions binds the schemas column table and the editor, so clicking in the table will add the
    //clicked information into the editor
    tableController.init("dynamicTableIdToUseInPopUp"); //the id of the table to use when showing the table information
    //in the modal
```

```Javascript
    /* Sample 9 */
    var dirController = new DirectoryController(
        'directoryContainerId', //div that will contain the directory browser
        'pathContainer', //the path container, this will display the path of the current view
        'dirForm', //binds a form to register directories inside the current active directory, is the form Id
        'modalId', //uses ModalController
        'loadingIconId', //uses LoadingIcon Class
        $.ajax,
        '/base-API-url'
    );
    dirController.setInputFromForm("myForm"); //binds the Query form input with the current directory
    dirController.init(); //initializes the directory browser
```

```Javascript
    /* Sample 10 */
    var cronController = new CronController(
        "formId", //Query form to attach to
        "nameOfTheInput", //name of the input that will hold the value of the cron expression
        "modalId", //this controller uses ModalController
    );
    cronController.init("stateInputId"); //since the cron is only relevant to the user when the mode Type is either
    //Time Machine mode or Cache mode, the stateInput represents the input that holds the value of the mode.
```

```Javascript
    /* Sample 11 */
    var resultsTableController = new SnapshotTableController(
        'tableId', //table skeleton Id for displaying the table with the results
        '/base-api-url', //See UrlFetcher
        $.ajax, 
        '2', //query Id
        {
            loadingIconId: "loadingIconId", //uses LoadingIcon
            modalId: "modalId" //uses ModalController
        }
    );
    
    resultsTableController.init();
    
```

```Javascript

    /* Sample 12 */

    var configuration = {
        "modal": {
            "id:"modalId"
        }, "loading": {
            "id": "loadingIconId"
        }, "queryId": 2,
        "download": {
            "button": {
                "id": "buttonId"
            }
        }
    };

    var calendarController = new CalendarController(
        'containerId', //the id of a div that holds the calendar
        '/url-base-api',
        $.ajax,
        configuration
    );
    
    calendarController.setChart(chart); //from sample 5
    calendarController.setTable(resultsTableController); //from sample 11
    calendarController.init();
    
```

```Javascript
    
    /* Sample 13 */
    var options = {
        modal: {
            "id": "modal"
        },
        loading: {
            "id": "homeBtn"
        },
        calendar: {
            "id": "snapshotCalendar"
        },
        directory: {
            "id": "directoryContent"
        },
        path: {
            "id": "pathContainer"
        },
        download: {
            button: {
                "id": undefined
            }
        }
    };

    var consoleController = new SnapshotConsoleController(
        "{{ url('qcharts.api.urls') }}",
        $.ajax,
        options
    );
    consoleController.init();
    
```

TODO's
======
- Code a better alternative in the ```/main/main.js``` script.