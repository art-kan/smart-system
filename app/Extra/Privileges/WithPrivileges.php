<?php

namespace App\Extra\Privileges;

use App\Models\Group;
use App\Models\ReportRequest;
use App\Models\User;
use Illuminate\Database\Query\Builder;

trait WithPrivileges {
    /**
     * @param User|Group|ReportRequest|int|int[] $target
     * @param Privilege $priv
     * @return bool
     */
    public function hasPrivilege($target, Privilege $priv): bool
    {
        return $priv->exists(normalize($this), $target);
    }

    /**
     * @param User|Group|ReportRequest|int|int[] $target
     * @param Privilege $priv
     * @return bool
     */
    public function updatePrivilege($target, Privilege $priv): bool
    {
        return $priv->update(normalize($this), $target);
    }

    public function availableReports(Privilege $priv): Builder
    {
        return $priv->allReports(normalize($this));
    }

    public function availableReportRequests(Privilege $priv): Builder
    {
        return $priv->allReportRequests(normalize($this));
    }

    public function availableGroups(Privilege $priv): Builder
    {
        return $priv->allGroups(normalize($this));
    }

    public function availableUsers(Privilege $priv): Builder
    {
        return $priv->allUsers(normalize($this));
    }
}

/**
 * @param $object
 * @return Group|User|ReportRequest|int
 */
function normalize($object)
{
    // TODO: USE TRAIT FOR RESOURCES
    if ($object instanceof User || $object instanceof Group || $object instanceof ReportRequest)
        return $object;

    if (is_object($object)) return $object->id;

    return (int) $object;
}

