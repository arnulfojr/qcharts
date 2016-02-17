QCharts
=============

ApiBundle Documentation
=======================

+ QCharts offers the ApiDocumentation in the route ```/api/doc```
    - ApiDoc Bundle integrated in the dev environment.

+ ApiBundle Routing, defined in ```ApiBundle/Resources/config/routing.yml``` and
Actions are found in ```ApiBundle/Controller/ApiController.php```:
    - ```api_query_*```
        - Path: ```/api/query```
        - Methods: ```[ POST, GET, DELETE, PUT ]```
        - Actions:
            + ```POST```: ```registerAction```
            + ```GET```: ```getAction```
            + ```PUT```: ```editAction```
            + ```DELETE```: ```deleteAction```
        - Notes:
            - The methods have different Actions.
            - The different methods represent the different actions to modify, delete or create the Query Request.
    - ```api_run_query```
        - Path: ```/api/run```
        - Methods: ```[ POST ]```
        - Action:
            + ```POST```: ```runAction```
        - Notes:
            - This returns the resulting data from the query, respecting the same limits.
    - ```api_chart_data```
        - Path: ```/api/chartData```
        - Methods: ```[ GET ]```
        - Action:
            + ```GET```: ```chartDataAction```
        - Notes:
            - Returns the data formatted results from the Query requested.
    - ```api_table_info```
        - Path: ```/api/tableInfoData```
        - Methods: ```[ GET ]```
        - Action: ```tableInformation```
        - Notes:
            - Returns the information of tha table
    - ```api_tables```
        - Path: ```/api/tables```
        - Methods: ```[ GET ]```
        - Action: ```tables```
        - Notes:
            - Returns the tables available in the database
    - ```api_users_roles_get```
        - Path: ```/api/user/role```
        - Methods: ```[ GET, POST, DELETE ]```
        - Notes:
            - ```GET```: Returns the users registered.
            - ```POST```: Promotes the user from ```USER``` to ```ADMIN```.
            - ```DELETE```: Demotes a user from ```ADMIN``` to ```USER```.
    - ```api_url```
        - Path: ```/api/urls```
        - Methods: ```[ GET ]```
        - Notes:
            - returns the urls from the api.
+ ApiBundle Requests to Routes:
    - Route: ```api_query_*```
        - Method parameters:
            + ```GET```
                + ```q```
                    - type: integer
                    - The Query Request Id to fetch the information
                    - Default value: if the parameter is absent or negative value then returns all Queries
                + ```_format```
                    - type: string
                    - The 
    - Route: ```api_run_query```
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
    - Route: ```api_chart_data```
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