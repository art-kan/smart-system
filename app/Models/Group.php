<?php

namespace App\Models;

use App\Extra\Privileges\WithPrivileges;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Group
 *
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property int $is_hidden
 * @property int|null $only_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin Eloquent
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static Builder|Group query()
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereDescription($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereIsHidden($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group whereOnlyUserId($value)
 * @method static Builder|Group whereOrganizationId($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @property-read Organization $organization
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @property string|null $password
 * @method static Builder|Group wherePassword($value)
 */
class Group extends Model
{
    use HasFactory, WithPrivileges;

    protected $fillable = [
        'name',
        'description',
        'password',
        'is_hidden',
        'only_user_id',
        'organization_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_lists');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
