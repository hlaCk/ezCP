<?php

namespace hlaCk\ezCP\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use hlaCk\ezCP\Database\Types\Type;

class LongBlobType extends Type
{
    const NAME = 'longblob';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'longblob';
    }
}
