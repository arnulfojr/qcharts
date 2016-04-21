- QCharts Overall Documentation -
====================

- What is it?
    + QCharts is a powerful and very handy tool to use when querying a lot
    of data from a database, aiding the user in a more graphical point of view.

- What is QCharts aimed for?
    + QCharts aims to help build a concrete, simple but yet effective
    visual representation of the queried data/results.

- Backend technologies involved
	+ QCharts is developed in and for PHP (5.5) using Symfony LTS (v.2.7) as framework.
	+ For more detailed information according to the project's dependencies feel free
	to check out the composer json file, located in the root directory.
	+ As for the frontend side, in brief it will be discussed.

-
	
	+ Bundles Developed +
	The following Bundles build QCharts, the core of the project, shortly it will be provided a
	brief but concrete description of each bundle:
		* ApiBundle
		    *DevApiBundle*
		* FrontendBundle
		* CoreBundle
	+ Bundles From Symfony +
	The following bundles were used:
		* Doctrine
			* Doctrine Migrations
		* Monolog
		* StopWatch
		* Assetic
	+ 3rd-Party Bundles/Library involved +
	The following bundles were involved for the development, for more information about this bundles
	please refer to their own webpage, a link is provided here as a guide:
		* FOS/UserBundle
			$ https://github.com/FriendsOfSymfony/FOSUserBundle
		* SqlFormatter
			$ https://github.com/jdorn/sql-formatter
		* Cron-Expression:
		    $ https://github.com/mtdowling/cron-expression

- Frontend technologies involved
	+ The technologies involved in QCharts, as for the client side (frontend) are pure 
	JavaScript and a bit of jQuery to sweet or bitter the life, 
	depending on your point of view, and simple CSS since Bootstrap is being used the project 
	didn't required a lot of css of my own hand.
	+ The files which are rendered with twig, you'll find them in 
	the ```/Resources/views``` directory ```@FrontendBundle```, which are basically all the ```.html.twig``` files.
	+ As for the CSS and JS, since the architecture of the project
	itself it's divided in three parts, as you'll see in the following sections.
	You can find all the JS and CSS files in the FrontendBundle, under the
	directory ```/Resources/public```.
	+ For more information on the JS and CSS libraries used, please refer to their own documentation.

-

	+ 3rd Party Libraries +
	* jQuery (2.1.4)
		$ http://api.jquery.com/
	* HighCharts
		$ http://api.highcharts.com/highcharts
	* Animate.css
		$ https://daneden.github.io/animate.css/
	* Ace
		$ https://github.com/ajaxorg/ace
	* DataTables
	    $ http://www.datatables.net/

- Directory Structure
	The structure of the project is as most of the other Symfony projects,
	following are only displayed the relevant content to the project for their own
	adaptation or modification, if needed.

-

	+ Legend: +
	- Directory
	* File
	
	 /QCharts
		-app
			-config
				* config.yml
				* parameters.yml
				* routing.yml
				* security.yml
				* services.yml
			-logs
		-src
		    - QCharts
                - ApiBundle
                    - Controller
                        * ApiController.php
                        * ApiDirectoryController.php
                        * DatabaseController.php
                        * FavoriteController.php
                        * SnapshotController.php
                        * UrlController.php
                        * UserController.php
                    - DependencyInjection
                    - Exception
                        * ExceptionMessage.php
                        * InvalidCredentialsExceptions.php
                    - Resources
                        - config
                            * routing.yml
                            - routing
                                * database.yml
                                * directory.yml
                                * favorite.yml
                                * query.yml
                                * snapshots.yml
                                * url.yml
                                * user.yml
                            * services.yml
                    * ApiBundle.php
                - CoreBundle
                    - Command
                        * QueryUpdateCommand.php
                    - DependencyInjection
                        * Configuration.php
                        * CoreExtension.php
                    - Entity
                        - User
                            * User.php
                            * QChartsSubjectInterface.php
                        * ChartConfig.php
                        * Directory.php
                        * Query.php
                        * QueryRequest.php
                    - EventSubscriber
                        * UserRegistrationSubscriber
                    - Exception
                        - Messages
                            * ExceptionMessage.php
                        * AbortedOperationException
                        * DatabaseException
                        * DirectoryNotEmptyException
                        * EmptyCallException
                        * InstanceNotFoundException
                        * NoTableNamesException
                        * NotPlotableException
                        * OffLimitsException
                        * ParameterNotPassedException
                        * OverlappingException
                        * ParameterNotPassedException
                        * SnapshotException
                        * SQLException.php
                        * TypeNotValidException
                        * UserRoleException
                        * ValidationFailedException
                        * WriteReadException
                    - Form
                        - ChartConfig
                            * ChartConfig
                        - Directory
                            * DirectoryType
                        - Query
                            * QueryForm
                        - Transformer
                            * DatabaseConnectionTransformer
                            * DirectoryTransformer
                        * QueryRequestType
                        * UserRegistrationType
                    - Repository
                        - Snapshot
                            * FinderConfigurator
                            * FinderLooper
                            * SnapshotRepository
                        * DatabaseQueries
                        * DirectoryRepository
                        * DynamicEntityManager
                        * DynamicRepository
                        * QueryRepository
                        * UserRepository
                    - Resources
                        -DoctrineMigrations
                            * Version20160216173010.php #last migration
                        - config
                            - services
                                * directory.yml
                                * favorite.yml
                                * repositories.yml
                                * snapshot.yml
                                * user.yml
                            * services.yml
                            * routing.yml
                        - views
                    - ResultsFormatter
                        * HCPieFormatter
                        * HCPolarFormatter
                        * HCUniversalFormatter
                        * OneDimensionTableformatter
                        * ResultFormatterInterface
                        * ResultsFormatterFactory
                        * ResultsFormatterFactoryInterface
                        * ResultsPrepareFormatter
                        * TableFormatter
                        * UserFormatterInterface
                    - Service
                        - Directory
                        - Favourite
                        - FetchingStrategy
                        - ServiceInterface
                            * ChartValidatorInterface
                            * QueryFormFactoryInterface
                            * QueryServiceInterface
                            * SerializationServiceInterface
                            * SerializerFactoryClass
                        - Snapshot
                            - FileSystem
                                - Saving
                                    - Saver
                                        * SaverInterface
                                        * SnapshotSaver
                                        * TimeMachineSaver
                                    * SaverFactory
                                    * SavingFactoryInterface
                                * FileReader
                                * FilesystemManager
                                * FileWriter
                                * SnapshotManager
                            * SnapshotService
                        * ChartValidation
                        * LimitsService
                        * QueryResultsFormatter
                        * QueryService
                        * QuerySyntaxService
                        * QueryValidatorService
                        * SerializationService
                        * SerializerFactory
                        * UserFormatter
                        * UserService
                    - Tests
                        - Service
                            * QueryServiceTest.php
                            * QuerySyntaxServiceTest.php
                            * QueryValidatorServiceTest.php
                        - Validation
                            - Validator
                                * ReadOnlyValidatorTest
                    - Validation
                        - ValidationInterface
                            * StringRegexInstanceClass
                            * ValidationFactoryClass
                            * ValidatorInterface
                        - Validator
                            * CronExpressionValidator
                            * ExecutionTimeConfigurationValidator
                            * ExistenceValidator
                            * NoAsteriscValidator
                            * NumericListValidator
                            * NumericValueValidator
                            * OffsetValidator
                            * PieCompabilityValidator
                            * ReadOnlyValidator
                            * RowLimitValidator
                            * SemicolonValidator
                            * TimeExecutionValidator
                            * ValidaTableNameValidator
                        * ChartValidationFactory
                        * SyntaxSemanticValidationFactory
                    * CoreBundle.php
                - FrontendBundle
                    - Controller
                        * MainController.php
                    - DependencyInjection
                        ...
                    - Resources
                        - config
                            - routing
                                * admin.yml
                                * database.yml
                                * directory.yml
                                * general.yml
                                * query.yml
                            - services
                                * twig_extensions.yml
                            * routing.yml
                            * services.yml
                        - public
                            - css
                                - about
                                    * about.css
                                - base
                                    * main.css
                                - calendarTheme
                                    * custom_calendar_theme.css
                                - login
                                    * login.css
                                - vendor
                                    - ace
                                    - animate
                                    - bootstrap
                                    - cron
                                    - dataTables
                                    - dynatable
                                    - fullCalendar
                                    - grayscale
                            - images
                            - js
                                - Browser
                                    * browser.js
                                - calendar
                                    * zcalendarController.js
                                - cron
                                    * cron_adapter.js
                                - DeleteQuery
                                    * delete.js
                                - favorite
                                    * favorite.js
                                - general
                                    * Classes.js
                                - graphComparator
                                    * graphComparator.js
                                - main
                                    * main.js
                                - SnapshotConsole
                                    * snapshot_console.js
                                - table
                                    * directoryClasses.js
                                    * tableController.js
                                    * TablesClasses.js
                                - vendor
                                * README.md
                            * README.md
                        - views
                            - blocks
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
                    - Twig
                        * FetchingModesFilter
                    * FrontendBundle.php
            * CONFIG_README.md
		-vendor
		* composer.json
		* composer.lock
		-web
			- bundles
			- css
			- js
			* app.php
			
			
- How to approach and important notes
	+ Idea
		- The concept of it is easy, user creates what it's called Query Requests,
		which are saved in database. Each Query that you requested has it's own chart
		configuration and it's own Query (as well as its own HTML formatting saved)

	+ Frontend and Backend
		- ```ApiBundle``` as the bridge of communication of both sides.
		- However there are some aspects like displaying the non-changing information with Twig.

	+ Type of users
		- The applications makes a difference in the level of allowance of the different roles given to the user,
		as in the ```FOS/UserBundle``` is specified.
		- The different type of roles are:
			- ```ROLE_USER```
				+ This role only holds the authorization to view the Requested Query,
				which involves the Query itself, the details, the resulting Chart
				and the resulting table from the results.
			- ```ROLE_ADMIN```
				+ This role holds the authorization from ```ROLE_USER``` and as well register,
				edit and delete the Query Requests.
			- ```ROLE_SUPER_ADMIN```
			    + This role holds the authorization from ```ROLE_ADMIN``` and is able to promote and
			    demote users to ```ROLE_ADMIN``` and ```ROLE_USER``` respectively.

	+ Limitations
		+ When a user is registered it holds only ```ROLE_USER```, hence the user can't
		interact at all with the application.
		+ Currently the results from the Query are rendered by Twig and loaded by DataTables, which are also limited
		to the default row limit in the parameters.
			+ This only affects the visualization of the table, not the chart data or any other.
			+ Twig is only used for the displaying the results on the main Query view.
		+ The limit for selecting rows from the database through the ```SELECT``` statement is limited by default
		by the value assigned in the configuration file, this value can be changed to the necessary requirements.
		+ As well there exists a maximum duration that limits the query's execution,
		by default the execution duration is defined in the config file in a measure of seconds.
			+ If the installed version of mySQL database is greater than ```5.7.4 (>= 5.7.4)``` then
			the max time limit changes from the bundle's default to the current value stored in the system variable
			from the database.
			+ This values are set in the ```5.7.4``` version as ```max_statement_time``` and after ```5.7.6``` is
			set as ```max_execution_time``` from the session variables.
			+ For more information about this variable please refer to mySQL site, as there exists some small letters
			in the contract in reference to the ```SELECT``` statements.
				+ If the version matches and the variables are exists but the variables are not set, in other words
				the system variables are set to ```0``` (default value), then this value is ignored.
				+ http://dev.mysql.com/doc/refman/5.7/en/server-system-variables.html#sysvar_max_statement_time
		+ The Ace Code editor is set by the Form from the server, the only thing required is
		to initialize the editor by JS.
		+ The ```CommunicatorService``` JS class requires a table containing a table with id ```resultsTable```


- Frontend development
	+ Helpers
		- In the JavaScript called ```Classes.js``` there's a couple of Objects defined
		that make the submission of forms easier, bind the Code Editor with the Form, communicate with
		the server to run test the query.
			+ For the Ace Code Editor
				* ```EditorManager``` helps you to bind the Ace code editor to an input or to a variable.
				This does all the heavy lifting for you, as it also initializes the editor and sets all what is required.
			+ For the form submission, whether if it is a Edit Form, Register Form or Delete Form,
			you can use the ```FormManager``` class to easily submit it without any concern, since they go via
			an ajax call.
			+ For fetching the results from the "Test Run" of the Query, you can use the class ```CommunicatorService```.
			With this "service" you can easily bind the limit input, bind the variable and bind the button that will
			execute the call. This service will get and print the results in the table.
			+ For fetching the data for the chart and setting up everything for the Chart you have a helper
			called ```ChartManager```, this will set everything for you when you initialize the chart.
	+ Requirements
	    - For the proper functionality of the following files should be linked to the ```HTML``` file:
	        + jQuery
	        + Bootstrap
	            - CSS
	            - JS
	        + Highcharts.js
	        + DataTables.js ```adatatables.min.js```
	        + ```Classes.js```
	        + ```databasetables.js``` // TODO: edit this
	    - For the extras:
	        + ```highChartsTheme.js```
	        + Animate.css
	    - The ```CommunicatorService``` JS class requires a table with id ```resultsTable```

- Backend Development
    + Bundles Included
        - The following bundles are available (for more details of each Bundle please found attached a README
        file in each Bundle's root folder):
            - ApiBundle
            - CoreBundle
            - FrontendBundle
    + Database configuration:
        - The bundle itself has a default database configured, in order to match your database please
        modify the Bundle's ```app/config/parameters.yml``` file to your needs
    + Query Limits:
        - The limits are defined in the configuration file. See CoreBundle's README for more information.
    + Overall Bundle's essential configuration and structure
        - Routing
            - In the directory ```/Resources/config``` of each Bundle, the routing is defined.
                + ApiBundle Routing, defined in ```ApiBundle/Resources/config/routing.yml``` and
                Actions are found in ```ApiBundle/Controller/ApiController.php```:
                + For more information about the Api Parameters of the request, you can find annotations in the
                ```ApiController.php``` over each ```Action``` and under the dev environment the ```/api/doc```
                route is enabled.
                    - ```api_query_*```
                        - Path: ```/api/query```
                        - Methods: ```[ POST, GET, DELETE, PUT ]```
                        - Actions:
                            + ```POST```: ```registerAction```
                            + ```GET```: ```getAction```
                            + ```PUT```: ```editAction```
                            + ```DELETE```: ```deleteAction```
                        - Notes:
                            - The methods have different Actions
                            - The different methods represent the different actions to modify, delete or create the Query Request
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
                + CoreBundle Routing, defined in ```CoreBundle/Resources/config/routing.yml```:
                + FrontendBundle Routing, defined in ```FrontendBundle/Resources/config/routing.yml``` and Actions are
                defined in ```FrontendBundle/Controller/FrontendController.php```, it's worth making the mention
                that the ```FrontendController``` is declared as a service:
                    - ```homepage```
                        - Path: ```/```
                        - Methods: ```[ GET ]```
                        - Action:
                            + ```GET```: ```mainAction```
                    - ```about```
                        - Path: ```/about```
                        - Methods: ```[ GET ]```
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
        - Services
            - Services are imported in the ```app/config``` directory.
                - ```CoreBundle\Service``` namespace, more information can be found in the Bundle's directory.
                    - ```qcharts.form_entity.adapter```
                        * This services consists on the adaptation between the
                        Form and the QueryRequest, Query and ChartConfig entities.
                    - ```qcharts.query_repo```
                        * The repository used for managing the queries for this Bundle.
                    - ```qcharts.user_repo```
                        * This repository is used for the managing of the users by this bundle.
                    - ```qcharts.query_form_factory```
                        * A Form factory for the editing, registration and deletion of the requested Queries.
                    - ```qcharts.query_results_formatter_factory```:
                        * This is the factory that returns the desire formatter.
                    - ```qcharts.query_results_formatter```
                        * Formats the results coming from the database for the use in a table or chart.
                        * Highchart formats supported by default, to add new chart support see the CoreBundle README:
                            + line
                            + spline
                            + area
                            + pie
                            + bar
                            + table
                    - ```qcharts.query```
                        * The service to use to interact with the querying, validation, registration and modification
                        of the requested Queries, as well provides the information of the query's execution duration.
                    - ```qcharts.query_modifier```
                        * This service is the one that handles the modification of the query before it prior execution.
                        * Adds the limits.
                    - ```qcharts.chart_validator```:
                        * This service helps with the validation of all the parameters it has
                        to deal with chart validation.
                    - ```qcharts.query_validator```
                        * Query validator concerning to query execution time, validates the query is a read-only statement,
                         and validates the table names in the query.
                         * Uses ```query.syntax``` service.
                    - ```qcharts.serializer_factory```
                        * Service in charge of configuring the the serializer and returning it ready to use
                    - ```qcharts.serializer```:
                        * This service is the one in charge of serializing the foreseen objects in json or xml.
                    - ```qcharts.core_limits```:
                        * Service that wraps the limits and holds the limits for the validators.
                    - ```qcharts.user_formatter```:
                        * Service that formats the user object and by consequence it fetches it too.
                    - ```qcharts.user_service```:
                        * Service that handles all the logic of fetching, promoting and demoting the Users.
                    - ```qcharts.user_registration_success.subscriber```:
                        * Event subscriber so it adds the ```ROLE_USER``` to new users.
                    - ```qcharts.user_registration.form```:
                        * Service that overrides the User Registration from the FOSUser Bundle
                - ```FrontendBundle\Controller``` namespace.
                    - ```frontend.main_controller```
                        * Frontend controller defined as a service.
- Contact:
    + Arnulfo Solis Ramirez
    + email: arnulfojr94@gmail.com