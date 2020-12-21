<?php


namespace App\Extra\Privileges;


use App\Models\Group;
use App\Models\Report;
use App\Models\ReportRequest;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Privilege
{
    /** @var string */
    private $table;

    /** @var boolean[] */
    private $setting;

    const PRIVILEGE_TABLES = ['privileges_on_groups', 'privileges_on_report_requests'];

    const ALL_PRIVILEGES_ON_GROUPS = [
        'inspect_priv',
        'edit_info_priv',
        'dissolve_priv',
        'group_members_priv',
        'add_members_priv',
        'remove_members_priv',
        'edit_members_priv',
    ];

    const ALL_PRIVILEGES_ON_REPORT_REQUESTS = [
        'open_priv',
        'close_priv',
        'edit_info_priv',
        'response_priv',
        'reject_response_priv',
        'accept_response_priv',
    ];

    public static function getTableNameByTargetType(string $target_type): string
    {
        // by name conversation
        $target_type = preg_replace('/[^a-zA-Z0-9_$]/', '', $target_type);
        return 'privileges_on_'.Str::plural($target_type);
    }

    /**
     * Privilege constructor.
     * @param string $target_type
     * @param boolean[] $privileges_setting
     */
    public function __construct(string $target_type, array $privileges_setting)
    {
        $this->table = self::getTableNameByTargetType($target_type);
        $this->setting = $privileges_setting;
    }

    public static function getReportRequestCreatorDefaultPriv(): Privilege
    {
        return new Privilege('report_request', array_merge(
            self::assignBooleans(self::ALL_PRIVILEGES_ON_REPORT_REQUESTS, true),
            self::assignBooleans(self::addGrantKeyPrefix(self::ALL_PRIVILEGES_ON_REPORT_REQUESTS), true),
        ));
    }

    public static function getGroupCreatorDefaultPriv(): Privilege
    {
        return new Privilege('report_request', array_merge(
            self::assignBooleans(self::ALL_PRIVILEGES_ON_GROUPS, true),
            self::assignBooleans(self::addGrantKeyPrefix(self::ALL_PRIVILEGES_ON_GROUPS), true),
        ));
    }

    public static function fromAllowedList(string $target, $list): Privilege
    {
        return new self($target, self::assignBooleans($list, true));
    }

    /**
     * @param User|Group|int|int[] $object
     * @param User|Group|int|int[] $target
     * @return bool
     */
    public function exists($object, $target): bool
    {
        $builder = $this->buildQueryHead($object);
        $this->setQueryTarget($builder, $target);

        $fetched = $builder->select(array_keys($this->setting))->first();

        return (array) $fetched == $this->setting;
    }

    public function update($object, $target): bool
    {
        $builder = $this->buildQueryHead($object);
        $this->setQueryTarget($builder, $target);

        return $this->checkSettingValidity($this->setting) && $builder->update($this->setting);
    }

    public function allReports($object): Builder
    {
        return Report::whereIn('id', $this->buildQueryHead($object)->select('target_id'));
    }

    public function allReportRequests($object): Builder
    {
        return ReportRequest::whereIn('id', $this->buildQueryHead($object)->select('target_id'));
    }

    public function allGroups($object): Builder
    {
        return Group::whereIn('id',
            $this->buildQueryHead($object)->select('target_id'));
    }

    public function allUsers($object): Builder
    {
        return User::whereIn('id',
            $this->buildQueryHead($object)
                ->join('group_lists', 'group_lists.group_id', $this->table.'.group_id')
                ->select('group_lists.user_id')
        );
    }

    private function buildQueryHead($object): Builder
    {
        $object = $object instanceof User
            ? $object->groups()->select('id')
            : ($object instanceof Group ? $object->id : $object);

        return is_int($object)
            ? DB::table($this->table)->where('group_id', $object)
                ->where($this->setting)
            : DB::table($this->table)->whereIn('group_id', $object)
                ->where($this->setting);
    }

    private function setQueryTarget(Builder $q, $target): Builder
    {
        $target = $target instanceof User
            ? $target->groups()->get('id')
            : ($target instanceof Group ? $target->id : $target);

        return is_int($target)
            ? $q->where('target_id', $target)
            : $q->whereIn('target_id', $target);
    }

    private function checkSettingValidity(array $against): bool
    {
        foreach ($against as $key => $value) {
            if (!(Str::endsWith($key, '_priv') && is_bool($value))) return false;
        }

        return true;
    }

    private static function addGrantKeyPrefix(array $assoc): array
    {
        $result = [];
        foreach ($assoc as $priv => $bool) {
            $result['grant_' . $priv] = $bool;
        }
        return $result;
    }

    private static function assignBooleans(array $array, bool $bool): array
    {
        $result = [];
        foreach ($array as $priv) {
            $result[$priv] = $bool;
        }
        return $result;
    }
}
