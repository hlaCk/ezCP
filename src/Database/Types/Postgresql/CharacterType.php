<?php

namespace hlaCk\ezCP\Database\Types\Postgresql;

use hlaCk\ezCP\Database\Types\Common\CharType;

class CharacterType extends CharType
{
    const NAME = 'character';
    const DBTYPE = 'bpchar';
}
