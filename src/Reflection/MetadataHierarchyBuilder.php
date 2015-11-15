<?php

namespace DoctrineExtensions\Reflection;

use Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadata;
use Zend\Stdlib\SplStack;

/**
 * Builds a class hierarchy for an entity.
 */
class MetadataHierarchyBuilder
{
    const CACHE_KEY = '__class_hierarchy__';

    /**
     * @var MetadataNode[]
     */
    protected $hierarchy;

    /**
     * @param ClassMetadataFactory $metadata
     * @param $entityName
     * @return MetadataNode
     */
    public function getHierarchy(ClassMetadataFactory $metadata, $entityName)
    {
        /** @var \Doctrine\Common\Cache\Cache $cache */
        $cache = null;
        if ($metadata instanceof AbstractClassMetadataFactory) {
            $cache = $metadata->getCacheDriver();
        }

        if (empty($this->hierarchy)) {
            if ($cache == null || !$cache->contains(self::CACHE_KEY)) {
                if ($cache != null) {
                    $this->hierarchy = $this->buildHierarchy($metadata);
                    $cache->save(self::CACHE_KEY, $this->hierarchy);
                }

            } else {
                $this->hierarchy = $cache->fetch(self::CACHE_KEY);
            }
        }


        return $this->hierarchy[$metadata->getMetadataFor($entityName)->getName()];
    }


    /**
     * Builds a Hierarchy of every entity in the Class metadata.
     * @param ClassMetadataFactory $metadataFactory
     * @return MetadataNode[]
     */
    protected function buildHierarchy(ClassMetadataFactory $metadataFactory)
    {
        $this->hierarchy = array();
        $stack = new SplStack();

        foreach ($metadataFactory->getAllMetadata() as $metadata) {
            $stack->push($metadata->getName());
        }

        while(!$stack->isEmpty()) {
            $meta = $metadataFactory->getMetadataFor($stack->pop());
            $name = $meta->getName();

            $node = $this->getNode($name);

            $parent = $meta->getReflectionClass()->getParentClass();
            if ($parent != null && $metadataFactory->hasMetadataFor($parent->getName())) {

                $parentNode = $this->getNode(
                    $metadataFactory->getMetadataFor($parent->getName())->getName());

                $node->setParent($parentNode);
                $parentNode->addChild($node);
            }
        }

        return $this->hierarchy;
    }

    /**
     * @param $name
     * @return MetadataNode
     */
    protected function getNode($name) {
        if (!isset($this->hierarchy[$name])) {
            $this->hierarchy[$name] = new MetadataNode($name);
        }

        return $this->hierarchy[$name];
    }
}

class MetadataNode {
    /**
     * @var MetadataNode
     */
    protected $parent;

    /**
     * @var MetadataNode[]
     */
    protected $children;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @param string $entityName Name of the entity
     */
    public function __construct($entityName)
    {
        $this->entityName = $entityName;
        $this->children = array();
    }

    public function getEntityName()
    {
        return $this->entityName;
    }

    public function hasParent()
    {
        return $this->parent != null;
    }

    public function setParent(MetadataNode $parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addChild(MetadataNode $child)
    {
        $this->children[] = $child;
    }

    public function hasChild()
    {
        return !empty($this->children);
    }

    public function getFirstChild()
    {
        if (!$this->hasChild()) {
            return null;
        }

        return reset($this->children);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function hasMultipleChildren()
    {
        return count($this->children) > 1;
    }
}