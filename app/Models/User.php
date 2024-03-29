<?php

namespace App\Models;

use App\Extra\Privileges\Privilege;
use App\Extra\Privileges\WithPrivileges;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $organization_id
 * @property string $role
 * @property string|null $deleted_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @mixin Eloquent
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereOrganizationId($value)
 * @method static Builder|User whereRole($value)
 * @property-read Collection|Group[] $groups
 * @property-read int|null $groups_count
 * @property-read Organization $organization
 * @property-read Group|null $primitiveGroup
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, WithPrivileges;

    protected $fillable = [
        'name',
        'role',
        'email',
        'password',
        'organization_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        self::created(function (User $model) {
            $priv_group = Group::create([
                'name' => $model->name,
                'organization_id' => $model->organization_id,
                'only_user_id' => $model->id,
            ]);

            $priv_group->users()->attach($model);
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_lists');
    }

    public function primitiveGroup(): HasOne
    {
        return $this->hasOne(Group::class, 'only_user_id');
    }

    public function writeTo(User $user, string $text): ChatMessage
    {
        return ChatMessage::create([
            'sent_by' => $this->id,
            'body' => $text,
            'chat_id' => $this->chatWith($user)->select('id'),
        ]);
    }

    public function chatWith(User $user): ?Chat
    {
        return Chat::where('id', function (QueryBuilder $query) use ($user) {
            $query->from('private_chat_links')
                ->where(['user_id' => $this->id, 'to_user_id' => $user->id])
                ->select('chat_id');
        })->first();
    }
}
