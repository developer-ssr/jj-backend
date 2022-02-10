<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
                'office_id' => $request->office_id ?? 0,
                'office_ids' => $request->office_ids ?? []
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
            'office_id' => $request->office_id ?? 0,
            'office_ids' => $request->office_ids ?? []
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

    public function change_password(Request $request)
    {
        $request->validate([
            'password' => 'current_password:api',
            'new_password' => 'required|confirmed'
        ]);
        if (Hash::check($request->password, $request->user()->password)) {
            $request->user()->update([
                'password' => bcrypt($request->new_password)
            ]);
            return response()->json(['result' => 'ok'], 200);
        } else {
            throw ValidationException::withMessages([
                'password' => ['Incorrect password.']
            ]);
        }
    }
}
