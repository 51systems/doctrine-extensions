<?php

namespace DoctrineExtensions\Controller\Plugin;

use \Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Controller plugin to provide the entity manager.
 */
class EntityManagerProviderPlugin extends AbstractPlugin
{
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getController()->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    function __invoke()
    {
        return $this->getEntityManager();
    }
}