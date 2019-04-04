<?php

namespace hlaCk\ezCP\Database\Types\Postgresql;

use hlaCk\ezCP\Database\Types\Common\VarCharType;

class CharacterVaryingType extends VarCharType
{
    const NAME = 'character varying';
    const DBTYPE = 'varchar';
}
