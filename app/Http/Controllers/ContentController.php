<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Unit;
use Illuminate\Support\Facades\Storage;

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
            'content_types' => [
                'notes' => 'Notes',
                'exercise' => 'Exercise'
            ],
            "question_types" => [
                'short' => 'Short Question',
                'mc' => 'Multiple Choice'
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
            'pdf_content' => $request->input('type') == 'notes' ? 'required|string' : '',
        ]);

        $pdfUrl = null;
        if ($request->input('type') == 'notes') {
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

        $exerciseDetailsJson = null;
        if ($request->input('type') == 'exercise') {
            $exerciseDetails = [];
            $questionList = $request->input('question');
            // Re-index questions to avoid missing indices
            $reindexedPayload = [];
            foreach ($questionList as $index => $question) {
                $type = $request->input('question_type')[$index];

                $reindexedPayload[$index] = [
                    'question' => $question,
                    'type' => $type,
                    'answer' => $request->input('answer')[$index],
                ];

                // If it's a multiple choice question, add the choices
                if ($type == 'mc') {
                    $mcInput = $request->input('mc')[$index];
                    $reindexedPayload[$index]['mc'] = [
                        $mcInput[0],
                        $mcInput[1],
                        $mcInput[2],
                        $mcInput[3]
                    ];
                    $reindexedPayload[$index]['answer'] = $request->input('answer_')[$index];
                }
            }

            // Loop through the re-indexed payload
            foreach ($reindexedPayload as $data) {
                $exerciseDetails[] = $data;
            }

            // Store the processed data as a JSON array
            $exerciseDetailsJson = json_encode($exerciseDetails);
        }

        Content::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'pdf_url' => $pdfUrl ? $pdfUrl : '',
            'exercise_details' => $exerciseDetailsJson ? $exerciseDetailsJson : '',
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
        if ($content->pdf_url) {
            $path = parse_url($content->pdf_url, PHP_URL_PATH);
            $segments = explode('/', $path);
            $fileToDelete = end($segments);

            $filePath = public_path("pdf" . "\\" . $fileToDelete);

            if (file_exists($filePath)) {
                // Delete the file
                unlink($filePath);
            }
        }

        $content->delete();
        return redirect()->route('content');
    }
}
