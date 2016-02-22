- FrontendBundle -
==================
FrontendBundle Documentation

-Directory Structure-
	The structure of the Bundle, following are only displayed
	the relevant content to the project for their own adaptation or modification, if needed.
	
	+ Legend: +
	- Directory
	* File
	
    - FrontendBundle
        - Controller
            * AdminController
            * DatabaseController
            * DirectoryController
            * MainController
            * QueryController
        - DependencyInjection
            ...
        - Twig
            * FetchingModesFilter
        - Resources
            - config
                - routing
                    * admin
                    * database
                    * directory
                    * general
                    * query
                - services
                    * twig_extensions
                * routing
                * services
            - public
                - css
                    - ace #Ace Code Editor stylesheet
                        * ace-twilight
                    - base
                        * main.css
                        * grayscale.css #Grayscale theme
                        * bootstrap.min.css
                        * bootstrap.min.css.map
                    - cron
                        * jquery-cron.css
                    - fullCalendar
                        * fullCalendar.min.css
                        * fullCalendar.print.css
                    - general
                        * animate.css
                    - login
                        * login.css
                    - table
                        * datatables.min.css
                        * fixedColumns.bootstrap.min.css
                        * fixHeader.boostrap.min.css
                        * jquery.dynatable.css
                        * responsive.bootstrap.min.css
                        * tableStyle.css
                - js
                    - ace
                        * ace.js
                        * ace.twilight.theme.js
                    - Browser
                        * browser.js
                    - cron
                        * cron_adapter.js
                        * jquery-cron.js
                    - DeleteQuery
                        * delete.js
                    - favorite
                        * favorite.js
                    - fullCalendar
                        * fullCalendar.min.js
                        * zcalendarController.js
                    - general
                        * ajquery.min.js #v 2.1.4
                        * bootstrap.min.js
                        * Classes.js
                        * grayscale.js
                    - highcharts
                        - theme
                            * highChartsTheme.js
                        * ahighcharts.js
                        * hcExporting.min.js
                        * hcOfflineExporting.min.js
                        * highchartsExtras.js
                    - main
                        * main.js
                    - moment
                        * moment.min.js
                    - SnapshotConsole
                        * snapshot_console.js
                    - table
                        * adatatables.min.js
                        * buttons.print.min.js
                        * dataTables.autoFill.min.js
                        * dataTables.buttons.min.js
                        * dataTables.fixedColumns.min.js
                        * dataTables.fixedHeader.min.js
                        * dataTables.responsive.min.js
                        * dataTables.select.min.js
                        * directoryClasses.js
                        * jquery.dynatable.js
                        * tableController.js
                        * TablesClasses.js
            - views
                - views
                    - about
                        * about.html.twig
                    - admin
                        * admin.html.twig
                    - directory
                        * directory.html.twig
                    - editQuery
                        * editQuery.html.twig
                    - main
                        * main.html.twig
                        * unexpectedError.html.twig
                    - registerQuery
                        * confirmed.html.twig
                        * invalidQuery.html.twig
                        * register.html.twig
                    - showQuery
                        * invalidQuery.html.twig
                        * mainView.html.twig
                    - snapshots
                        * snapshotsView.html.twig
                    - tableInfo
                        * tableInfo.html.twig
                - Form
                    * custom_form_bootstrap_ace.html.twig
                - Profile
                    * show_content.html.twig
                - Registration
                    * register_content.html.twig
                - Security
                    * login.html.twig
                * layout.html.twig
                * base.html.twig
                * showQueryBase.html.twig
        * FrontendBundle.php

- FrontendBundle Routing
    - Defined in ```FrontendBundle/Resources/config/routing/general.yml``` and Actions are 
    defined in the controller```FrontendBundle/Controller/MainController```:
        - ```qcharts.frontend.homepage```
            - Path: ```/```
            - Methods: ```[ GET ]```
            - Action:
                + ```GET```: ```mainAction```
        - ```qcharts.frontend.about```
            - Path: ```/about```
            - Methods: ``` [ GET ] ```
            - Action:
                + ```GET```: ```aboutAction```
        - ```qcharts.frontend.query_register_success```
            - Path: ```/query/register/success```
            - Methods: ```[ GET ]```
            - Action:
                + ```GET```: ```successAction```
            - Notes:
                - Renders a simple confirmation if the registration was successful.
    - Defined in ```FrontendBundle/Resources/config/routing/query.yml``` and Actions are defined in
    the controller ```FrontendBundle/Controller/QueryController```
        - ```qcharts.frontend.query_view```
            - Path: ```/query/{queryId}```
            - Requirement:
                - ```queryId``` should be numeric ```\d+```
            - Methods: ```[ GET ]```
            - Action:
                + ```GET```: ```showAction```
            - Notes:
                - Renders the requested Query information.
        - ```qcharts.frontend.query_register```
            - Path: ```/query/register```
            - Methods: ```[ GET ]```
            - Action:
                + ```GET```: ```registerAction```
            - Notes:
                - Renders the registration form
        - ```qcharts.frontend.query_edit```
            - Path: ```/query/edit/{queryId}```
            - Requirement:
                - ```queryId``` should be numeric ```\d+```
            - Methods: ```[ GET ]```
            - Action:
                + ```GET```: ```editAction```
            - Notes:
                - Renders the form to edit the requested Query.
    - Defined in ```FrontendBundle/Resources/config/routing/database.yml``` and Actions are defined in 
    the controller ```FrontendBundle/Controller/DatabaseController```
        - ```qcharts.frontend.table_information```
            - Path: ```/tableInfo```
            - Methods: ```[ GET ]```
            - Action:
                + ```GET```: ```tableInfo```
        
- Controller
    + You can find all controllers in ```FrontendBundle/Controller``` namespace.
        - ```MainController.php```
            * The Controller handles the requested routing mapped in the Routing file
             found in the Bundle's directory ```/Resources/config/routing/general.yml```
            * Actions
                - ```mainAction()```
                    - Route:
                        + ```homepage```
                    - QCharts mapped role required:
                        - ```user```
                    - File rendered:
                        - Renders ```main.html.twig``` found in the
                        bundles's ```/Resources/views/views/main``` directory
                    - Description:
                        - This action renders, if authenticated, using Twig the list of all queries.
                            + For fetching all the queries there exists also an API call.
                        - If there's no valid authentication then it returns a ```RedirectResponse```
                        to ```/login```.
                    - Return:
                        - ```Response```
                        - ```RedirectResponse```
                    - Exceptions:
                        - ```NoTableNamesException```
                            - The page is rendered without any Requested Queries from the database.
                            - This exception occurs when there are no tables found in database.
                        - ```Exception```:
                            - The page still is rendered but without any listed requested Queries,
                            as the above Exception.
                - ```successAction()```
                    - Route:
                        + ```query_register_success```
                    - File rendered:
                        - ```confirmed.html.twig``` from the ```/Resources/views/views/registerQuery``` directory.
        - ```QueryController.php```
            * The controller handles the rendering of the views related to show, edit and register Queries
                - ```showAction($queryId)```
                    - Route:
                        + ```query_view```
                    - ROLE required:
                        - ```ROLE_USER``` or above
                    - File rendered:
                        - Renders ```mainView.html.twig``` form the bundle's directory
                        ```/Resources/views/views/showQuery```.
                    - Description
                        - This action renders the requested query if authenticated, else it redirects
                        to ```/login```
                    - Parameters:
                        - ```$queryId```
                            - Type: integer
                            - default value ```null```
                    - Return Value:
                        - ```RedirectResponse```
                        - ```Response```
                    - Exceptions:
                        - ```Exception```
                - ```registerAction()```
                    - Route:
                        + ```query_register```
                    - ROLE required:
                        - ```ROLE_ADMIN```
                    - File rendered:
                        - Renders ```register.html.twig``` from the directory ```/Resources/views/views/registerQuery```.
                    - Description:
                        - This action renders the Form for registering the ```QueryRequest```.
                    - Return value:
                        - ```Response```
                        - ```RedirectResponse```
                - ```editAction($queryId)```
                    - Route:
                        + ```query_edit```
                    - ROLE required:
                        - ```ROLE_ADMIN```
                    - File rendered:
                        - Renders ```editQuery.html.twig``` from the directory ```/Resources/views/editQuery```.
                    - Description:
                        - This action renders the Form for editing or deleting the ```QueryRequest```.
                    - Parameters:
                        - ```$queryId```
                            - Type: integer
                    - Return value:
                        - ```Response```
                        - ```RedirectResponse```
- Important notice about JS and CSS files:
    + Assetic is required for the files to be rendered.
        - Register QChart's ```FrontendBundle``` in Assetic.
        - To do dump Assetic files, the command in the terminal is as follows:
            - ```php app/console assetic:dump```
        - If problems arise please clear the cache with the following command and try to dump again the Assetic:
            - ```php app/console cache:clear```
    + All JS helper classes are found in ```/Resources/public/js/...``` directory.
    + For more information about this classes please refer to the directory
    mentioned above and find the README file attached.