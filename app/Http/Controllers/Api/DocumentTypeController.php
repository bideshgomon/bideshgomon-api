<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of active document types.
     */
    public function index()
    {
        return DocumentType::where('is_active', true)->orderBy('name', 'asc')->get();
    }
}
