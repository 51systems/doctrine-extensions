<?php

namespace DoctrineExtensions\Hydrator;


use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

/**
 * Hydrates a single scalar column.
 * Will result in an array of strings containing the values in the column
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