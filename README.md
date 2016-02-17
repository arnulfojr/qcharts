QCharts
=============

QCharts is a powerful visual aid and very handy tool to use when querying a lot
of data from a database to its analysis and team collaboration.

How to quick-install
==============
- Require QCharts with composer.
    + Alternate installation would be to add directly QCharts to a target project.
```
composer require arnulfosolis/qcharts
```

- Installing QCharts using composer is pretty simple
```
composer install
```

- QCharts needs some configuration in the target project prior to the execution.
    + For a more in-depth configuration please refer to the ```CONFIG_README.md``` file located in QCharts directory.
```yml
#app/config/config.yml

#QCharts, this will be filled by the defauls
core:
    url:
    limits:
    paths:
    roles:
    charts:
```

- QCharts requires access to your target's database, since QCharts requires creating it's own
tables in the database, so:
```
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
```

- Since the project comes included with an application interface,
QCharts requires that you dump the assets:
    + You have to register QChart's ```FrontendBundle``` in the assetic's configuration.
```
php app/console assetic:dump
```

How to use
==========
- QCharts comes with three bundles.
    + The ```CoreBundle``` comes with all the 
    important services needed to get your data formatted.
    + The ```ApiBundle``` that handles QChart's API.
    + And the client application laying in the ```FrontendBundle```.
- QCharts also comes with a full frontend application which comes with a some basic QChart's tool management.
- To start using it, login using the targeted project security measures,
and start going through all the list of the Requested Queries other developers have submitted.
- To request a Query to be charted, you can go directly to ```/query/register```.
    + Notice: The user has to hold the mapped QChart's role ```admin```.
        - For more information about setting up the user roles, consult the ```CONFIG_README.md``` file in the root
        directory of QCharts. 
- QCharts uses the first column in the table of the results
from the requested Query to represent the X-Axis of the graph.
- Have fun!
