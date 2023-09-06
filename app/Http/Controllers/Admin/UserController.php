<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserCollection;
use App\Http\Resources\Admin\UserResource;

class UserController extends Controller
{
    public function index() {
        $users = User::filter(request()->only(['search']))
                    ->orderBy('id','desc')
                    ->paginate(5);
        return new UserCollection($users);
    }

    public function store(CreateUserRequest $request) {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return new UserResource($user->refresh());
    }

    public function update(UpdateUserRequest $request,User $user) {
         $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);
        return new UserResource($user);
    }

    public function changeRole(Request $request,User $user) {
         $user->update([
            'role' => $request->role
        ]);
        return new UserResource($user);
    }

    public function destory(User $user){
        $user->delete();
        return response()->noContent();
    }

     public function bulkDelete()
    {
        User::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Users deleted successfully!']);
    }
}
