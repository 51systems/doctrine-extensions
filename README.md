# doctrine-extensions
[![Build Status](https://travis-ci.org/51systems/doctrine-extensions-module.svg?branch=master)](https://travis-ci.org/51systems/doctrine-extensions-module)



Collection of extensions to the doctrine2 ORM

## Features:
- Controller Plugins
  - EntityManagerProvider (entityManagerProvider) - Returns the orm_default entity manager
  - AuthenticatedUserProvider (authenticatedUserProvider) - Gets the currently authenticated user (if any)
  - InitFormPlugin (initForm) - Initializes forms that may need the entity manager. Also sets up a doctrine object hydrator.
- DataFixtures
  - AddIfNotPresentTrait - Helper trait to make it easy to only add fixture entities if they aren't already present in the database
- Types
  - UTCDateTime (`utc_datetime`) type as per [Doctrine Cookbook](http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/cookbook/working-with-datetime.html). Be careful when querying from this
- Gedmo Extensions
  - Timestampable
    - Extends Timestampable extension to support UTCDateTime (`utc_datetime`). `DoctrineExtensions\Gedmo\Timestampable\TimestampableListener` should be used in place of `Gedmo\Timestampable\TimestampableListener` in config files
- Hydrators
  - Single Column Hydrator
- ORM
  - Repositories
    - SubclassRepositoryFactory that will return a repo that uses the most defined repo definition in the class hierarchy
  - Traits
    - UTCTimestampableEntity - [Timestampable](https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/timestampable.md)
    behaviour using UTCDateTime
    - EntityManagerAwareTrait - Provides getters/setters for object to hold and instance of an EntityManager. 
    

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

## Testing
Some of the unit tests depend on test classes from other modules. Use `--prefer-source` when installing composer to run them.