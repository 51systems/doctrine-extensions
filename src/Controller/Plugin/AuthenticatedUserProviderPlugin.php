<?php
namespace DoctrineExtensions\Controller\Plugin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
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
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * The entity to return.
     * @var string
     */
    private $entityClass;

    /**
     * AuthenticatedUserProviderPlugin constructor.
     * @param AuthenticationService $authenticationService
     * @param string $entityClass Class to use for entity objects
     */
    public function __construct(AuthenticationService $authenticationService, $entityClass)
    {
        $this->authenticationService = $authenticationService;
        $this->entityClass = $entityClass;
    }


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

        if (!$this->authenticationService->hasIdentity()) {
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

        $user = $this->authenticationService->getIdentity();

        /** @var EntityManager $em */
        /** @noinspection PhpUndefinedMethodInspection */
        $em = $this->getController()->entityManagerProvider();
        if ($attach && $em->getUnitOfWork()->getEntityState($user) != UnitOfWork::STATE_MANAGED) {
            return $em->find($this->entityClass, $user->getId());
        }

        return $user;
    }
}