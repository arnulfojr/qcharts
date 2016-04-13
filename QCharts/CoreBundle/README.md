QCharts Core
============

CoreBundle Index
================
+ Documentation
+ Config
+ Using the Command line tool
+ How to use services
+ How to add a Formatter

CoreBundle Documentation
========================

- In the ```CoreBundle``` all the logic is found.
- The following services are made available from this namespace, ```CoreBundle\Service``` which are registered
in the ```CoreBundle/Resources/config/services.yml```.
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
    + ```qcharts.user_service```
    + ```qcharts.user_registration_success.subscriber```
    + ```qcharts.user_registration.form```

CoreBundle Config
=================
- Before using CoreBundle, some configuration is required.
    + In the configuration file from the targeted project, QCharts requires some values.
        * In ```urls``` definition, lays a subcategory called ```redirects```, these asks for three relative urls.
            + ```login```: In case an unauthorized user attempts to access the client application, the QCharts will
             redirect the user to this login.
                - Default value: "/login"
            + ```logout```: QCharts offer an option to logout the user, in order to override the default value,
            define this option.
                - Default value: "/logout"
            + ```user_profile```: QCharts offers the option for the user to be redirected to their profile page.
            Define this key if you wish to override it.
                - Default value: "/profile"
        * ```limits```: This limits are applied to the Query Request. default values are used.
            + ```time```: This is the maximum allowed execution time of the query.
            + ```row```: This is the maximum amount of rows allowed to be returned and cached from the Query.
        * ```paths```: This options allows to override the default location to save the snapshots in the system,
        under the ```snapshots``` definition.
            + Requires an absolute path and permissions to write to it.
        * In charts attribute the supported charts are added, this is used to several stuff, one is to show in the
        registration view. ```<key>``` this is used internally as the representation of the chart type
        and the ```<value-to-display>``` to display in the frontend interface.
```yml
#app/config/config.yml
#QCharts full core configuration
core:
    urls:
        redirects:
            login: /someURL
            logout: /someURL
            user_profile: /someURL
    limits:
        time: 2 #seconds
        row: 1500 #rows
    paths:
        snapshots: "/path/to/snapshots"
    charts: #empty will leave the defaults, else will only use the defined ones
        <key>: <value>
        ....
```

Using the command tool
=======================
- QCharts offers a command tool for updating the Query Requests that are in Snapshot or Time Machine mode.
- The command line is ```app/console qcharts:update:snapshots```
    + The command tool offers a debug option ```-d``` flag, which will stop when an error in the connection happened or the process found an error on the validation when applying the query limits previously configured.
- The concept of this command line tool is to set a Cron Job to call this command, in this way QCharts will be able to have a fresh copy every time you set your snapshots to be updated.
    + Recommended interval for calling the tool with a Cron Job is 1 minute.
        - Notice: this will not call update of the snapshot if the Query Request is not due yet.

How to use the services
=======================

- ```QueryService``` and ```QueryResultsFormatter``` are the services QCharts uses the most.
    - ```QueryService``` (service name: ```qcharts.query```)
        + This service provides the most common Repository calls for getting Query Requests, editing, adding and 
        deleting the wished Query Request, as well, provides a way to Run a Query (string) in the given connection.
            - The query run method is only called by the API route ```qcharts.api.run_query```, and since it only offers
            basic query semantic analysis, it should NOT be used broadly, use the ```qcharts.query_validator``` service
            instead.
            - Relevant Methods:
                + ```getAllQueries()```
                    - returns all queries ordered by Date Created, descending sort.
                + ```add(Form $form, QChartsSubjectInterface $user)```
                    - Parameters:
                        + ```Form $form```
                            - The form containing the data.
                        + ```QChartsSubjectInterface $user```
                            - The user who is doing the Query registration.
                    - Returns an array containing the following:
                        + ```results```: Key to the fresh results of the Validation run of the registered Query.
                        + ```query```: The ```QueryRequest``` which was registered.
                    - Exceptions:
                        + ```OffLimitsException```: This exceptions is thrown when the passed limits are not valid,
                        in this case QCharts applies the default maximum limits and saves the Query Request,
                        the exception will hold the same data the method returns.
                        + ```ValidationFailedException```: This exception is thrown when the validation of the
                        context of the Request is not valid, not the actual execution.
                        + ```TypeNotValid``` if during the process of validating the context of the request 
                        some objects are not the expected type.
                    - Notes:
                        + Calling this method calls the ```flush()``` method from the Entity Manager of the repository.
                + ```edit(FormInterface $form, QChartsSubjectInterface $user, $queryId)```
                    - Parameters:
                        + ```FormInterface $form```
                            - The form containing the Query Request data.
                        + ```QChartsSubjectInterface $user```
                            - The user who is doing the Query modification.
                    - Returns an array containing the following:
                        + ```query```: The ```QueryRequest``` object it was modified.
                        + ```results```: The results of the query validation run.
                    - Exceptions:
                        + ```OffLimitsException```: This exception is thrown when the passed limits are not
                        valid, and as in the ```add(...)``` function, sets the maximum default values.
                        + ```ValidationFailedException```: This exception is thrown when the validation of the 
                        context of the Request is not valid, not the actual execution.
                    - Notes:
                        + Calling this method calls the ```flush()``` method from the Entity Manager.
                + ```delete($queryId)```
                    - Parameters:
                        + ```$queryId```
                            - Integer type representing the requested query Id.
                    - Exceptions:
                        + ```InstanceNotFoundException``` is thrown when the ```QueryRequest``` was not found using the
                        passed parameter.
                        + ```ParameterNotPassed```: is thrown when the parameter was absent.
                    - Notes:
                        + Calling this method calls the ```flush()``` from the Entity Manager.
                + ```getResultsFromQuery($query [, $limit, $connection])```
                    - Parameters:
                        + ```$query```
                            - Data type: ```string```
                            - The query to execute.
                        + ```$limit```
                            - Data type: ```integer```
                            - Default Value: 0
                            - Note: If default value is passed then sets the limit
                            as the defined in the configuration file.
                        + ```$connection```
                            - Data type: ```string```
                            - Default value: default
                            - Note: The connection the query will be ran in.
                    - Returns an array:
                        + ```results```: The results of the query.
                        + ```duration```: The time it took to run the query.
                    - Notes:
                        + Why running the query without the time execution limit?
                            - For test running the query!
                            - IMPORTANT: this is function is only called in the Test Run,
                            in the ```qcharts.api.run_query``` route. 
                        + Limitations of the results are as follows:
                            - Rows Limitation in ```SELECT``` statement
                                + The maximum row limitation is set in the configuration file.
                                + Even if the Query Request holds a row limit, the limit in the configuration file is
                                the limit taken in consideration.
                                + The custom limitation of rows saved in the Query Request is to set a custom limit
                                inside the limits of the constant given in the configuration file.
                + ```getTableNames()```
                    - Returns an array containing the names of the tables in the current database.
                    - Parameters:
                        + ```$schemaName```: The schema name from which to fetch the names of the table.
                        + ```$connectionName```: The connection name from which to fetch the information.
                + ```getTableInformation(ParameterBag $parameterBag)```
                    - Parameters:
                        + ```$parameterBag```
                            - Data type: ```ParameterBag```
                            - The ParameterBag containing the tableName to request information from.
                    - Exceptions:
                        + ```ParameterNotPassedException```: happens in case the parameter bag doesn't contain the value
                        for key (tableName)
            - Dependencies:
                + ```qcharts.query_repo```
                + ```qcharts.dynamic_repo```
                + ```qcharts.query_validator```
    - ```StrategyFactory```, service name: ```qcharts.core.fetching_factory```.
        + This service is in charge of returning the right Strategy to use for fetching the data of the ```QueryRequest```
        since QCharts handles 3 types of fetching data (2 basically).
            - Live mode
            - Snapshot mode/Time Machine Mode (Cached)
            - Interface: ```FetchingStrategyInterface```
    - ```QueryResultsFormatter``` (service name: ```qcharts.query_results_formatter```)
        + This service formats the database resulting data using the desired formatter.
            - ```formatResults(array $rawResults, $type)```
                + Throws a ```TypeNotValidException``` in case the passed formatter type is not valid.
                + This function sets up the ```ResultsFormatterFactory``` and gets the formatter, consequently
                formats the results with the desired formatter, if valid.
                    - Is worth mentioning that the ```ResultsFormatterFactory``` implements the
                    ```ResultsFormatterFactoryInterface``` and is injected to the service ```qcharts.query_results_formatter```
                    using the service name ```qcharts.query_results_formatter_factory```.
                    - For adding a new formatter this service is the one to modify, ```qcharts.query_results_formatter_factory```
                        + For this the implementation of the interface ```ResultFormatterInterface``` must be used on
                        the desired Formatter.
                        + For the same thing the formatter can also use the ```ResultsPrepareFormatter``` class as a helper.
                + Default supported types:
                    - The types are taken from the configuration file, the charts registered or the default ones.
                    This means that in the supported charts given in the configuration file (the key)
                    is the value given to the service to decide the formatter.
                    - The structure of the array passed usually is the one returned from the database,
                    for an example see the Sample 1 example below.
                    - For the ```ResultsPrepareFormatter```, which is a helper class for some formatters and they extend
                    from it, receives an array of structure as in sample 1 and returns an array with structure as in
                    sample 2.
```PHP
<?php

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
<?php

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
/* $results dump:
$results = [
    "a" => [
        1, 3, //...
    ],
    "b" => [
        2, 4, //...
    ]
];
*/

```


How to add a Formatter
=====================

- To add a formatter QCharts needs some prior configuration.
- First you have to register the type of chart in the configuration file, since the validator checks if the chart type
is supported. If this chart type uses the same formatting than the ```HCUniversalFormatter``` has, then this is the only
step you need, since all new values will take the universal steps, unless specified.

```yml
core:
    ...
    limits:
        #...
    charts:
        line: Line
        spline: SpLine
        ...
        newChart: 'New added Chart'
```

- For the case that the new chart requires another formatting, different than the universal one. QCharts uses a factory
to create and setting up the formatters. You can create your own factory and inject it to the service
```qcharts.query_results_formatter``` or to the class ```ResultsFormatterFactory```, or you can only add
the formatter to the factory. For showing how to do this implementation we will add the Polar type to the chart.
    + We start by adding it to the configuration file (yml in this case, see below for example).
    + At this point we can see in the register and edit view the new chart type, for now it acts as a universal chart,
    hence we need to properly configure it.
    + First we need a proper formatter to implement, the only requirement is that the formatter implements the
     ```ResultFormatterInterface```, additionally you can extend from the ```ResultsPrepareFormatter``` if it requires to.
    + In this case we have the same formatting than the Line chart, just that the HighCharts configuration
    requires the option ```polar``` in the chart attribute to be true. So we will just extend
    from the ```HCUniversalFormatter``` and override the ```getChartConfig``` function to add the ```polar``` attribute.
    Now we have a formatter called ```HCPolarFormatter```.
    + Now all we have to do is to add it to the factory, so when we pass the string ```polar_line``` we
    return the respective formatter.
    + So far the representation of the Backend is all done, all what is needed if required,
    is the further implementation in the frontend, which can be done in the JS file ```Classes.js``` under the class
    ```ChartManager``` and/or ```ChartDataFetcher```.
    
```yml
# config file
core:
    limits:
        #...
    charts:
        line: Line
        ...
        polar_line: Polar Line
```