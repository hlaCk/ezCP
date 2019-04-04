<?php

namespace hlaCk\ezCP\Models;

use Illuminate\Database\Eloquent\Model;
use hlaCk\ezCP\Events\SettingUpdated;

class Setting extends Model
{
    protected $table = 'settings';

    protected $guarded = [];

    public $timestamps = false;

    protected $dispatchesEvents = [
        'updating' => SettingUpdated::class,
    ];
}
