<?php

return array(
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