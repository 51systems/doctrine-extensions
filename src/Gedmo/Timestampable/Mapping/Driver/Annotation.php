<?php

namespace DoctrineExtensions\Gedmo\Timestampable\Mapping\Driver;

/**
 * This is an annotation mapping driver for Timestampable
 * behavioral extension. Used for extraction of extended
 * metadata from Annotations specifically for Timestampable
 * extension.
 *
 * This version of the driver supports the utc_datetime type
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Annotation extends \Gedmo\Timestampable\Mapping\Driver\Annotation
{
    public function __construct()
    {
        $this->validTypes[] = 'utc_datetime';
    }

}