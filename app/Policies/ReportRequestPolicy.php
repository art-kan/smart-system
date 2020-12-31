<?php

namespace App\Policies;

use App\Extra\Privileges\Privilege;
use App\Models\Report;
use App\Models\ReportRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param ReportRequest $reportRequest
     * @return mixed
     */
    public function view(User $user, ReportRequest $reportRequest)
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
        return $user->availableGroups(Privilege::fromAllowedList('group', ['ask_reports_priv']))->exists();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ReportRequest $reportRequest
     * @return mixed
     */
    public function update(User $user, ReportRequest $reportRequest)
    {
        return $user->hasPrivilege($reportRequest,
            Privilege::fromAllowedList('report_request', ['edit_info_priv']));
    }

    public function open(User $user, ReportRequest $reportRequest): bool
    {
        return $user->hasPrivilege($reportRequest,
            Privilege::fromAllowedList('report_request', ['open_priv']));
    }

    public function close(User $user, ReportRequest $reportRequest): bool
    {
        return $user->hasPrivilege($reportRequest,
            Privilege::fromAllowedList('report_request', ['close_priv']));
    }

    public function response(User $user, ReportRequest $reportRequest): bool
    {
        $exist = Report::where(['report_request_id' => $reportRequest->id, 'created_by' => $user->id])
            ->select('status')->first();

        return $user->hasPrivilege($reportRequest, Privilege::fromAllowedList('report_request', ['response_priv']))
            && ($exist ? $exist->status === 'rejected' : true);
    }

    public function inspect(User $user, ReportRequest $reportRequest): bool
    {
        return $user->hasPrivilege($reportRequest,
            Privilege::fromAllowedList('report_request', ['inspect_priv']));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ReportRequest $reportRequest
     * @return mixed
     */
    public function delete(User $user, ReportRequest $reportRequest)
    {
        //
    }
}
