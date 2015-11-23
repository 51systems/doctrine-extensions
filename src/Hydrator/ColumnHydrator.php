<?php

namespace DoctrineExtensions\Hydrator;


use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

/**
 * Hydrates a single scalar column.
 *
 * Sometimes doctrine returns an array of arrays, when we actually only want a single
 * column. For example, if we select a single column off of an entity, it may return
 * [[id1],[id2]]
 *
 * This hydration strategy changes the result into:
 * [id1, id2]
 */
class ColumnHydrator extends AbstractHydrator
{

    /**
     * @inheritdoc
     */
    protected function hydrateAllData()
    {
        $results = $this->_stmt->fetchAll(\PDO::FETCH_COLUMN);

        //Check to ensure that the returned results is an array of scalars
        if (!empty($results) && is_array($results[0])) {
            //For some reason the returned results isn't in the form of a scalar array, process the result set
            $scalarArray = array();
            foreach ($results as $result) {
                $scalarArray[] = array_shift($result);
            }

            return $scalarArray;
        }

        return $results;
    }
}