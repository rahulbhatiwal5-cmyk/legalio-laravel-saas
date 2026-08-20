<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\HomeContent;

class SearchController extends Controller
{
    public function SearchResult($q = null )
    {
        $query        = trim($q ?? '');
        $perPage      = 22;

        $docsQuery = Document::where('published', 1);
        if ($query !== '') {
            $docsQuery->where(function ($q) use ($query) {
                $q->where('title',       'LIKE', "%{$query}%")
                    ->orWhere('slug',       'LIKE', "%{$query}%");
            });
        }

        $documents  = $docsQuery->paginate($perPage)->withQueryString();
        $totalCount = $documents->total();

        return view('users.search.results', compact(
            'documents',
            'query',
            'totalCount',
        ));
    }
}
