<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Unit;
use App\DataProcessing;
use Illuminate\Support\Str;

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
        ]);

        $pdfUrl = null;
        $tags = [];
        if ($request->input('type') == 'notes') {

            if ($request->input('pdf_content') == '<p></p>') {
                return response()->json(['error' => 'Notes cannot be empty.'], 422);
            }

            $snake_title = preg_replace('/\s+/', '_', $request->input('title')); // Replace spaces with underscores
            $snake_title = preg_replace('/[^a-zA-Z0-9]/', '_', $snake_title); // Replace non-alphanumeric characters with underscores
            $snake_title = preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $snake_title); // Insert underscores before uppercase letters
            strtolower($snake_title);

            $pdfFilePath = public_path('pdf/' . $snake_title . '.pdf');

            $string = preg_replace('/[^A-Za-z ]/', '', strip_tags($request->input('pdf_content')));
            $string = Str::replace('gtgtgt', '', $string);

            // Array to store words from each iteration
            $wordsCount = [];

            // Run topic modeling for multiple iterations
            $it = 10; // Number of iterations
            for ($i = 0; $i < $it; $i++) {
                $result = DataProcessing::topicModeling($string); // Execute topic modeling for each iteration

                // Count the occurrence of words in each iteration
                foreach ($result as $word) {
                    if (!isset($wordsCount[$word])) {
                        $wordsCount[$word] = 1;
                    } else {
                        $wordsCount[$word]++;
                    }
                }
            }

            // Filter words that appeared more than once
            $tags = array_keys(array_filter($wordsCount, function ($count) use ($it) {
                return $count > ($it / 3);
            }));

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
            'tags' => json_encode($tags),
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
