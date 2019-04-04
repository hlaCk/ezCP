<?php

namespace hlaCk\ezCP\Facades;

use Illuminate\Support\Facades\Facade;

class ezCP extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ezcp';
    }
}
