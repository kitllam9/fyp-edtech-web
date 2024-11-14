<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Unit;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('content.index', ['content' => Content::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.create', [
            'types' => [
                'notes' => 'Notes',
                'exercise' => 'Exercise'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContentRequest $request)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'type' => 'required|string|in:notes,exercise',
            'pdf_content' => $request->input('type') == 'article' ? 'required|string' : '',
        ]);

        if ($request->input('pdf_content')) {
            $snake_title = preg_replace('/\s+/', '_', $request->input('title')); // Replace spaces with underscores
            $snake_title = preg_replace('/[^a-zA-Z0-9]/', '_', $snake_title); // Replace non-alphanumeric characters with underscores
            $snake_title = preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $snake_title); // Insert underscores before uppercase letters
            strtolower($snake_title);

            $pdfFilePath = public_path('pdf/' . $snake_title . '.pdf');

            // Generate PDF from the temporary Blade view
            Pdf::View('content.temp', ['content' => $request->input('pdf_content')])
                ->margins(64, 64, 64, 64, Unit::Pixel)
                ->save($pdfFilePath);


            // Get the URL of the saved PDF file
            $pdfUrl = asset('pdf/' . $snake_title . '.pdf');
        }

        Content::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'pdf_url' => $pdfUrl,
        ]);

        return redirect()->route('content.create');
    }

    public function temp(Request $request)
    {
        return view('content.temp', [
            'content' => $request->input('content'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $content)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContentRequest $request, Content $content)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        //
    }
}
