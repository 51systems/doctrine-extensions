<?php
namespace DoctrineExtensions\ORM\Traits;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Allows classes to hold an instance of a {@link EntityManagerInterface}
 */
trait EntityManagerAwareTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Set the {@link EntityManagerInterface}
     *
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get the stored {@link EntityManagerInterface} instance.
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}