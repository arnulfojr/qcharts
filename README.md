QCharts
=============

QCharts is a powerful visual aid and very handy tool to use when querying a lot
of data from a database.

How to quick-install
==============
- Installing QCharts using composer is pretty simple
    + Alternate installation would be to add directly QCharts to a target project.
```
composer install
```

- QCharts needs some configuration in the target project prior to the execution,
please refer to the ```CONFIG_README.md``` file under the ```/src``` directory from QCharts
for a more in-depth installation guide.

- QCharts requires access to database, so it requires creating it's own
tables in the database, so:
```
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
```

- Since the project comes included with an application interface,
QCharts requires that you dump the assets:
```
php app/console assetic:dump
```

How to use
==========
- QCharts comes with three bundles.
    + The ```CoreBundle``` comes with all the 
    important services needed to get your data formatted.
    + The ```ApiBundle``` that handles the Api.
    + And the application laying in the ```FrontendBundle```.
- QCharts also comes with a full frontend application that comes with an interface and user management.
- To start using it, login using the targeted project security measures,
and start going through all the list of Requested Queries the developers have submitted.
- To request a Query to be charted, you can go directly to ```/query/register```.
    + User has to hold the mapped role ```admin```.
        - For more information about setting up the user roles, consult the ```CONFIG_README.md``` file in the source
        directory of QCharts. 
- QCharts uses the first column in the table of the results
from the requested Query to represent the X-Axis of the graph.