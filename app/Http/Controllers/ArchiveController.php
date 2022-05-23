<?php

namespace App\Http\Controllers;

use App\Models\ArchiveDocument;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArchiveController extends Controller
{
    public function getFile($doc_id): StreamedResponse
    {
        $doc = ArchiveDocument::whereId($doc_id)->select(['path', 'filename'])->first();
        abort_if(is_null($doc), 404);
        return Storage::download($doc->path, $doc->filename);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files' => ['required', 'array'],
            'files.*' => ['file', 'max:16384'], // 16MiB
        ]);

        $nameToIdMap = [];

        /** @var UploadedFile $file */
        foreach ($request->file('files') as $file) {
            $id = ArchiveDocument::insertGetId([
                'filename' => $file->getBasename(),
                'size' => $file->getSize(),
                'path' => $file->store('archive'),
            ]);

            $nameToIdMap[$file->getClientOriginalName()] = $id;
        }

        return response($nameToIdMap);
    }
}
