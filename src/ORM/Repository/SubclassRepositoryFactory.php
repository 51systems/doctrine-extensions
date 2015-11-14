<?php
namespace DoctrineExtensions\ORM\Repository;


use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Repository\RepositoryFactory;
use DoctrineExtensions\Reflection\MetadataHierarchyBuilder;

/**
 * Repository factory that will automatically determine the repository
 * of the most-derived subclass for the mapped-inheritance type.
 *
 * This has several catches though:
 *  1. If there are multiple subclass trees (not a linear inheritance chain)
 *     the default repository will be returned
 *  2. If the subclass does not have a repository associated with it, the next
 *     parent of the inheritance relationship will be returned
 *  3. If the subclass's repository doesn't inherit from the parent's defined
 *     repository, the parents repository will be returned.
 *
 *
 * @see Doctrine\ORM\Repository\DefaultRepositoryFactory
 */
class SubclassRepositoryFactory implements RepositoryFactory
{
    /**
     * @var MetadataHierarchyBuilder
     */
    protected $hierarchyBuilder;

    /**
     * The list of EntityRepository instances.
     *
     * @var \Doctrine\Common\Persistence\ObjectRepository[]
     */
    private $repositoryList = array();

    public function __construct()
    {
        $this->hierarchyBuilder = new MetadataHierarchyBuilder();
    }

    /**
     * Gets the repository for an entity class.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager The EntityManager instance.
     * @param string $entityName The name of the entity.
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $repositoryHash = $entityManager->getClassMetadata($entityName)->getName() . spl_object_hash($entityManager);

        if (isset($this->repositoryList[$repositoryHash])) {
            return $this->repositoryList[$repositoryHash];
        }

        return $this->repositoryList[$repositoryHash] = $this->createRepository($entityManager, $entityName);
    }

    /**
     * Create a new repository instance for an entity class.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager The EntityManager instance.
     * @param string                               $entityName    The name of the entity.
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function createRepository(EntityManagerInterface $entityManager, $entityName)
    {
        /* @var $originalMeta \Doctrine\ORM\Mapping\ClassMetadata */
        $originalMeta            = $entityManager->getClassMetadata($entityName);

        if (!$originalMeta->isMappedSuperclass) {
            return $this->_createRepository($entityManager, $entityName);
        }

        //we have a mapped superclass we are going to try to resolve the repository in a sane way.
        $metadataFactory = $entityManager->getMetadataFactory();
        $originalNode = $this->hierarchyBuilder->getHierarchy($metadataFactory, $entityName);

        if (!$originalNode->hasChild()) {
            //We are the most-derived child class, just return the repo
            return $this->_createRepository($entityManager, $entityName);
        }

        //Traverse down to the bottom of the hierarchy
        $currentNode = $originalNode;
        while($currentNode->hasChild()) {
            if ($currentNode->hasMultipleChildren()) {
                break;
            }

            $currentNode = $currentNode->getFirstChild();
        }

        //Start traversing back up
        while($currentNode != $originalNode && $currentNode->hasParent()) {
            $currentMeta = $entityManager->getClassMetadata($currentNode->getEntityName());

            if ($originalMeta->customRepositoryClassName) {
                if (!empty($currentMeta->customRepositoryClassName) && is_subclass_of($currentMeta->customRepositoryClassName, $originalMeta->customRepositoryClassName)) {
                    //The current class metadata has a custom repo set, and it is a child of
                    //the original class's repo.
                    return $this->_createRepository($entityManager, $currentNode->getEntityName());
                }
            } else {
                //The original class metadata didn't specify a custom repo. Simply return
                //the most derived custom repo we come across

                if (!empty($currentMeta->customRepositoryClassName)) {
                    return $this->_createRepository($entityManager, $currentNode->getEntityName());
                }
            }
        }

        //If we made it this far without returning, it means that either there were no custom repos
        //set for any subclasses, or they didnt inherit from the original entities repo.
        //If this is the case, we must return the original entities repo
        return $this->_createRepository($entityManager, $entityName);

    }

    /**
     * Creates a new Entity repository.
     * @param EntityManagerInterface $entityManager
     * @param $entityName
     * @return ObjectRepository
     */
    private function _createRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $metadata = $entityManager->getClassMetadata($entityName);

        $repositoryClassName = $metadata->customRepositoryClassName
            ?: $entityManager->getConfiguration()->getDefaultRepositoryClassName();

        return new $repositoryClassName($entityManager, $metadata);
    }
}