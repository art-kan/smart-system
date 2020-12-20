<?php

namespace App\Models;

use App\Extra\Documents\WithAttachments;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Report
 *
 * @method static Builder|Report newModelQuery()
 * @method static Builder|Report newQuery()
 * @method static Builder|Report query()
 * @mixin Eloquent
 * @property int $id
 * @property int $report_request_id
 * @property int $created_by
 * @property string $title
 * @property string $body
 * @property string $status
 * @property int|null $document_set_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Report whereBody($value)
 * @method static Builder|Report whereCreatedAt($value)
 * @method static Builder|Report whereCreatedBy($value)
 * @method static Builder|Report whereDocumentSetId($value)
 * @method static Builder|Report whereId($value)
 * @method static Builder|Report whereReportRequestId($value)
 * @method static Builder|Report whereStatus($value)
 * @method static Builder|Report whereTitle($value)
 * @method static Builder|Report whereUpdatedAt($value)
 * @property-read User $creator
 * @property-read ReportRequest $respondedTo
 */
class Report extends Model
{
    use HasFactory, WithAttachments;

    const STATUSES = ['PENDING', 'ACCEPTED', 'REJECTED'];

    protected $fillable = [
        'report_request_id',
        'created_by',
        'body',
        'status',
        'document_set_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function respondedTo(): BelongsTo
    {
        return $this->belongsTo(ReportRequest::class);
    }

    public function setStatus(string $new_status): bool
    {
        if (!in_array($new_status, self::STATUSES)) return false;
        $this->status = $new_status;
        return true;
    }
}
