<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Organization
 *
 * @method static Builder|Organization newModelQuery()
 * @method static Builder|Organization newQuery()
 * @method static Builder|Organization query()
 * @mixin Eloquent
 * @property int $id
 * @property int|null $super_group_id
 * @property int|null $staff_group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Organization whereCreatedAt($value)
 * @method static Builder|Organization whereId($value)
 * @method static Builder|Organization whereStaffGroupId($value)
 * @method static Builder|Organization whereSuperGroupId($value)
 * @method static Builder|Organization whereUpdatedAt($value)
 * @property-read Collection|Group[] $groups
 * @property-read int|null $groups_count
 * @property-read Collection|User[] $members
 * @property-read int|null $members_count
 * @property-read Group|null $staffGroup
 * @property-read Group|null $superGroup
 * @property string $name
 * @method static Builder|Organization whereName($value)
 */
class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'super_group_id',
        'staff_group_id',
    ];

    public function superGroup(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'super_group_id');
    }

    public function staffGroup(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'staff_group_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
