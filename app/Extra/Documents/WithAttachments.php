<?php

namespace App\Extra\Documents;

use App\Models\ArchiveDocument;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

trait WithAttachments {
    public function attachments(): Builder
    {
        return ArchiveDocument::whereIn('id',
            DB::table('document_set_lists')
                ->where('set_id', $this->document_set_id)
                ->select('set_id')
        );
    }
}
