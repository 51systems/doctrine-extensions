<?php

namespace DoctrineExtensions\Controller\Plugin;


use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Form;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Given the class name of a form, will instantiate it
 * initialize it and setup the hydrators.
 */
class InitFormPlugin extends AbstractPlugin
{
    /**
     * @var DoctrineObject
     */
    private $doctrineHydrator;

    /**
     * InitFormPlugin constructor.
     * @param DoctrineObject $doctrineHydrator
     */
    public function __construct(DoctrineObject $doctrineHydrator)
    {
        $this->doctrineHydrator = $doctrineHydrator;
    }

    function __invoke($formClass)
    {
        /** @var Form $form */
        $form = new $formClass();

        if ($form instanceof ObjectManagerAwareInterface) {
            $form->setObjectManager($this->getController()->entityManagerProvider());
        }

        $form->init();
        $form->setHydrator($this->doctrineHydrator);

        return $form;
    }

}