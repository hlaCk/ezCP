<?php

namespace hlaCk\ezCP\Listeners;

use hlaCk\ezCP\Events\BreadAdded;
use hlaCk\ezCP\Facades\ezCP;

class AddBreadPermission
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
     * Create Permission for a given BREAD.
     *
     * @param BreadAdded $event
     *
     * @return void
     */
    public function handle(BreadAdded $bread)
    {
        if (config('ezcp.bread.add_permission') && file_exists(base_path('routes/web.php'))) {
            // Create permission
            //
            // Permission::generateFor(snake_case($bread->dataType->slug));
            $role = ezCP::model('Role')->where('name', config('ezcp.bread.default_role'))->firstOrFail();

            // Get permission for added table
            $permissions = ezCP::model('Permission')->where(['table_name' => $bread->dataType->name])->get()->pluck('id')->all();

            // Assign permission to admin
            $role->permissions()->attach($permissions);
        }
    }
}
