<?php

namespace DoctrineExtensions\Controller\Plugin;

use Doctrine\ORM\EntityManager;
use \Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Controller plugin to provide the entity manager.
 */
class EntityManagerProviderPlugin extends AbstractPlugin
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * EntityManagerProviderPlugin constructor.
     * @param $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    function __invoke()
    {
        return $this->getEntityManager();
    }
}