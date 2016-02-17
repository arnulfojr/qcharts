QCharts in-depth installation
===============================

QCharts needs some preparations before it can be actually used.
Since QCharts is base in the users you have attached in your database (default connection),
it needs some security preparations.

+ Please follow the next steps for a proper installation:
    - If you choose to add QCharts as a plugin, then go ahead and add the QCharts
    directory in the target project.
    - Then, we will add the three Bundles in the target's AppKernel.
        + QCharts\ApiBundle\ApiBundle
        + QCharts\FrontendBundle\FrontendBundle
        + QCharts\CoreBundle\CoreBundle
    - If you try to run the target project at this point, it will fail since the QCharts\CoreBundle needs some configuration!
        + The following fields are required for activating the default values under the ```core``` definition in the 
        config file of the targeted project:
            - ```urls```: Since QCharts is independent from the target project User class, some redirect urls
            are recommended to be defined as the login and logout url (relative paths).
            - ```paths```: These paths are recommended to override since is the location where QCharts will store
            the files for the Caching feature.
                + Notice: the given folder should have the permissions for QCharts to write and read
                from the given directory
            - ```limits```
                + Limits to apply to the database connection.
            - ```roles```
                + As QCharts is user independent, QCharts uses roles to grant users different parts of the application.
                + This is defined to map the current roles defined by the targeted User class.
            - ```charts```
                + Override if the target project has custom charts registered.
        + The only required fields that QCharts need are the user roles, under the ```roles``` and the
        query limits, under the ```limits``` definition.
            - For more information about the configuration call
            the command ```app/console config:dump-reference CoreBundle```
    - If you want to check the configuration you can call the command ```app/console debug:config CoreBundle```, 
    default values will be shown if needed.
    - Next, we are going to add the necessary fields to the current target's User Class.
        + For this, the target project needs to implement the QChartsSubjectInterface in the target User Class.
        + You can find this Interface in the ```QCharts\CoreBundle\Entity\User``` namespace.
    - Once the target User Class implements QChartsSubjectInterface, your project has to tell doctrine to resolve
    QChart's User Interface to the target User Class.
        + For this in your project's config, under ORM's definition of doctrine,
        you need to add the following definition: ```resolve_target_entities```
        + QCharts interface can be found under ```QCharts\CoreBundle\Entity\User``` directory.
    - By this point, the target project needs to add QCharts routing.
        + The needed routing is under the ApiBundle and FrontendBundle.
            - The ApiBundle routing, as well as the FrontendBundle routing, can be found in the ```routing.yml``` file.
        + Tip: add a prefix for the routing.
    - Now dump Assetic with the following command: ```app/console assetic:dump```
        + Make sure that the Bundles are registered in the Assetic definition in the config file of the
        target project. QCharts only needs FrontendBundle to be registered.
    - Update your default's connection schema.
        - ```app/console doctrine:database:create```
        - ```app/console doctrine:schema:update --force```
    - Your target project is ready to be run.

```
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
        + Map the wished roles to use to give your users availability to use QCharts.