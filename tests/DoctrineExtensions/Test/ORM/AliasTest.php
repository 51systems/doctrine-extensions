<?php

namespace DoctrineExtensions\Test\ORM;

use DoctrineExtensions\ORM\Alias;

class AliasTest extends \PHPUnit_Framework_TestCase
{
    public function testField()
    {
        $alias = new Alias();

        $this->assertEquals('a.foo', $alias->field('foo'));
    }

    public function testParam()
    {
        $alias = new Alias();
        $this->assertEquals('a_foo', $alias->param('foo'));
    }

    public function testAlias()
    {
        $alias = new Alias(['foo', 'bar']);
        $this->assertEquals('foo_bar', $alias->alias());
    }

    public function testAdd()
    {
        $alias = new Alias('a');
        $alias2 = $alias->add('b');
        $alias3 = $alias2->add('c');

        $this->assertEquals('a', $alias->alias());
        $this->assertEquals('a_b', $alias2->alias());
        $this->assertEquals('a_b_c', $alias3->alias());
    }

    public function testAddUnique()
    {
        $alias = new Alias('a');
        $alias2 = $alias->addUnique('b');
        $alias3 = $alias2->addUnique('b');
        $alias4 = $alias3->addUnique('b');

        $this->assertEquals('a_b0_b1_b2', $alias4->alias());
        $this->assertEquals('a_b3', $alias->addUnique('b')->alias());
    }

    public function testToString()
    {
        $alias = new Alias(['foo', 'bar']);
        $this->assertEquals('foo_bar', $alias . '');
    }
}