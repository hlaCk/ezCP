<?php

namespace hlaCk\ezCP\Events;

use hlaCk\ezCP\Hook;

class InstalledHook
{
    public $hook;

    /**
     * Create a new event instance.
     *
     * @param \hlaCk\ezCP\Hook $hook
     */
    public function __construct(Hook $hook)
    {
        $this->hook = $hook;
    }
}
