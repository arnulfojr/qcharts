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
            * MainController.php
        - DependencyInjection
            ...
        - Resources
            - config
                * routing.yml
            - public
                - css
                    - base
                        * main.css
                    - general
                        * animate.css
                    - login
                        * login.css
                - js
                    - ace
                    - general
                        * ajquery.min.js
                        * Classes.js
                    - highChartsTheme
                        * highChartsTheme.js
                    - main
                        * main.js
            - views
                - views
                    - about
                        * about.html.twig
                    - admin
                        * admin.html.twig
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
        * FrontendBundle.php

- FrontendBundle Routing
    - defined in ```FrontendBundle/Resources/config/routing.yml``` and Actions are 
    defined in ```FrontendBundle/Controller/FrontendController.php```:
    - ```homepage```
        - Path: ```/```
        - Methods: ```[ GET ]```
        - Action:
            + ```GET```: ```mainAction```
    - ```about```
        - Path: ```/about```
        - Methods: ``` [ GET ] ```
        - Action:
            + ```GET```: ```aboutAction```
    - ```query_view```
        - Path: ```/query/{queryId}```
        - Requirement:
            - ```queryId``` should be numeric ```\d+```
        - Methods: ```[ GET ]```
        - Action:
            + ```GET```: ```showAction```
        - Notes:
            - Renders the requested Query information.
    - ```query_register```
        - Path: ```/query/register```
        - Methods: ```[ GET ]```
        - Action:
            + ```GET```: ```registerAction```
        - Notes:
            - Renders the registration form
    - ```query_edit```
        - Path: ```/query/edit/{queryId}```
        - Requirement:
            - ```queryId``` should be numeric ```\d+```
        - Methods: ```[ GET ]```
        - Action:
            + ```GET```: ```editAction```
        - Notes:
            - Renders the form to edit the requested Query.
    - ```query_register_success```
        - Path: ```/query/register/success```
        - Methods: ```[ GET ]```
        - Action:
            + ```GET```: ```successAction```
        - Notes:
            - Renders a simple confirmation if the registration was successful.


- Controller
    + You can find all controllers in ```FrontendBundle/Controller``` directory.
        - ```MainController.php```
            * The Controller handles the requested routing mapped in the Routing file
             found in the Bundle's directory ```/Resources/config/routing.yml```
            * It's worth mentioning that this Controller has been declared as a Global Service.
                - Service called: ```frontend.main_controller```
                - Since the Controller is defined as a Service, this Controller has dependency on other services:
                    + ```qcharts.service```
                    + ```security.authorization_checker```
                    + ```templating```
                    + ```routing```
                    + ```qcharts.query_form_factory```
                    + ```qcharts.query_results_formatter```
            * Actions
                - ```mainAction(Request $request)```
                    - Route:
                        + ```homepage```
                    - ROLE required:
                        - ```ROLE_USER```
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
                - ```successAction()```
                    - Route:
                        + ```query_register_success```
                    - File rendered:
                        - ```confirmed.html.twig``` from the ```/Resources/views/views/registerQuery``` directory.
- Important notice about JS and CSS files:
    + Assetic is required for the files to be rendered.
        - To do this, the command in the terminal is as follows:
            - ```php app/console assetic:dump```
        - If problems arise please clear the cache with the following command and try to dump again the Assetic:
            - ```php app/console cache:clear```
    + All JS helper classes are found in the ```Classes.js``` and in ```databasetables.js``` // TODO: edit here
    file found in the ```/Resources/public/js/...``` directory.
    + For more information about this classes please refer to the
    directory mentioned above and find the README file attached.