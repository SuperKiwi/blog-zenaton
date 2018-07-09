<?php

namespace Framework\Repositories;

use Framework\Models\User;

class UserRepository
{
    /**
     * Get users collection paginate.
     *
     * @param  int  $nbrPages
     * @param  array  $parameters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll($nbrPages, $parameters)
    {
        return User::with ('ingoing')
            ->orderBy ($parameters['order'], $parameters['direction'])
            ->when (($parameters['role'] !== 'all'), function ($query) use ($parameters) {
                $query->whereRole ($parameters['role']);
            })->when ($parameters['valid'], function ($query) {
                $query->whereValid (true);
            })->when ($parameters['confirmed'], function ($query) {
                $query->whereConfirmed (true);
            })->when ($parameters['new'], function ($query) {
                $query->has ('ingoing');
            })->paginate ($nbrPages);
    }

    /**
     * Update a user.
     *
     * @param  \Framework\Http\Requests\UserUpdateRequest $request
     * @param  \Framework\Models\User $user
     * @return void
     */
    public function update($request, $user)
    {
        $inputs = $request->all ();

        if (isset($inputs['confirmed'])) {
            $inputs['confirmed'] = true;
        }

        if (isset($inputs['valid'])) {
            $inputs['valid'] = true;
        }

        $user->update($inputs);

        if(!$request->has('new') && $user->ingoing) {
            $user->ingoing->delete ();
        }
    }
}
