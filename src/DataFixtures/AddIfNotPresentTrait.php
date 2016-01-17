<?php

namespace DoctrineExtensions\DataFixtures;


use Doctrine\Common\Persistence\ObjectManager;

/**
 * Trait applied to fixtures to make it easy to add an entity to the database only if it does not exist
 */
trait AddIfNotPresentTrait
{
    abstract function addReference($name, $object);

    /**
     * Persists the provided entity to the database if it does not exist.
     *
     * Uses the specified $criteria array to check if the entity exists in the database.
     *
     * Adds a new reference by concatenating the $referencePrefix with the first criteria
     * array item. This allows the entity to be referenced from other fixtures
     *
     *
     * @param object $entity
     * @param string $criteria Criteria to use when determining if the entity exists.
     * @param string $referencePrefix
     * @param ObjectManager $manager
     * @return object
     */
    protected function _addIfNotPresent($entity, $criteria, $referencePrefix, ObjectManager $manager)
    {
        $repo = $manager->getRepository(get_class($entity));

        $existingEntity = $repo->findOneBy($criteria);

        if ($existingEntity) {
            $this->addReference($referencePrefix . '-' . reset($criteria), $existingEntity);
            return $existingEntity;
        }

        $manager->persist($entity);
        $this->addReference($referencePrefix . '-' . reset($criteria), $entity);
        return $entity;
    }
}