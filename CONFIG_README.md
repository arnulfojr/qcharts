QCharts in-depth installation
===============================

QCharts needs some preparations before it can be actually used.
Since QCharts is base in the users you have attached in your database (default connection),
it needs some security preparations.

+ Please follow the next steps for a proper installation:
    - Install QCharts using composer ```composer install arnulfosolis/qcharts```
        + If you choose to add QCharts as a part of the project,
        then go ahead and add the QCharts directory in the target project.
    - Then, make sure to add the three QCharts Bundles in the target's AppKernel.
        + ```QCharts\ApiBundle\ApiBundle```
        + ```QCharts\FrontendBundle\FrontendBundle```
        + ```QCharts\CoreBundle\CoreBundle```
    - If you try to run or clean the cache of the target project at this point,
    it will fail since the ```QCharts\CoreBundle``` needs some configuration!
        + The following fields are required for activating the default values under the ```core``` definition in the 
        configuration file of the targeted project:
            - ```urls```: Since QCharts is independent from the target project User class, some redirect urls
            are recommended to be defined as the login and logout url (relative paths),
            under the ```redirects``` definition.
            - ```paths```: This is highly recommended to override since is the location where QCharts will store
            the files for the Caching feature.
                + Notice: the given folder should have the permissions for QCharts to write and read
                from the given directory.
            - ```limits```
                + Limits to apply to the database connection.
            - ```roles```
                + As QCharts is user independent, QCharts uses roles to grant users different parts of the application.
                + This is defined to map the current roles defined by the targeted User class.
            - ```charts```
                + Override if the target project has custom charts registered.
        + The only required fields that QCharts need are the user roles, under the ```roles``` and the
        query limits, under the ```limits``` definition.
            - For more information about the configuration,
            call the command ```app/console config:dump-reference CoreBundle```
    - If you want to check the current configuration you can call the command ```app/console debug:config CoreBundle```, 
    default values will be shown if needed.
    - Next, we are going to add the necessary fields to the current target's User Class.
        + For this, the target project needs to implement the QChartsSubjectInterface in the target User Class.
        + You can find this Interface in the ```QCharts\CoreBundle\Entity\User``` namespace.
    - Once the target User Class implements QChartsSubjectInterface, your project has to tell doctrine to resolve
    QChart's User Interface to the target User Class.
        + For this in your project's configuration, under ORM's definition of doctrine,
        you need to add the following definition: ```resolve_target_entities```
            - [Relationships with Abstract Classes and Interfaces](http://symfony.com/doc/2.7/cookbook/doctrine/resolve_target_entity.html)
        + QCharts interface can be found under ```QCharts\CoreBundle\Entity\User``` namespace.
    - By this point, the target project needs to add QCharts routing.
        + The needed routing is under the ApiBundle and FrontendBundle.
            - The ApiBundle routing, as well as the FrontendBundle routing, can be found in the ```routing.yml``` file.
        + Tip: add a prefix for the routing.
    - Now dump Assetic files with the following command: ```app/console assetic:dump```
        + Make sure that the Bundles are registered in the Assetic definition in the config file of the
        target project, QCharts only needs FrontendBundle to be registered.
    - Update your default's connection schema.
        - ```app/console doctrine:database:create```
        - ```app/console doctrine:schema:update --force```
    - Your target project is ready to be run.

```yml
#minimum configuration under the target project's config.yml

#qcharts config:
core:
    urls:
    limits:
    paths:
    roles:
    charts:
```

+ User roles:
    - For consulting an already configured project, run the command ```app/console debug:config CoreBundle```.
    - QCharts is based in three roles, which can be mapped from the current targeted project.
        + When the targeted project is already set up, call the ```app/console debug:config CoreBundle``` to know the
        default values, calling ```app/console config:dump-reference CoreBundle``` will dump the information.
        + roles:
            - user: The user role that can see the results.
            - admin: the user role that can add/edit/delete queries.
            - super_admin: role that manages users and has access the snapshot console.
        + Tip: QCharts works better when the roles are organized in a hierarchical way. 
        + Map the wished roles to use to give your users availability to use QCharts.
        
```yml
#Full example of QChart's configurations 
#QCharts
core:
    urls:
       redirects: #custom user information urls
           login: /myLogin
           logout: /myLogout
           profile: /myProfile
    limits:
        row: 1500 #max. rows in the query's result, will take the first 1500 rows.
        time: 2 #seconds query execution limit
    paths:
        snapshots: "/my/path_to_snapshots/" #path in which the snapshots will be saved
    roles:
        user: ROLE_WATCHER #role that can only access the results
        admin: ROLE_DEVELOPER #role that can edit/register/delete Query Requests
        super_admin: ROLE_ADMIN #role that can access QChart's management tools
    charts: #filled with default charts
```