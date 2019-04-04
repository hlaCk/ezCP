<?php

namespace hlaCk\ezCP\Events;

use Illuminate\Queue\SerializesModels;
use hlaCk\ezCP\Models\Setting;

class SettingUpdated
{
    use SerializesModels;

    public $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }
}
