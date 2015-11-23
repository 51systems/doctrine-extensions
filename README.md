# doctrine-extensions


Collection of extensions to the doctrine2 ORM

## Features:
- Controller Plugins
  - EntityManagerProvider (entityManagerProvider) - Returns the orm_default entity manager
  - AuthenticatedUserProvider (authenticatedUserProvider) - Gets the currently authenticated user (if any)
  - InitFormPlugin (initForm) - Initializes forms that may need the entity manager. Also sets up a doctrine object hydrator.
- Types
  - UTCDateTime type as per [Doctrine Cookbook](http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/cookbook/working-with-datetime.html). Be careful when querying from this
- Hydrators
  - Single Column Hydrator
- SubclassRepositoryFactory that will return a repo that uses the most defined repo definition in the class hierarchy

## Skipper
To get The custom type working in skipper, you need to make a [custom configuration file](https://help.skipper18.com/expert-usage/customization/configuration-files) and include the following:
```
<ormd2-configuration xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
 <orm-configuration name="Doctrine2">
  <data-types>
   <data-type name="utc_datetime"/>
  </data-types>
 </orm-configuration>
</ormd2-configuration>
```


## Installation


Install Via composer.

Add `DoctrineExtensions` to Modules in application.config.php

## Config Setup
Copy doctrine-extensions.global.php.dist to your configuration directory and rename to doctrine-extensions.global.php.
Modify as necessary.