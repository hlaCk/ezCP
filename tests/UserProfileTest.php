<?php

namespace hlaCk\ezCP\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use hlaCk\ezCP\Models\Role;
use hlaCk\ezCP\Models\User;

class UserProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected $editPageForTheCurrentUser;

    protected $listOfUsers;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::loginUsingId(1);

        $this->editPageForTheCurrentUser = route('ezcp.users.edit', ['user' => $this->user->id]);

        $this->listOfUsers = route('ezcp.users.index');

        $this->withFactories(__DIR__.'/database/factories');
    }

    public function testCanSeeTheUserInfoOnHisProfilePage()
    {
        $this->visit(route('ezcp.profile'))
             ->seeInElement('h4', $this->user->name)
             ->seeInElement('.user-email', $this->user->email)
             ->seeLink(__('ezcp::profile.edit'));
    }

    public function testCanEditUserName()
    {
        $this->visit(route('ezcp.profile'))
             ->click(__('ezcp::profile.edit'))
             ->see(__('ezcp::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('New Awesome Name', 'name')
             ->press(__('ezcp::generic.save'))
             ->seePageIs($this->listOfUsers)
             ->seeInDatabase(
                 'users',
                 ['name' => 'New Awesome Name']
             );
    }

    public function testCanEditUserEmail()
    {
        $this->visit(route('ezcp.profile'))
             ->click(__('ezcp::profile.edit'))
             ->see(__('ezcp::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('another@email.com', 'email')
             ->press(__('ezcp::generic.save'))
             ->seePageIs($this->listOfUsers)
             ->seeInDatabase(
                 'users',
                 ['email' => 'another@email.com']
             );
    }

    public function testCanEditUserPassword()
    {
        $this->visit(route('ezcp.profile'))
             ->click(__('ezcp::profile.edit'))
             ->see(__('ezcp::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('ezcp-rocks', 'password')
             ->press(__('ezcp::generic.save'))
             ->seePageIs($this->listOfUsers);

        $updatedPassword = DB::table('users')->where('id', 1)->first()->password;
        $this->assertTrue(Hash::check('ezcp-rocks', $updatedPassword));
    }

    public function testCanEditUserAvatar()
    {
        $this->visit(route('ezcp.profile'))
             ->click(__('ezcp::profile.edit'))
             ->see(__('ezcp::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->attach($this->newImagePath(), 'avatar')
             ->press(__('ezcp::generic.save'))
             ->seePageIs($this->listOfUsers)
             ->dontSeeInDatabase(
                 'users',
                 ['id' => 1, 'avatar' => 'user/default.png']
             );
    }

    public function testCanEditUserEmailWithEditorPermissions()
    {
        $user = factory(\hlaCk\ezCP\Models\User::class)->create();
        $editPageForTheCurrentUser = route('ezcp.users.edit', ['user' => $user->id]);
        $roleId = $user->role_id;
        $role = Role::find($roleId);
        // add permissions which reflect a possible editor role
        // without permissions to edit  users
        $role->permissions()->attach(\hlaCk\ezCP\Models\Permission::whereIn('key', [
            'browse_admin',
            'browse_users',
        ])->get()->pluck('id')->all());
        Auth::onceUsingId($user->id);
        $this->visit(route('ezcp.profile'))
             ->click(__('ezcp::profile.edit'))
             ->see(__('ezcp::profile.edit_user'))
             ->seePageIs($editPageForTheCurrentUser)
             ->type('another@email.com', 'email')
             ->press(__('ezcp::generic.save'))
             ->seePageIs($this->listOfUsers)
             ->seeInDatabase(
                 'users',
                 ['email' => 'another@email.com']
             );
    }

    public function testCanSetUserLocale()
    {
        $this->visit(route('ezcp.profile'))
             ->click(__('ezcp::profile.edit'))
             ->see(__('ezcp::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->select('de', 'locale')
             ->press(__('ezcp::generic.save'));

        $user = User::find(1);
        $this->assertTrue(($user->locale == 'de'));

        // Validate that app()->setLocale() is called
        Auth::loginUsingId($user->id);
        $this->visitRoute('ezcp.dashboard');
        $this->assertTrue(($user->locale == $this->app->getLocale()));
    }

    protected function newImagePath()
    {
        return realpath(__DIR__.'/temp/new_avatar.png');
    }
}
