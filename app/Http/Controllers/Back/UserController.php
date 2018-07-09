<?php

namespace Framework\Http\Controllers\Back;

use Framework\ {
    Http\Controllers\Controller,
    Http\Requests\UserUpdateRequest,
    Models\User,
    Repositories\UserRepository
};

class UserController extends Controller
{
    use Indexable;

    /**
     * Create a new UserController instance.
     *
     * @param  \Framework\Repositories\UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;

        $this->table = 'users';
    }

    /**
     * Update "new" field for user.
     *
     * @param  \Framework\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function updateSeen(User $user)
    {
        $user->ingoing->delete ();

        return response ()->json ();
    }

    /**
     * Update "valid" field for user.
     *
     * @param  \Framework\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function updateValid(User $user)
    {
        $user->valid = true;
        $user->save();

        return response ()->json ();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Framework\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('back.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Framework\Http\Requests\UserUpdateRequest $request
     * @param  \Framework\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $this->repository->update($request, $user);

        return back()->with('user-updated', __('The user has been successfully updated'));
    }

    /**
     * Remove the user from storage.
     *
     * @param  \Framework\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete ();

        return response ()->json ();
    }
}
