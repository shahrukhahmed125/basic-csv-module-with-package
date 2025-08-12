<?php

namespace App\Http\Controllers;

use App\Exports\PostsExport;
use App\Imports\PostsImport;
use App\Models\Post;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PostCsvController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        Excel::import(new PostsImport, $request->file('csv_file'));

        return redirect()->back()->with('success', 'Posts imported successfully.');
    }

    public function export()
    {
        return Excel::download(new PostsExport, 'posts.csv');
    }
}
