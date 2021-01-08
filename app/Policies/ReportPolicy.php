<?php

namespace App\Policies;

use App\Models\User;
use App\Models\report;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param report $report
     * @return mixed
     */
    public function view(User $user, report $report)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param report $report
     * @return mixed
     */
    public function update(User $user, report $report)
    {
        //
    }
}
