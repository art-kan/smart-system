<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\DocumentSet
 *
 * @property int $id
 * @property-read Collection|ArchiveDocument[] $documents
 * @property-read int|null $documents_count
 * @method static Builder|DocumentSet newModelQuery()
 * @method static Builder|DocumentSet newQuery()
 * @method static Builder|DocumentSet query()
 * @method static Builder|DocumentSet whereId($value)
 * @mixin Eloquent
 */
class DocumentSet extends Model
{
    use HasFactory;

    public $timestamps = [];

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(ArchiveDocument::class, 'document_set_lists');
    }
}
