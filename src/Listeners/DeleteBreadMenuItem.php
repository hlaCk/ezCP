<?php

namespace hlaCk\ezCP\Listeners;

use hlaCk\ezCP\Events\BreadDeleted;
use hlaCk\ezCP\Facades\ezCP;

class DeleteBreadMenuItem
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Delete a MenuItem for a given BREAD.
     *
     * @param BreadDeleted $bread
     *
     * @return void
     */
    public function handle(BreadDeleted $bread)
    {
        if (config('ezcp.bread.add_menu_item')) {
            $menuItem = ezCP::model('MenuItem')->where('route', 'ezcp.'.$bread->dataType->slug.'.index');

            if ($menuItem->exists()) {
                $menuItem->delete();
            }
        }
    }
}
