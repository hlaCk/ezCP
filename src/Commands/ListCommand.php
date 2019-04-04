<?php

namespace hlaCk\ezCP\Commands;

use Illuminate\Console\Command;
use hlaCk\ezCP\Hooks;

class ListCommand extends Command
{
    protected $signature = 'hook:list';

    protected $description = 'List installed hooks';

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
        $this->table(['Name', 'Status'], $this->hooks->hooks()->transform(function ($hook) {
            return [
                'name'    => $hook['name'],
                'enabled' => $hook['enabled'] ? 'Enabled' : 'Disabled',
            ];
        }));
    }
}
