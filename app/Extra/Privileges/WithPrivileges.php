<?php

namespace App\Extra\Privileges;

use App\Models\Group;
use App\Models\ReportRequest;
use App\Models\User;

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

    public function availableGroups($priv)
    {
        return $priv->allGroups(normalize($this));
    }

    public function availableUsers($priv)
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
}
