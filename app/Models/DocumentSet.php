<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

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

    /**
     * @param UploadedFile[] $files
     * @return DocumentSet
     */
    public static function fromFiles(array $files)
    {
        $set = self::create();
        $docs = ArchiveDocument::fromFiles($files);
        $set->documents()->attach($docs->pluck('id'));
        return $set;
    }
}
