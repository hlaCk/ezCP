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
     * assign this fields to user on create
     *
     * @var string
     */
    protected $fileds = [];

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
            ['debug', null, InputOption::VALUE_NONE, '*/*/*/*/*', null],
            ['no-confirm', 'y', InputOption::VALUE_NONE, 'No password confirm', null],
            ['create', 'c', InputOption::VALUE_NONE, 'Create an admin user', null],
            ['name', null, InputOption::VALUE_OPTIONAL, 'Admin name', null],
            ['password', 'p', InputOption::VALUE_OPTIONAL, 'Admin password', null],
            ['username', 'u', InputOption::VALUE_OPTIONAL, 'Admin username', null],
            ['add', 'a', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Add fields. -a "email=email@site.com"', []],
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
        if( $this->option('debug') )
            d(
                $this->arguments(),
                $this->options(),
                "no-confirm: " . (!!!$this->option('no-confirm'))
            );

        // Get or create user
        $user = $this->getUser(
            $this->option('create'),
            !!!$this->option('no-confirm')
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
     * Get option.
     *
     * @param $optionName
     *
     * @return mixed
     */
    protected function o( $optionName ) {
        if( $this->fileds && isset($this->fileds[ $optionName ]) )
            return $this->fileds[ $optionName ];

        return $this->option(...func_num_args());
    }

    /**
     * register options.
     *
     * @param array $options
     * @return bool
     */
    protected function optionsFieldRegister( array $options ) {
        if( count($options) ) {
            foreach ($options as $value) {
                $value = explode("=", $value);
                $this->fileds[ $value[0] ] = isset($value[1]) ? $value[1] : null;
            }
        } else return false;

        return true;
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
        $this->optionsFieldRegister( $this->option('add') );

        $name = $this->o('name');
        $username = $this->o('username') ?: false ;
        $password = $this->o('password');

        $model = config('ezcp.user.namespace') ?: config('auth.providers.users.model');

        // If we need to create a new user go ahead and create it
        if ($create) {

            // Ask for email if there wasnt set one
            if (!$email) {
                $email = $this->ask('Enter the admin email');
            }
            // Ask for name if there wasnt set one
            if (!$name) {
                $name = $this->ask('Enter the admin name');
            }

            // Ask for password if there wasnt set one
            if (!$password) {
                $password = $this->secret('Enter admin password');
            }

            $confirmPassword = !$confirm ? $password : $this->secret('Confirm Password');

            // Passwords don't match
            if ($password != $confirmPassword) {
                $this->info("Passwords don't match");

                return;
            }
            // Passwords don't match
            if ( $model::where('email', $email)->get()->count() > 0 ) {
                $this->error("{$email} already exist !");

                return;
            }

            $this->info('Creating admin account');

            $admin_data = array_merge($this->fileds?:[], ['email'=>$email]);

            if( $email && !$admin_data[ 'email' ] )
                $admin_data[ 'email' ] = $email;

            if( $name && !$admin_data[ 'name' ] )
                $admin_data[ 'name' ] = $name;

            if( $username && !$admin_data[ 'username' ] )
                $admin_data[ 'username' ] = $username;

            if( $password && !$admin_data[ 'password' ] )
                $admin_data[ 'password' ] = $password;

            $admin_data[ 'password' ] = Hash::make($password);

            return $model::create(
                $admin_data
            );
        }

        return $model::where('email', $email)->firstOrFail();
    }
}
