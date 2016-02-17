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
- JS Samples
    - Sample (1) code for initializing the code Editor in either the Edit or Registration Form using ```EditorManager```, this following code will also
    syncronize the input from the form and the code editor.
    - Sample (2) code to manage the form on its submit using the ```FormManager```, this sample code will do the HTTP request with the given method.
    The ```$.ajax``` represents the service to use.
    - Sample (3) code to fetch the "Test Run" in the edit of the requested Query using the ```CommunicatorService```, this sample will fetch the results from the
    server and display them in the table, using the limit from the form and binding a Button that will trigger the fetch.
    In this sample you can also see how the ```EditorManager``` can bind also a variable.
    - Sample (4) code will show how to override the callbacks if required from the ```FormManager```.
    - Sample (5) code shows the ```ChartManager```, this class is used to unify the service of initializing the chart
    from HighCharts and the fetching of the results from the server.
```Javascript
            /*Sample 1*/
            var editor = new EditorManager('containerId', 'ace/theme/xcode', 'ace/mode/sql', 'inputIdToBindWith');
            editor.initializeEditor(); //sets up the editor
            editor.bindWithInputById(); //syncs the text from editor or input, to have the same value and binds them
```

```Javascript
            /*Sample 2*/
            var editFormManager = new FormManager('formId', '/url-to-PUT', 'PUT', $.ajax);
            editFormManager.bindFormWithService(); //bind the passed service to the form
```

```Javascript
            /*Sample 3*/
            var communicator = new CommunicatorService($.ajax); //the service will use ajax to fetch the data
            //using the code editor from the first sample
            editor.bindWithVariable(communicator);
            communicator.bindWithButton('buttonId');
            communicator.bindInputForLimitById('inputLimitId');
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
            
            var registerFormManager = new FormManager('formId', '/url-to-post', 'POST', $.ajax, callbacks);
            registerFormManager.bindFormWithService();
            //or it can be omitted, passing the callbacks in the constructor, and pass the callbacks in the binding
            registerFormManager.bindFormWithService(callbacks);
```

```Javascript
            /* Sample 5 */
            var chart = new ChartManager(
                'graphContainerId',
                'Chart Title',
                'Chart description',
                $.ajax,
                45, //queryRequest Id
                'spline', //chart type
                '/url-to-request-get' //url to perform the Get method and fetch the results, by default is /chartData
                );
            chart.initializeChart(); //calling this function will fetch data from server and build the chart
```


TODO's
======

- Code a better alternative in the ```/main/main.js``` script.