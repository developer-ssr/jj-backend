<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required|confirmed',
            'email' => 'required|unique:users,email',
            'type' => 'required',
            'office_id' => 'nullable'
        ]);

        return DB::transaction(function() use($request) {
            return tap(User::create([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'type' => $request->type,
                'office_id' => $request->office_id ?? 0
            ]), function (User $user) {
                $this->createTeam($user);
            });
        });
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'office_id' => 'nullable'
        ]);
        $user->update([
            'name' => $request->name,
            'type' => $request->type,
            'office_id' => $request->office_id ?? 0
        ]);
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response('ok');
    }

    protected function createTeam(User $user)
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Team",
            'personal_team' => true,
        ]));
    }
}
