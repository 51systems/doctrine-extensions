<?php

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
                )
            )
        )
    ),
);