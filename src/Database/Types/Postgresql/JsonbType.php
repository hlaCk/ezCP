<?php

namespace hlaCk\ezCP\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use hlaCk\ezCP\Database\Types\Type;

class JsonbType extends Type
{
    const NAME = 'jsonb';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'jsonb';
    }
}
