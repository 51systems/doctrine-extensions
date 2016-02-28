<?php

namespace DoctrineExtensions\Test\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use DoctrineExtensions\DataFixtures\AddIfNotPresentTrait;

class AddIfNotPresentTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the case where the object isn't present
     */
    public function testAddIfNotPresent()
    {
        $params = $this->params();
        $trait = $this->factory();

        $entity = new TestEntity('test-id');
        $criteria = ['name'=> 'test'];
        $prefix = 'test';

        \Phake::when($trait)
            ->getReference('test-test')
            ->thenThrow(new \OutOfBoundsException("Reference does not exist"));


        $result = \Phake::makeVisible($trait)->_addIfNotPresent($entity, $criteria, $prefix, $params['object_manager']);

        \Phake::verify($trait)->addReference('test-test', $entity);
        \Phake::verify($params['object_manager'])->persist($entity);

        $this->assertSame($entity, $result);
    }

    /**
     * Verify the behaviour when the object already exists in the
     * repository.
     */
    public function testAddIfNotPresentExists()
    {
        $params = $this->params();
        $trait = $this->factory();

        $existingEntity = new TestEntity('test-id');

        //Make the repostiory return an object
        \Phake::when($params['repo'])
            ->findOneBy(\Phake::anyParameters())
            ->thenReturn($existingEntity);

        $entity = new TestEntity('test-id');
        $criteria = ['name'=> 'test'];
        $prefix = 'test';

        $result = \Phake::makeVisible($trait)->_addIfNotPresent($entity, $criteria, $prefix, $params['object_manager']);

        \Phake::verify($trait)->addReference('test-test', $entity);
        \Phake::verify($params['object_manager'], \Phake::times(0))->persist($entity);

        $this->assertNotSame($entity, $result);
        $this->assertSame($existingEntity, $result);
    }

    /**
     * Verify the behaviour when the reference already exists
     */
    public function testAddIfNotPresentReferenceExists()
    {
        $params = $this->params();
        $trait = $this->factory();

        $existingEntity = new TestEntity('test-id');

        //Make the repostiory return an object
        \Phake::when($params['repo'])
            ->findOneBy(\Phake::anyParameters())
            ->thenReturn($existingEntity);

        \Phake::when($trait)
            ->hasReference('test-test')
            ->thenReturn(true);

        \Phake::when($trait)
            ->getReference('test-test')
            ->thenReturn($existingEntity);

        $entity = new TestEntity('test-id');
        $criteria = ['name'=> 'test'];
        $prefix = 'test';

        $result = \Phake::makeVisible($trait)->_addIfNotPresent($entity, $criteria, $prefix, $params['object_manager']);

        \Phake::verify($trait, \Phake::times(0))->addReference('test-test', $entity);
        \Phake::verify($params['object_manager'], \Phake::times(0))->persist($entity);

        $this->assertNotSame($entity, $result);
        $this->assertSame($existingEntity, $result);
    }

    /**
     * Verify the behaviour when the reference is set to a different
     * object than the one returned by the repository.
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage New value does not match existing test-test reference value
     */
    public function testAddIfNotPresentReferenceExistsNoMatch()
    {
        $params = $this->params();
        $trait = $this->factory();

        $existingEntity = new TestEntity('test-id');

        //Make the repostiory return an object
        \Phake::when($params['repo'])
            ->findOneBy(\Phake::anyParameters())
            ->thenReturn($existingEntity);

        \Phake::when($trait)
            ->hasReference('test-test')
            ->thenReturn(true);

        \Phake::when($trait)
            ->getReference('test-test')
            ->thenReturn(new TestEntity('test-id'));

        $entity = new TestEntity('test-id');
        $criteria = ['name'=> 'test'];
        $prefix = 'test';

        $result = \Phake::makeVisible($trait)->_addIfNotPresent($entity, $criteria, $prefix, $params['object_manager']);

        \Phake::verify($trait, \Phake::times(0))->addReference('test-test', $entity);
        \Phake::verify($params['object_manager'], \Phake::times(0))->persist($entity);

        $this->assertNotSame($entity, $result);
        $this->assertSame($existingEntity, $result);
    }

    /**
     * @return array
     */
    public function params() {
        $params = [
            'object_manager' => \Phake::mock(ObjectManager::class),
            'repo' => \Phake::mock(EntityRepository::class)
        ];

        \Phake::when($params['object_manager'])
            ->getRepository(\Phake::anyParameters())->thenReturn($params['repo']);

        return $params;
    }

    /**
     * @return ConcreteAddIfNotPresentTrait|\Phake_IMock
     */
    public function factory() {
        return \Phake::partialMock(ConcreteAddIfNotPresentTrait::class);
    }
}

abstract class ConcreteAddIfNotPresentTrait {
    use AddIfNotPresentTrait;
}

class TestEntity {
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}