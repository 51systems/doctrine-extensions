<?php
namespace DoctrineExtensions;

return array(
    'controller_plugins' => array(
        'factories' => array(
            'entityManagerProvider' => 'DoctrineExtensions\Controller\Plugin\EntityManagerProviderPluginFactory',
            'authenticatedUserProvider' => 'DoctrineExtensions\Controller\Plugin\AuthenticatedUserProviderPluginFactory',
            'initForm' => 'DoctrineExtensions\Controller\Plugin\InitFormPluginFactory'
        )
    ),


    'doctrine' => array (
        'configuration' => array (
            'orm_default' => array (
                'customHydrationModes' => array (
                    'column' => 'DoctrineExtensions\Hydrator\ColumnHydrator'
                ),

                'types' => array(
                    'utc_datetime' => 'DoctrineExtensions\DBAL\Types\UTCDateTimeType'
                ),

                'repositoryFactory' => 'DoctrineExtensions\ORM\SubclassRepositoryFactory',
            ),
        )
    ),

    'service_manager' => array(
        'invokables' => array(
            'DoctrineExtensions\ORM\SubclassRepositoryFactory' => 'DoctrineExtensions\ORM\Repository\SubclassRepositoryFactory'
        )
    ),
);