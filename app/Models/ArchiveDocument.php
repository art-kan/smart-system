<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
 */
class ArchiveDocument extends Model
{
    use HasFactory;
}
