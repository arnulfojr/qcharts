QCharts
=============

QCharts is a powerful visual aid and a very handy tool to use when querying a lot
of data from a database and sharing them between your team.

How to quick-install
==============
- Require QCharts via composer
```
composer require arnulfosolis/qcharts @dev
```

- Installing QCharts using composer is pretty simple
    + Alternate installation would be to add directly QCharts to a target project.
```
composer install
```

- Add the Bundles to the ```AppKernel```.
    + ```QCharts\CoreBundle\CoreBundle```
    + ```QCharts\FrontendBundle\FrontendBundle```
    + ```QCharts\ApiBundle\ApiBundle```
        - If API Documentation is required, use ```QCharts\DevApiBundle\DevApiBundle```
         instead.

- QCharts needs some configuration in the target project prior to the execution,
please refer to the ```CONFIG_README.md``` file under ```QCharts``` directory for a more in-depth installation guide.
    + A simple configuration would be:
```yml
#app/config/config.yml
#A minimum configuration of QCharts
qcharts:
    urls:
    limits:
    paths:
    roles:
    charts:
```

- Resolve QChart's User Interface on ```orm```'s definition to your User Class.
    + Refer to ```CONFIG_README.md``` file for further details.
    + ```resolve_target_entities```

- QCharts requires access to your target's database, since QCharts requires persisting information, so:
```
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
```

- Since the project comes included with a web application, QCharts requires Assetic files to be dumped.
    + You have to register QChart's ```FrontendBundle``` in Assetic's Configuration. 
```
php app/console assetic:dump
```

How to use
==========
- QCharts comes with three bundles.
    + The ```CoreBundle```, which comes with all the important services needed to get your data formatted.
    + The ```ApiBundle``` that handles the QChart's API.
        - When using the ```DevApiBundle```, you'll benefit of Nelmio's
        ApiDoc implementation (read DevApiBundle README for more information)
        under the route ```/api/doc```.
    + And the client application laying in the ```FrontendBundle```.
- QCharts also comes with a client frontend application that comes with some QChart's tool management.
- To start using it, login using the targeted project security measures, and start going through all the list
of Requested Queries the developers have submitted.
- To request a Query to be charted, you can go directly to ```/query/register```.
    + Notice: The user has to hold the mapped role ```admin```.
        - For more information about setting up the user roles, consult the ```CONFIG_README.md``` file in the source
        directory of QCharts. 
- QCharts uses the first column in the table of the results from the requested Query to represent the X-Axis
of the graph.
- Have fun!

Future development
==================
- In time machine, compare snapshots
- Reduce unnecessary snapshot files with redundant data.

Contact
=======
- Arnulfo Solis
- E-Mail: arnulfojr@kuzzy.com
- Twitter: @arnulfojr