<?php

namespace DoctrineExtensions\DBAL\Types;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Database type that will always be stored in the DB in UTC.
 * This ensures that we always have a consistent timezone to work
 * from.
 *
 * @see http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/cookbook/working-with-datetime.html
 */
class UTCDateTimeType extends DateTimeType
{
    static private $utc = null;

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $value = clone $value;
        $value->setTimezone((self::$utc) ? self::$utc : (self::$utc = new \DateTimeZone('UTC')));

        return $value->format($platform->getDateTimeFormatString());
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $val = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            (self::$utc) ? self::$utc : (self::$utc = new \DateTimeZone('UTC'))
        );

        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }
        return $val;
    }
}