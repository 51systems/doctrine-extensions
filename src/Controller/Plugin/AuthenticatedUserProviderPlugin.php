<?php
namespace DoctrineExtensions\Controller\Plugin;

use Doctrine\ORM\EntityManager;
use Zf2Extensions\Controller\Plugin\SendResponsePlugin;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\DispatchableInterface;

/**
 * Controller plugin to provide the authenticated user
 */
class AuthenticatedUserProviderPlugin extends SendResponsePlugin
{
    /**
     * The entity to return.
     * @var string
     */
    private $entityClass;

    public function __invoke($requireUser=true, $attach=false)
    {
        return $this->getUser($requireUser, $attach);
    }

    /**
     * Returns the authenticated user (if it exists) or null.
     *
     * @param bool $requireUser If true, will end the request if there is no logged in user.
     * @param bool $attach If true, the user will be attached to the Entity Manager
     * @return object
     */
    public function getUser($requireUser=true, $attach=false)
    {
        /** @var AbstractController $controller */
        $controller = $this->getController();

        /** @var AuthenticationService $authService */
        $authService =  $controller->getServiceLocator()->get('AuthService');

        if (!$authService->hasIdentity()) {
            if (!$requireUser)
                return null;

            //We require the user, if we dont have it, return a 404 error and end the request
            $controller = $this->getController();
            if ($controller instanceof AbstractActionController) {
                //TODO: we may want to handle ajax requests a bit better
                $this->sendResponse($controller->redirect()->toRoute('login'));
            }


            /** @var Response $response */
            $response = $controller->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_401);
            $response->setContent('User Not Found');

            $this->sendResponse($response);
        }

        $user = $authService->getIdentity();

        if (!$attach)
            return $user;

        /** @var EntityManager $em */
        $em = $this->getController()->entityManagerProvider();
        return $em->find($this->getEntityClass(), $user->getId());
    }


    /**
     * Gets the entity class to use for user objects.
     * Will return a cached results if available.
     *
     * @return string
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    private function getEntityClass()
    {
        if (isset($this->entityClass))
            return $this->entityClass;

        if (!($this->getController() instanceof ServiceLocatorAwareInterface)) {
            throw new \UnexpectedValueException("Controller must implement ServiceLocatorAwareInterface");
        }

        /** @var ServiceLocatorAwareInterface|DispatchableInterface $controller */
        $controller = $this->getController();

        $config = $controller->getServiceLocator()->get('config');


        if (isset($config['doctrine-extensions']['authenticated_user_provider']['entity'])) {
            $entityClass = $config['doctrine-extensions']['authenticated_user_provider']['entity'];
        } else if (isset($config['zfcuser']['user_entity_class'])) {
            $entityClass = $config['zfcuser']['user_entity_class'];
        }else {
            throw new \InvalidArgumentException('Config value doctrine-extensions.authenticated_user_provider.entity must be set');
        }

        return $this->entityClass = $entityClass;
    }

}