<?php

namespace hlaCk\ezCP\Database\Types\Common;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use hlaCk\ezCP\Database\Types\Type;

class JsonType extends Type
{
    const NAME = 'json';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'json';
    }
}
