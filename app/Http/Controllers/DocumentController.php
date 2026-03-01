<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{


    public function download(Document $document)
    {
        $this->authorize('download', $document);
        // Log::debug("Downloading document");

        // Download document
        return Storage::download($document->filepath, $document->origfilename);


    }


}
