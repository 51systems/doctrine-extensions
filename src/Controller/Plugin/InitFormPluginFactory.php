<?php

namespace DoctrineExtensions\Controller\Plugin;


use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class InitFormPluginFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var HydratorPluginManager $m */
        $m = $container->get('hydratorManager');

        /** @var DoctrineObject $doctrineHydrator */
        $doctrineHydrator = $m->get('DoctrineModule\Stdlib\Hydrator\DoctrineObject');

        return new InitFormPlugin($doctrineHydrator);
    }
}