<?php
namespace DoctrineExtensions;

use DoctrineExtensions\ORM\Repository\SubclassRepositoryFactory;

return array(
    'controller_plugins' => array(
        'invokables' => array(
            'entityManagerProvider' => '\DoctrineExtensions\Controller\Plugin\EntityManagerProviderPlugin',
            'authenticatedUserProvider' => '\DoctrineExtensions\Controller\Plugin\AuthenticatedUserProviderPlugin',
            'initForm' => '\DoctrineExtensions\Controller\Plugin\InitFormPlugin'
        )
    ),


    'doctrine' => array (
        'configuration' => array (
            'orm_default' => array (
                'customHydrationModes' => array (
                    'column' => '\DoctrineExtensions\Hydrator\ColumnHydrator'
                ),

                'types' => array(
                    'utc_datetime' => '\DoctrineExtensions\DBAL\Types\UTCDateTimeType'
                ),

                'repositoryFactory' => '\DoctrineExtensions\ORM\SubclassRepositoryFactory',
            ),
        )
    ),

    'service_manager' => array(
        'invokables' => array(
            'DoctrineExtensions\ORM\SubclassRepositoryFactory' => 'DoctrineExtensions\ORM\Repository\SubclassRepositoryFactory'
        )
    ),
);