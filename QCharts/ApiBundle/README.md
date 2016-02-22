QCharts
=============

ApiBundle Documentation
=======================

+ QCharts offers the ApiDocumentation in the route ```/api/doc```
    - ApiDoc Bundle integrated in the dev environment.

+ ApiBundle Routing, defined in ```ApiBundle/Resources/config/routing.yml``` and
Actions are found in Controllers found in the ```QCharts/ApiBundle/Controller``` namespace:
    - ```qcharts.api.query_*```
        - Path: ```/api/query```
        - Methods: ```[ POST, GET, DELETE, PUT ]```
        - Controller: ```ApiController```
        - Actions:
            + ```POST```: ```registerAction```
            + ```GET```: ```getAction```
            + ```PUT```: ```editAction```
            + ```DELETE```: ```deleteAction```
        - Notes:
            - The methods have different Actions.
            - The different methods represent the different actions to modify, delete or create the Query Request.
    - ```qcharts.api.run_query```
        - Path: ```/api/run```
        - Methods: ```[ POST ]```
        - Controller: ```DatabaseController```
        - Action:
            + ```POST```: ```runAction```
        - Notes:
            - This returns the resulting data from the query, respecting the same limits.
    - ```qcharts.api.chart_data```
        - Path: ```/api/chartData```
        - Methods: ```[ GET ]```
        - Controller: ```DatabaseController```
        - Action:
            + ```GET```: ```chartDataAction```
        - Notes:
            - Returns the data formatted results from the Query requested.
    - ```qcharts.api.table_info```
        - Path: ```/api/tableInfoData```
        - Methods: ```[ GET ]```
        - Controller: ```DatabaseController```
        - Action: ```tableInformation```
        - Notes:
            - Returns the information of tha table
    - ```qcharts.api.tables```
        - Path: ```/api/tables```
        - Methods: ```[ GET ]```
        - Controller: ```DatabaseController```
        - Action: ```tables```
        - Notes:
            - Returns the tables available in the database
    - ```qcharts.api.user_*```
        - Path: ```/api/user/role```
        - Methods: ```[ GET, POST, DELETE ]```
        - Controller: ```UserController``` 
        - Notes:
            - ```GET```: Returns the users registered.
            - ```POST```: Promotes the user from ```USER``` to ```ADMIN```.
            - ```DELETE```: Demotes a user from ```ADMIN``` to ```USER```.
    - ```qcharts.api.urls```
        - Path: ```/api/urls```
        - Methods: ```[ GET ]```
        - Controller: ```UrlController```
        - Notes:
            - returns the urls from the api.
    - ```qcharts.api.directory_*```
        - Path: ```/api/directory```
        - Methods: ```[ GET, PUT, POST, DELETE ]```
        - Controller: ```ApiDirectoryController```
        - Notes:
            - Manages the API calls related to the Directory.
    - ```qcharts.api.directory_query```
        - Path: ```/api/directory/query```
        - Controller: ```ApiDirectoryController```
        - Action: ```queriesInDirectory```
        - Methods: ```[ GET ]```
        - Notes:
            - Returns the Queries inside the passed Directory.
    - ```qcharts.api.favorite_*```
        - Path: ```/api/favorite```
        - Controller: ```FavoriteController```
        - Methods: ```[ GET; POST, DELETE ]```
        - Notes:
            - Manages the API call for toggling the files as Favorite.
    - ```qcharts.api.snapshot.get```
        - Path: ```/api/snapshot```
        - Controller: ```SnapshotController```
        - Action: ```snapshotListAction```
        - Methods: ```[ GET ]```
        - Notes:
            - Returns the list of snapshots for the given Query.
    - ```qcharts.api.snapshot.snapshot_download```
        - Path: ```/api/snapshot/download```
        - Controller: ```SnapshotController```
        - Methods: ```[ GET ]```
        - Action: ```downloadSnapshotAction```
        - Notes:
            - Returns the file from the snapshot requested.
    - ```qcharts.api.snapshot_delete```
        - Path: ```/api/snapshot```
        - Controller: ```SnapshotController```
        - Action: ```deleteSnapshotAction```
        - Methods: ```[ DELETE ]```
+ ApiBundle Requests to Routes, for further information and sandboxing please try ```api/doc``` from Nelmio:
    - Route: ```qcharts.api.query_get```
        - Method parameters:
            + ```GET```
                + ```q```
                    - type: integer
                    - The Query Request Id to fetch the information
                    - Default value: if the parameter is absent or negative value then returns all Queries
                + ```_format```
                    - type: string
                    - The wished format to get the results on.
                    - Default value: json
                + ```tm```
                    - type: boolean
                    - Flag to return only the Time Machined ones
            + ```DELETE```
                + ```id```
                    - type: integer
                    - The Query Request Id to delete. 
            + ```[ POST, PUT ]```
                + ```id```
                    - ONLY in ```PUT``` method.
                    - type: integer
                    - Query Request Id.
                + ```query_request[title]```
                + ```query_request[description]```
                + ```query_request[config][databaseConnection]```
                    - type: string
                    - The connection to use
                + ```query_request[config][typeOfChart]```
                    - type: string
                    - The type of chart to use
                + ```query_request[query][query]```
                    - type: string
                    - Query to execute
                + ```query_request[config][queryLimit]```
                    - type: integer
                    - Query row limit
                + ```query_request[config][offset]```
                    - type: integer
                    - Offset of the Query
                + ```query_request[config][executionLimit]```
                    - type: number
                    - The time limit of the execution in seconds
                + ```query_request[config][isCached]```
                    - type: integer
                    - The mode of the Caching
                + ```query_request[cronExpression]```
                    - type: string
                    - The cron expression
                + ```query_request[directory]```
                    - type: integer
                    - The directory Id in which the query will be in.
    - Route: ```qcharts.api.run_query```
        - Method parameters:
            + ```POST```
                + ```query```
                    - Type: string
                    - The query string to test run
                + ```limit```
                    - Type: integer
                    - The limit of rows.
                    - When the value is absent then 0 is taken, then it takes the 
                    default limit number defined in the ```CoreBundle``` Bundle.
                + ```connection```
                    - Type: string
                    - Connection name to use
        - Return Values:
            + ```POST```
                - Type: ```Json Response```
                - Parameters:
                    + ```status```
                        - Type: integer
                        - Status from the server
                    + ```textStatus```
                        - Type: string
                        - Description of the response.
                    + ```originalQuery```
                        - Type: string
                        - The original query
                    + ```results```
                        - Type: Array
                        - The results from the database
                    + ```lengthResults```
                        - Type: integer
                        - The length of the results
                    + ```queryDuration```
                        - Type: string
                        - The duration that the query took to execute in seconds.
                    + ```limit```
                        - Type: integer
                        - The row limit inserted in the statement.
                    + ```isPieChartCompatible```
                        - Type: boolean
    - Route: ```qcharts.api.chart_data```
        - Method parameters:
            + ```GET```
                + ```q```
                    - The Query Request Id
                    - Type: integer
                    - Default value: 0
                + ```type```
                    - The type of formatting requested
                    - Type: string
                    - Default value: ```line```
        - Return Values:
            + ```GET```
                - Type: ```Json Response```
                - Parameters:
                    + ```status```
                        - Type: integer
                        - Status from the server
                    + ```textStatus```
                        - Type: string
                        - Description of the response.
                    + ```chartData```
                        - Type: Object
                        - NOTE: return value depends on type of of chart requested.
                    + ```duration```
                        - Type: string
                        - The duration that the query took to execute in seconds.
                    + ```snapshot```
                        - Type: string
                        - The snapshot used, in a DateTime format.