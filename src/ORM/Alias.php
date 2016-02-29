<?php

namespace DoctrineExtensions\ORM;

/**
 * Provides an easy way to work with aliases for doctrine entities.
 *
 * Allows chaining of aliases to reduce the chance of collisions when
 * working with subqueries.
 */
class Alias
{
    /**
     * Chain of aliases
     *
     * @var string|\string[]
     */
    protected $stack = [];

    /**
     * Global counter to allow generation of
     * unique aliases.
     *
     * @var int
     */
    protected static $counter=0;

    /**
     * Constructs a new alias instance.
     * If passed parameter is an array, it completely replaces
     * the internal stack.
     *
     * @param string|string[] $alias
     */
    public function __construct($alias = 'a')
    {
        if (is_array($alias)) {
            $this->stack = $alias;
            return;
        }

        $this->stack[] = $alias;
    }

    /**
     * Adds another level.
     * The current alias will be copied
     * with the specified alias added on
     *
     * @param string $next alias to add to the stack
     * @return Alias
     */
    public function add($next)
    {
        return new self(array_merge($this->stack, (array)$next));
    }

    /**
     * Adds another level of alias.
     *
     * This method combines the prefix with a unique
     * counter to ensure that the alias is unique.
     *
     * Use this method with care as it will tend to create
     * misses in the doctrine query cache.
     *
     * @param string $prefix Prefix to use with alias
     * @return Alias
     */
    public function addUnique($prefix)
    {
        return $this->add($prefix . (self::$counter++));
    }

    /**
     * Writes out a field name
     *
     * @param string $name
     * @return string
     */
    public function field($name)
    {
        return $this->alias() . '.' . $name;
    }

    /**
     * Writes out a parameter name
     *
     * @param string $name
     * @return string
     */
    public function param($name)
    {
        return $this->alias() . '_' . $name;
    }

    /**
     * Returns the string version of the alias.
     *
     * @return string
     */
    public function alias()
    {
        return join('_', $this->stack);
    }

    public function __toString()
    {
        return $this->alias();
    }


    /**
     * Conditionally creates the alias.
     * If the passed object is already an alias, simply returns it.
     *
     * @param string|Alias $alias
     * @return Alias
     */
    public static function create($alias)
    {
        if ($alias instanceof self) {
            return $alias;
        }

        return new self($alias);
    }
}