<?php

namespace App\Models;

use App\Extra\Documents\WithAttachments;
use App\Extra\Privileges\Privilege;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
 */
class ReportRequest extends Model
{
    use HasFactory, WithAttachments;

    const STATUSES = ['ACTIVE', 'CLOSED', 'ARCHIVED'];

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

    public function setStatus(string $new_status): bool
    {
        if (!in_array($new_status, self::STATUSES)) return false;
        $this->status = $new_status;
        return true;
    }
}
