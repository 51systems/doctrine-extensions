<?php

namespace DoctrineExtensions\Controller\Plugin;


use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class AuthenticatedUserProviderPluginFactory
 * @package DoctrineExtensions\Controller\Plugin
 *
 * Factory to create instances of {@link AuthenticatedUserProviderPlugin} objects.
 * Will attempt to automatically determine the entity class from the zfcuser configuration
 */
class AuthenticatedUserProviderPluginFactory implements FactoryInterface
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
        return new AuthenticatedUserProviderPluginFactory(
            $container->get('Zend\Authentication\AuthenticationService'),
            self::getEntityClass($container->get('config'))
        );
    }

    /**
     * Gets the entity class to use for user objects.
     * Will return a cached results if available.
     *
     * @param array $config
     * @return string
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    private static function getEntityClass($config)
    {
        if (isset($config['doctrine-extensions']['authenticated_user_provider']['entity'])) {
            $entityClass = $config['doctrine-extensions']['authenticated_user_provider']['entity'];
        } else if (isset($config['zfcuser']['user_entity_class'])) {
            $entityClass = $config['zfcuser']['user_entity_class'];
        }else {
            throw new \InvalidArgumentException('Config value doctrine-extensions.authenticated_user_provider.entity must be set');
        }

        return $entityClass;
    }
}