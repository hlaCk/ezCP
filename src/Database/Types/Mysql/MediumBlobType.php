<?php

namespace hlaCk\ezCP\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use hlaCk\ezCP\Database\Types\Type;

class MediumBlobType extends Type
{
    const NAME = 'mediumblob';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'mediumblob';
    }
}
