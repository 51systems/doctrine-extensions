<?php

namespace DoctrineExtensions\DataFixtures;


use BadMethodCallException;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Trait applied to fixtures to make it easy to add an entity to the database only if it does not exist.
 *
 * This trait should be applied to classes inheriting from {@link ReferenceRepository} as they already
 * implement all the abstract methods.
 *
 */
trait AddIfNotPresentTrait
{

    /**
     * Set the reference entry identified by $name
     * and referenced to managed $object. If $name
     * already is set, it throws a
     * BadMethodCallException exception
     *
     * @param string $name
     * @param object $object - managed object
     * @see Doctrine\Common\DataFixtures\ReferenceRepository::addReference
     * @throws BadMethodCallException - if repository already has
     *      a reference by $name
     * @return void
     */
    abstract function addReference($name, $object);

    /**
     * Loads an object using stored reference
     * named by $name
     *
     * @param string $name
     * @see Doctrine\Common\DataFixtures\ReferenceRepository::getReference
     * @return object
     */
    abstract function getReference($name);

    /**
     * Check if an object is stored using reference
     * named by $name
     *
     * @param string $name
     * @return boolean
     */
    abstract function hasReference($name);

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

        if (!is_null($existingEntity)) {
            $this->safeAddReference($referencePrefix . '-' . reset($criteria), $existingEntity);
            return $existingEntity;
        }

        $manager->persist($entity);
        $this->safeAddReference($referencePrefix . '-' . reset($criteria), $entity);
        return $entity;
    }

    /**
     * Safely adds the reference.
     *
     * Checks to see if the reference already exists, and if it does, ensures
     * that it matches the object.
     *
     * @param string $name
     * @param object $object - managed object
     */
    protected function safeAddReference($name, $object)
    {
        if (!$this->hasReference($name)) {
            $this->addReference($name, $object);
            return;
        }

        $existing = $this->getReference($name);

        if ($existing !== $object) {
            throw new \UnexpectedValueException(
                sprintf("New value does not match existing %s reference value.\n".
                "Cannot reset reference value one it has been set",
                    $name));
        }
    }
}