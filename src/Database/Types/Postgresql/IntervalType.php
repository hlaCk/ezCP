<?php

namespace hlaCk\ezCP\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use hlaCk\ezCP\Database\Types\Type;

class IntervalType extends Type
{
    const NAME = 'interval';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'interval';
    }
}
