<?php

namespace DoctrineExtensions\Controller\Plugin;


use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Form;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Hydrator\HydratorPluginManager;

/**
 * Given the class name of a form, will instantiate it
 * initialize it and setup the hydrators.
 */
class InitFormPlugin extends AbstractPlugin
{
    function __invoke($formClass)
    {
        /** @var Form $form */
        $form = new $formClass();

        if ($form instanceof ObjectManagerAwareInterface) {
            $form->setObjectManager($this->getController()->entityManagerProvider());
        }

        $form->init();

        /** @var ServiceLocatorAwareInterface $controller */
        $controller = $this->getController();

        if (!($controller instanceof ServiceLocatorAwareInterface)) {
            throw new \Exception("Controller must implement ServiceLocatorAwareInterface");
        }

        /** @var HydratorPluginManager $m */
        $m = $controller->getServiceLocator()->get('hydratorManager');

        /** @var DoctrineObject $doctrineHydrator */
        $doctrineHydrator = $m->get('DoctrineModule\Stdlib\Hydrator\DoctrineObject');

        $form->setHydrator($doctrineHydrator);

        return $form;
    }

}