<?php

namespace hlaCk\ezCP\Http\Controllers;

use Illuminate\Http\Request;
use hlaCk\ezCP\Facades\ezCP;

class ezCPUserController extends ezCPBaseController
{
    public function profile(Request $request)
    {
        return ezCP::view('ezcp::profile');
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        if (app('ezCPAuth')->user()->getKey() == $id) {
            $request->merge([
                'role_id'                              => app('ezCPAuth')->user()->role_id,
                'user_belongsto_role_relationship'     => app('ezCPAuth')->user()->role_id,
                'user_belongstomany_role_relationship' => app('ezCPAuth')->user()->roles->pluck('id')->toArray(),
            ]);
        }

        return parent::update($request, $id);
    }
}
