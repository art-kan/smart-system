<?php

namespace App\Extra\Documents;

use App\Models\ArchiveDocument;
use App\Models\DocumentSet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use function Illuminate\Events\queueable;
use function PHPUnit\Framework\throwException;

trait WithAttachments
{
    public function documentSet()
    {
        return $this->belongsTo(DocumentSet::class);
    }

    public function getAttachments(): Collection
    {
        return $this->documentSet ? $this->documentSet->documents : collect();
    }

    /**
     * USAGE NOTE: run `.save()` afterward.
     * @param UploadedFile[] $files
     * @return bool - if `.save()` should be called
     */
    public function attachUploadedFiles(array $files = null): bool
    {
        if ($files) {
            if (is_null($this->document_set_id)) {
                $this->document_set_id = DocumentSet::fromFiles($files)->id;
                return true;
            } else {
                $this->documentSet->documents()->attach(ArchiveDocument::fromFiles($files)->pluck('id'));
            }
        }

        return false;
    }

    /**
     * @param ?int[] $ids
     */
    public function detachAttachments($ids = null)
    {
        if ($ids && isset($this->document_set_id)) {
            $this->documentSet->documents()->detach($ids);
            ArchiveDocument::deleteWithFiles($ids);
        }
    }
}
