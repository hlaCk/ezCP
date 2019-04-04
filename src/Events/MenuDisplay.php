<?php

namespace hlaCk\ezCP\Events;

use Illuminate\Queue\SerializesModels;
use hlaCk\ezCP\Models\Menu;

class MenuDisplay
{
    use SerializesModels;

    public $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;

        // @deprecate
        //
        event('ezcp.menu.display', $menu);
    }
}
