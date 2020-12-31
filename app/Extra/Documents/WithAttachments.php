<?php

namespace App\Extra\Documents;

use App\Models\ArchiveDocument;
use App\Models\DocumentSet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
}
