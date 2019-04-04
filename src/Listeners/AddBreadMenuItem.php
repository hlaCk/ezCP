<?php

namespace hlaCk\ezCP\Listeners;

use hlaCk\ezCP\Events\BreadAdded;
use hlaCk\ezCP\Facades\ezCP;

class AddBreadMenuItem
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
     * Create a MenuItem for a given BREAD.
     *
     * @param BreadAdded $event
     *
     * @return void
     */
    public function handle(BreadAdded $bread)
    {
        if (config('ezcp.bread.add_menu_item') && file_exists(base_path('routes/web.php'))) {
            require base_path('routes/web.php');

            $menu = ezCP::model('Menu')->where('name', config('ezcp.bread.default_menu'))->firstOrFail();

            $menuItem = ezCP::model('MenuItem')->firstOrNew([
                'menu_id' => $menu->id,
                'title'   => $bread->dataType->display_name_plural,
                'url'     => '',
                'route'   => 'ezcp.'.$bread->dataType->slug.'.index',
            ]);

            $order = ezCP::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target'     => '_self',
                    'icon_class' => $bread->dataType->icon,
                    'color'      => null,
                    'parent_id'  => null,
                    'order'      => $order,
                ])->save();
            }
        }
    }
}
