<?php

namespace DoctrineExtensions\Gedmo\Timestampable;

/**
 * The Timestampable listener handles the update of
 * dates on creation and update.
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class TimestampableListener extends \Gedmo\Timestampable\TimestampableListener
{

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

}