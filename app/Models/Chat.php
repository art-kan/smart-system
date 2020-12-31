<?php

namespace App\Models;

use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Chat
 *
 * @method static Builder|Chat newModelQuery()
 * @method static Builder|Chat newQuery()
 * @method static Builder|Chat query()
 * @mixin Eloquent
 * @property int $id
 * @property string|null $name
 * @property int $is_private
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Chat whereCreatedAt($value)
 * @method static Builder|Chat whereId($value)
 * @method static Builder|Chat whereIsPrivate($value)
 * @method static Builder|Chat whereName($value)
 * @method static Builder|Chat whereUpdatedAt($value)
 */
class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_private',
    ];

    public function usersIfPrivate()
    {
        if ($this->is_private) {
            $link = DB::table('private_chat_links')->where('chat_id')->limit(1)->first();
            return User::whereId($link->user_id)->orWhere('id', $link->to_user_id);
        }
        return null;
    }

    /**
     * @param ?string $before
     * @param ?string $after
     * @return Collection|ChatMessage[]
     */
    public function messageHistoryGroupedByDate(string $before = null, string $after = null)
    {
        return $this->messageHistory($before, $after)->get()->groupBy(function (ChatMessage $item) {
            return \Carbon\Carbon::parse($item->created_at)->format('m.d.Y');
        });
    }

    public function messageHistory(string $before = null, string $after = null, int $limit = 10): HasMany
    {
        $q = $this->messages()->with(['sender']);
        if (!is_null($before)) $q = $q->where('created_at', '<', $before);
        if (!is_null($after)) $q = $q->where('created_at', '>', $after);

        return $q->orderBy('created_at')->limit($limit);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
