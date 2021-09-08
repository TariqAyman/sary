<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends ApiController
{

    /**
     * UserController construct
     */
    public function __construct()
    {
        $this->middleware(['auth:api', 'isAdmin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $users = User::query()->get();

        return $this->success($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $this->validator($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'employee_number' => 'required|digits:4|unique:users',
            'user_type' => 'required|in:admin,employee'
        ]);

        $user = User::query()->create($request->all());

        return $this->success($user);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return string
     */
    public function show($id)
    {
        $user = User::query()->findOrFail($id);

        if (!$user) return $this->notFound('User Not Found');

        return $this->success($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return string
     */
    public function update(Request $request, $id)
    {
        $user = User::query()->find($id);

        if (!$user) return $this->notFound('User Not Found');

        $this->validator($request, [
            'email' => "string|email|max:100|unique:users,email,$id",
            'password' => 'nullable|string|min:6',
            'employee_number' => "digits:4|unique:users,employee_number,$id",
            'user_type' => 'in:admin,employee'
        ]);

        $user->update($request->all());

        return $this->success($user->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return string
     */
    public function destroy($id)
    {
        $user = User::query()->find($id);

        if (!$user) return $this->notFound('User Not Found');

        $user->delete();

        return $this->success('User Deleted!');
    }
}
