<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Log;
use Storage;

/**
 * App\Models\ArchiveDocument
 *
 * @property int $id
 * @property string $path
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ArchiveDocument newModelQuery()
 * @method static Builder|ArchiveDocument newQuery()
 * @method static Builder|ArchiveDocument query()
 * @method static Builder|ArchiveDocument whereCreatedAt($value)
 * @method static Builder|ArchiveDocument whereId($value)
 * @method static Builder|ArchiveDocument whereName($value)
 * @method static Builder|ArchiveDocument wherePath($value)
 * @method static Builder|ArchiveDocument whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string $filename
 * @method static Builder|ArchiveDocument whereFilename($value)
 * @property int $size
 * @method static Builder|ArchiveDocument whereSize($value)
 */
class ArchiveDocument extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        self::deleted(function (ArchiveDocument $model) {
            Log::info($model->path);
            Storage::delete($model->path);
        });
    }

    protected $fillable = [
        'filename',
        'path',
        'size',
    ];

    /**
     * @param UploadedFile[] $files
     * @return Collection
     */
    public static function fromFiles(array $files): Collection
    {
        $collection = collect();
        foreach ($files as $file) {
            $collection->push(ArchiveDocument::create([
                'filename' => $file->getClientOriginalName(),
                'path' => $file->store('archive'),
                'size' => $file->getSize(),
            ]));
        }

        return $collection;
    }

    /**
     * @param int[] $ids
     * @return bool
     */
    public static function deleteWithFiles(array $ids): bool
    {
        self::whereIn('id', $ids)
            ->select('path')
            ->each(function (ArchiveDocument $doc) {
                Storage::delete($doc->path);
            });

        return self::whereIn('id', $ids)->delete();
    }
}
