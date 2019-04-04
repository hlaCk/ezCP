<?php

namespace hlaCk\ezCP\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use hlaCk\ezCP\Facades\ezCP;

class AdminCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ezcp:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make sure there is a user with the admin role that has all of the necessary permissions.';

    /**
     * Get user options.
     */
    protected function getOptions()
    {
        return [
            ['no-confirm', 'y', InputOption::VALUE_NONE, 'No password confirm', null],
            ['create', 'c', InputOption::VALUE_NONE, 'Create an admin user', null],
            ['name', null, InputOption::VALUE_OPTIONAL, 'Admin name', null],
            ['password', 'p', InputOption::VALUE_OPTIONAL, 'Admin password', null],
            ['username', 'u', InputOption::VALUE_OPTIONAL, 'Admin username', null],
        ];
    }
    public function fire()
    {
        return $this->handle();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Get or create user
        $user = $this->getUser(
            $this->option('create'),
            $this->option('no-confirm')
        );

        // the user not returned
        if (!$user) {
            exit;
        }

        // Get or create role
        $role = $this->getAdministratorRole();

        // Get all permissions
        $permissions = ezCP::model('Permission')->all();

        // Assign all permissions to the admin role
        $role->permissions()->sync(
            $permissions->pluck('id')->all()
        );

        // Ensure that the user is admin
        $user->role_id = $role->id;
        $user->save();

        $this->info('The user now has full access to your site.');
    }

    /**
     * Get command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['email', InputArgument::OPTIONAL, 'The email of the user.', null],
        ];
    }

    /**
     * Get the administrator role, create it if it does not exists.
     *
     * @return mixed
     */
    protected function getAdministratorRole()
    {
        $role = ezCP::model('Role')->firstOrNew([
            'name' => 'admin',
        ]);

        if (!$role->exists) {
            $role->fill([
                'display_name' => 'Administrator',
            ])->save();
        }

        return $role;
    }

    /**
     * Get or create user.
     *
     * @param bool $create
     *
     * @return \App\User
     */
    protected function getUser($create = false, $confirm = true)
    {
        $email = $this->argument('email');
        $name = $this->option('name');
        $username = $this->option('username') ?: false ;
        $password = $this->option('password');

        $model = config('ezcp.user.namespace') ?: config('auth.providers.users.model');

        // If we need to create a new user go ahead and create it
        if ($create) {

            // Ask for email if there wasnt set one
            if (!$email) {
                $email = $this->ask('Enter the admin email');
            }
            // Ask for email if there wasnt set one
            if (!$name) {
                $name = $this->ask('Enter the admin name');
            }
            // Ask for email if there wasnt set one
            if ($username) {
                $username = is_bool($username) ? $this->ask('Enter the admin username') : $username;
            }
            // Ask for email if there wasnt set one
            if (!$password) {
                $password = $this->secret('Enter admin password');
            }

            $confirmPassword = !$confirm ? $password : $this->secret('Confirm Password');

            // Passwords don't match
            if ($password != $confirmPassword) {
                $this->info("Passwords don't match");

                return;
            }

            $this->info('Creating admin account');

            $admin_data = [];

            if( $email )
                $admin_data[ 'email' ] = $email;

            if( $name )
                $admin_data[ 'name' ] = $name;

            if( $username )
                $admin_data[ 'username' ] = $username;

            return $model::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($password),
            ]);
        }

        return $model::where('email', $email)->firstOrFail();
    }
}
