<?php

namespace Tests\Unit;

use App\Models\ArchiveDocument;
use App\Models\DocumentSet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

class DocumentSetModel extends TestCase
{
    public function testRelationWithDocuments()
    {
        /** @var DocumentSet $doc_set */
        $doc_set = DocumentSet::factory()->createOne();

        /** @var Collection|ArchiveDocument[] $docs */
        $docs = ArchiveDocument::factory()->count(5)->create();

        DB::table('document_set_lists')->delete();
        DB::table('document_set_lists')->insert(
            $docs->map(function (ArchiveDocument $doc) use ($doc_set) {
                return ['doc_id' => $doc->id, 'set_id' => $doc_set->id];
            })->toArray()
        );

        self::assertEquals(
            $docs->sortBy('id')->pluck('id')->toArray(),
            $doc_set->documents()->orderBy('id')->pluck('id')->toArray()
        );
    }
}
