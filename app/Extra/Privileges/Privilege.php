<?php


namespace App\Extra\Privileges;


use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class Privilege
{
    /** @var string */
    private $table;

    /** @var boolean[] */
    private $setting;

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

    public function allGroups($object): Builder
    {
        return Group::whereIn('id',
            $this->buildQueryHead($object)
                ->where($this->setting)
                ->select('target_id')
        );
    }

    public function allUsers($object): Builder
    {
        return User::whereIn('id',
            $this->buildQueryHead($object)
                ->where($this->setting)
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
            : DB::table($this->table)->whereIn('group_id', $object);
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
}
