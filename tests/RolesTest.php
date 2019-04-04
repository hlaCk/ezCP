<?php

namespace hlaCk\ezCP\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use hlaCk\ezCP\Models\Role;

class RolesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testRoles()
    {
        $this->visit(route('ezcp.login'))
             ->type('admin@admin.com', 'email')
             ->type('password', 'password')
             ->press(__('ezcp::generic.login'))
             ->seePageIs(route('ezcp.dashboard'));

        // Adding a New Role
        $this->visit(route('ezcp.roles.create'))
             ->type('superadmin', 'name')
             ->type('Super Admin', 'display_name')
             ->press(__('ezcp::generic.submit'))
             ->seePageIs(route('ezcp.roles.index'))
             ->seeInDatabase('roles', ['name' => 'superadmin']);

        // Editing a Role
        $this->visit(route('ezcp.roles.edit', 2))
             ->type('regular_user', 'name')
             ->press(__('ezcp::generic.submit'))
             ->seePageIs(route('ezcp.roles.index'))
             ->seeInDatabase('roles', ['name' => 'regular_user']);

        // Editing a Role
        $this->visit(route('ezcp.roles.edit', 2))
             ->type('user', 'name')
             ->press(__('ezcp::generic.submit'))
             ->seePageIs(route('ezcp.roles.index'))
             ->seeInDatabase('roles', ['name' => 'user']);

        // Get the current super admin role
        $superadmin_role = Role::where('name', '=', 'superadmin')->first();

        // Deleting a Role
        $response = $this->call('DELETE', route('ezcp.roles.destroy', $superadmin_role->id), ['_token' => csrf_token()]);
        $this->assertEquals(302, $response->getStatusCode());
        $this->notSeeInDatabase('roles', ['name' => 'superadmin']);
    }
}
