<?php
namespace DoctrineExtensions;

use DoctrineExtensions\ORM\Repository\SubclassRepositoryFactory;

return array(
    'controller_plugins' => array(
        'invokables' => array(
            'entityManagerProvider' => '\DoctrineExtensions\Controller\Plugin\EntityManagerProviderPlugin',
            'authenticatedUserProvider' => '\DoctrineExtensions\Controller\Plugin\AuthenticatedUserProviderPlugin',
        )
    ),


    'doctrine' => array (
        'configuration' => array (
            'orm_default' => array (
                'customHydrationModes' => array (
                    'column' => '\DoctrineExtensions\Hydrator\ColumnHydrator'
                ),

                'repositoryFactory' => 'DoctrineExtensions\ORM\SubclassRepositoryFactory',
            ),
        )
    ),

    'service_manager' => array(
        'factories' => array(
            'DoctrineExtensions\ORM\SubclassRepositoryFactory' => function() {
                return new SubclassRepositoryFactory();
            }
        )
    ),
);