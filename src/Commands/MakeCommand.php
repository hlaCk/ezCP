<?php

namespace hlaCk\ezCP\Commands;

use Illuminate\Console\Command;
use hlaCk\ezCP\Hooks;

class MakeCommand extends Command
{
    protected $signature = 'hook:make {name}';

    protected $description = 'Make a hook';

    protected $hooks;

    public function __construct(Hooks $hooks)
    {
        $this->hooks = $hooks;

        parent::__construct();
    }

    public function fire()
    {
        return $this->handle();
    }

    public function handle()
    {
        $name = $this->argument('name');
        $name = kebab_case($name);

        $this->hooks->make($name);

        $this->info("Hook [{$name}] have been made.");
    }
}
