<?php

namespace hlaCk\ezCP\Models;

use Illuminate\Database\Eloquent\Model;
use hlaCk\ezCP\Facades\ezCP;

class Role extends Model
{
    protected $guarded = [];

    public function users()
    {
        $userModel = ezCP::modelClass('User');

        return $this->belongsToMany($userModel, 'user_roles')
                    ->select(app($userModel)->getTable().'.*')
                    ->union($this->hasMany($userModel))->getQuery();
    }

    public function permissions()
    {
        return $this->belongsToMany(ezCP::modelClass('Permission'));
    }
}
