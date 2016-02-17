
QCharts
=============

CoreBundle Documentation
========================

- In the ```CoreBundle``` all the logic is found.
- The following services are made available from this namespace, ```CoreBundle\Service```.
    + ```qcharts.core.form_entity.adapter```
    + ```qcharts.query_repo```
    + ```qcharts.user_repo```
    + ```qcharts.query_form_factory```
    + ```qcharts.query_results_formatter_factory```
    + ```qcharts.query_results_formatter```
        - Class: ```CoreBundle\Service\QueryResultsFormatter```
        - Test: ```CoreBundle\Tests\Service\QueryResultsFormatterTest```
    + ```qcharts.query_results_formatter_factory```
    + ```qcharts.query```
        - Class: ```CoreBundle\Service\QueryService```
        - Test: ```CoreBundle\Tests\Service\QueryServiceTest```
    + ```qcharts.query_modifier```
        - Class: ```CoreBundle\Service\QuerySyntaxService```
        - Test: ```CoreBundle\Tests\Service\QuerySyntaxServiceTest```
    + ```qcharts.query_validator```
        - Class: ```CoreBundle\Service\QueryValidatorService```
        - Test: ```CoreBundle\Tests\Service\QueryValidatorServiceTest```
    + ```qcharts.chart_validator```
    + ```qcharts.serializer_factory```
    + ```qcharts.serializer```
    + ```qcharts.core_limits```
    + ```qcharts.user_formatter```
    + ```qcharts.usr_service```
    + ```qcharts.user_registration_success.subscriber```
    + ```qcharts.user_registration.form```

- For using the CoreBundle some configuration is required.
    + In the configuration file, QCharts requires some values.
        * In charts attribute the supported charts are added, this is used to several stuff, one is to show in the
        registration view. ```<key>``` this is used internally as the representation of the chart type
        and the ```<value-to-display>``` to display in the frontend interface.
```
#qcharts-core
core:
    limits:
        time: 2 #in seconds
        row: 1000 #rows
    charts: { line: Line, <key>: <value-to-display> }
```

Using the command tool
=======================
- QCharts offers a command tool for updating the Query Requests that are in Snapshot or Time Machine mode.
- The command line is ```app/console qcharts:update:snapshots```
    + The command tool offers a debug option ```-d``` flag.
- A use for this tool is to use it in conjunction with a cron job.
    + Recommended interval for calling the tool by a cron job is 1 minute.
        - Notice: this will not call update of the snapshot if the due time of the Query Request is not due!

How to use the services
=======================

- ```QueryService``` and ```QueryResultsFormatter``` are the classes Query Charter
uses the most.
    - ```QueryService``` (service name: ```qcharts.query```)
        + This service is the service Query Charter uses to get in contact to 
        the database, that is gets the information required and contacting the Repository
        ```qcharts.query_repo``` to save or delete the requested Query, this service also uses other
        ```CoreBundle``` services.
            - Relevant Methods:
                + ```getAllQueries()```
                    - returns all queries ordered by Date Created, descending sort.
                + ```getSerializedQueries($queryId = null)```
                    - Parameter:
                        + ```$queryId``` integer type or null.
                        + ```$encoding``` string type, default is 'json';
                    - Returns the serialised requested Query in the requested encoding format format.
                    - Default: returns all serialised Queries in json format.
                + ```add(Form $form, User $user)```
                    - Parameters:
                        + ```Form $form```
                            - The form containing the data, see ```qcharts.core.form_entity.adapter``` service
                            or the ```qcharts.query_form_factory```.
                        + ```User $user```
                            - The user who is doing the Query registration.
                    - Returns the Query string of the ```QueryRequest``` just added.
                    - Notes:
                        + Calling this method calls the ```flush()``` method from the Entity Manager.
                + ```edit(Form $form, User $user)```
                    - Parameters:
                        + ```Form $form```
                            - The form containing the data, see ```qcharts.core.form_entity.adapter``` service
                            or the ```qcharts.query_form_factory```.
                        + ```User $user```
                            - The user who is doing the Query modification.
                    - Returns the Requested Query Id of the ```QueryRequest``` just added.
                    - Notes:
                        + Calling this method calls the ```flush()``` method from the Entity Manager.
                + ```delete($queryId)```
                    - Parameters:
                        + ```$queryId```
                            - Integer type representing the requested query Id.
                    - Notes:
                        + Calling this method calls the ```flush()``` from the Entity Manager.
                + ```getQueryResultsOfQueryRequestId($queryRequestId)```
                    - Parameters:
                        + ```$queryRequestId```
                            - Integer type holding the Query Request Id.
                    - Notes:
                        - Calls the ```getQueryResultsOfQueryRequest($queryRequest)```.
                + ```getQueryResultsOfQueryRequest(QueryRequest $qr)```
                    - Parameters:
                        + ```QueryRequest $qr```
                            - The ```QueryRequest``` from which will get the Query 
                            to fetch the results from.
                    - Notes:
                        + Validates the query preparation and execution.
                + ```getResultsFromQuery($query [, $limit])```
                    - Parameters:
                        + ```$query```
                            - Data type: ```string```
                            - The query to execute.
                        + ```$limit```
                            - Data type: ```integer```
                            - Default Value: 0
                            - Note: If default value is passed then sets the limit
                            as the defined in the configuration file.
                    - Returns the rows resulting from the query execution on the database. This call only validates
                    the validation of the query itself and not the execution of the same.
                    - Notes:
                        + Why running the query without the time execution limit?
                            - For test running the query!
                        + Limitations of the results are as follows:
                            - Rows Limitation in ```SELECT``` statement
                                + The maximum row limitation is set in the configuration file.
                                + Even if the Query Request holds a row limit, the limit in the configuration file is
                                the limit taken in consideration.
                                + The custom limitation of rows saved in the Query Request is to set a custom limit
                                inside the limits of the constant given in the configuration file.
                        - ```array```
                + ```getDurationOfLastQuery()```
                    - Return Type: ```string```
                    - Returns the duration of the last query.
                + ```getTableNames()```
                    - Returns an array containing the names of the tables in the current database.
                + ```getTableInformation($query)```
                    - Parameters:
                        + ```$query```
                            - Data type: ```ParameterBag```
                            - The ParameterBag containing the tableName to request information from.
                    - Exceptions:
                        + ```ParameterNotPassedException```: happens in case the parameter bag doesn't contain the value
                        for key (tableName)
            - Dependencies:
                + ```qcharts.query_repo```
                + ```qcharts.serializer```
                + ```qcharts.query_validator```
                + ```qcharts.core.form_entity.adapter```
    - ```QueryResultsFormatter``` (service name: ```qcharts.query_results_formatter```)
        + This service formats the database resulting data using the desired formatter.
            - ```formatResults(array $rawResults, $type)```
                + Throws a ```TypeNotValidException``` in case the passed formatter type is not valid.
                + This function sets up the ```ResultsFormatterFactory``` and gets the formatter, consequently
                formats the results with the desired formatter, if valid.
                    - Is worth mentioning that if desired the ```ResultsFormatterFactory``` implements the
                    ```ResultsFormatterFactoryInterface``` and is injected to the service ```qcharts.query_results_formatter```
                    using the service name ```qcharts.query_results_formatter_factory```.
                    - For adding new formatters this service is the one to modify, ```qcharts.query_results_formatter_factory```
                        + For this the implementation of the interface ```ResultFormatterInterface``` must be used.
                        For the same thing formatters can also use the ```ResultsPrepareFormatter``` class as a helper.
                + Default supported types:
                    - The types are taken from the configuration file, the charts registered. This means that in the
                    supported charts given in the configuration file (the key) is the value given to the service to
                    decide the formatter.
                    - The structure of the array passed usually is the one returned from the database, for an example see
                    the Sample 1 example below.
                    - For the ```ResultsPrepareFormatter```, which is a helper class for some formatters and they extend
                    from it, receives an array of structure as in sample 1 and returns an array with structure as in
                    sample 2.
```PHP
/* Sample 1 */

$raw = [
    [
        "a" => 1,
        "b" => 2
    ],
    [
        "a" => 3,
        "b" => 4
    ],
    //...
];

$formatterFactory = $this->container->get('qcharts.query_results_formatter_factory');
$formatterFactory->setFormatType('line');
$formatter = $formatterFactory->getFormatter(); //returns a HCUniversalFormatter that implements ResultFormatterInterface
$results = $formatter->formatResults($raw);

//or:
$formatterService = $this->container->get('qcharts.query_results_formatter');
$results = $formatterService->formatResults($raw, 'line');

```
```PHP
/* Sample 2 */
$raw = [
    [
        "a" => 1,
        "b" => 2
    ],
    [
        "a" => 3,
        "b" => 4
    ],
    //...
];

$formatterService = $this->container->get('qcharts.query_results_formatter');
$results = $formatterService->formatResults($raw, 'prepare');

var_dump($results);
//$results dump:
$results = [
    "a" => [
        1, 3, //...
    ],
    "b" => [
        2, 4, //...
    ]
];

```


How to add Formatters
=====================

- To add formatters QCharts needs some prior configuration.
- First you have to register the type of chart in the configuration file, since the validator checks if the chart type
is supported. If this chart type uses the same formatting than the ```HCUniversalFormatter``` has, then this is the only
step you need, since all new values will take the universal steps, unless specified.
```
core:
    limits:
        #...
    charts:
        line: Line
        spline: SpLine
        ...
        newChart: 'New added Chart'
```
- For the case that the chart requires another formatting, different than the universal one. QCharts uses a factory
for creating and setting up the formatters. You can create your own factory and inject it to the service
```qcharts.query_results_formatter``` or to the class ```ResultsFormatterFactory```, or you can only add
the formatter to the factory. For showing how to do this implementation we will add the Polar type to the chart.
    + We start by adding it to the configuration file (yml in this case, see below for example).
    + At this point we can see in the register and edit view the new chart type, for now it acts as a universal chart,
    hence we need to properly configure it.
    + First we need a proper formatter to implement, the only requirement is that the formatter implements the
     ```ResultFormatterInterface```, additionally you can extend from the ```ResultsPrepareFormatter``` if you wish.
    + In this case we have the same formatting than the Line chart, just that the HighCharts configuration
    requires the option ```polar``` in the chart attribute to be true. So we will just extend
    from the ```HCUniversalFormatter``` and override the ```getChartConfig``` function to add the ```polar``` attribute.
    Now we have a formatter called ```HCPolarFormatter```.
    + Now all we have to do is to add it to the factory, so when we pass the string ```polar_line``` we
    return the respective formatter.
    + So far the representation of the Backend is all done, all what is needed if required,
    is the further implementation in the frontend, which can be done in the JS file ```Classes.js``` under the class
    ```ChartManager``` and/or ```ChartDataFetcher```.
    
```
# config file
core:
    limits:
        #...
    charts:
        line: Line
        ...
        polar_line: Polar Line
```