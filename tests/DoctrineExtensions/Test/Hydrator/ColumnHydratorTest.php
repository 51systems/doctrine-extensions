<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dustin
 * Date: 10/10/13
 * Time: 7:37 PM
 * To change this template use File | Settings | File Templates.
 */

namespace DoctrineExtensions\Test\Hydrator;


use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Tests\Mocks\HydratorMockStatement;
use Doctrine\Tests\ORM\Hydration\HydrationTestCase;
use Doctrine\Tests\ORM\Performance\MockEntityManager;
use DoctrineExtensions\Hydrator\ColumnHydrator;

class ColumnHydratorTest extends HydrationTestCase
{

    public function testHydrateColumn()
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Doctrine\Tests\Models\CMS\CmsUser', 'u');
        $rsm->addFieldResult('u', 'u__name', 'name');

        // Faked result set
        $resultSet = array(
            array(
                'u__name' => 'romanb'
            ),
            array(
                'u__name' => 'jwage'
            )
        );

        $stmt = new HydratorMockStatement($resultSet);

        $hydrator = new ColumnHydrator($this->_em);
        $result = $hydrator->hydrateAll($stmt, $rsm);

        $this->assertEquals(array(
            'romanb',
            'jwage'
        ), $result);
    }
}
