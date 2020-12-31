<?php

namespace App\Models;

use App\Extra\Documents\WithAttachments;
use App\Extra\Privileges\Privilege;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\ReportRequest
 *
 * @property-read User $creator
 * @method static Builder|ReportRequest newModelQuery()
 * @method static Builder|ReportRequest newQuery()
 * @method static Builder|ReportRequest query()
 * @mixin Eloquent
 * @property int $id
 * @property int $created_by
 * @property string $string
 * @property string $body
 * @property Carbon|null $due_date
 * @property string $status
 * @property int|null $document_set_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ReportRequest whereBody($value)
 * @method static Builder|ReportRequest whereCreatedAt($value)
 * @method static Builder|ReportRequest whereCreatedBy($value)
 * @method static Builder|ReportRequest whereDocumentSetId($value)
 * @method static Builder|ReportRequest whereDueDate($value)
 * @method static Builder|ReportRequest whereId($value)
 * @method static Builder|ReportRequest whereStatus($value)
 * @method static Builder|ReportRequest whereString($value)
 * @method static Builder|ReportRequest whereUpdatedAt($value)
 * @property string $title
 * @property-read Collection|Group[] $responderGroups
 * @property-read int|null $responder_groups_count
 * @method static Builder|ReportRequest whereTitle($value)
 * @property-read DocumentSet|null $documentSet
 */
class ReportRequest extends Model
{
    use HasFactory, WithAttachments;

    const STATUSES = ['active', 'closed', 'archived'];
    const DEFAULT_STATUS = 'active';

    protected $fillable = [
        'created_by',
        'title',
        'body',
        'due_date',
        'status',
        'document_set_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        self::created(function (ReportRequest $model) {
            $model->creator->updatePrivilege($model, Privilege::getReportRequestCreatorDefaultPriv());
            $model->__DDoPEAssignSchoolsAsResponders();
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responses(string $status = null): HasMany
    {
        $builder = $this->hasMany(Report::class);
        if ($status) {
            $builder = $builder->where('status', $status);
        }
        return $builder;
    }

    public function responsesGroupedByDate(string $status = null): Collection
    {
        return $this->responses($status)
            ->with('creator')
            ->select(['id', 'created_at', 'created_by'])
            ->selectRaw('LEFT (body, 150) as body')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function (Report $item) {
                return \Carbon\Carbon::parse($item->created_at)->format('m.d.Y');
            });
    }

    public function responderGroups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class,
            Privilege::getTableNameByTargetType('report_request')
        )->wherePivot('response_priv', true);
    }

    public function responders()
    {
        $priv_table = Privilege::getTableNameByTargetType('report_request');

        return User::whereIn('id',
            DB::table($priv_table)
                ->where('target_id', $this->id)
                ->where('response_priv', true)
                ->join('group_lists', 'group_lists.group_id', $priv_table.'.group_id')
                ->select('user_id')
        );
    }

    public function setStatus(string $new_status): bool
    {
        if (!in_array($new_status, self::STATUSES)) return false;
        $this->status = $new_status;
        return true;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isEditable(): bool
    {
        return $this->status !== 'archived';
    }

    public function isClosable(): bool
    {
        return $this->status === 'active';
    }

    public function isOpenable(): bool
    {
        return $this->status === 'closed';
    }

    private function __DDoPEAssignSchoolsAsResponders(): bool
    {
        return Group::whereName('schools')->first()->updatePrivilege($this,
            Privilege::fromAllowedList('report_request', ['response_priv']));
    }
}
