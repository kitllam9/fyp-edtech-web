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

        $tags = [];

        $pdfUrl = null;
        if ($request->input('type') == 'notes') {

            if ($request->input('pdf_content') == '<p></p>') {
                return response()->json(['error' => 'Notes cannot be empty.'], 422);
            }

            $snake_title = preg_replace('/\s+/', '_', $request->input('title')); // Replace spaces with underscores
            $snake_title = preg_replace('/[^a-zA-Z0-9]/', '_', $snake_title); // Replace non-alphanumeric characters with underscores
            strtolower($snake_title);

            // $pdfFilePath = public_path('pdf/' . $snake_title . '.pdf');
            $pdfFilePath = storage_path('app/public/pdf/' . $snake_title . '.pdf');;

            $string = preg_replace('/[^A-Za-z ]/', '', strip_tags($request->input('pdf_content')));
            $string = Str::replace('gtgtgt', '', $string);

            $tags = $this->iteratedLda($string, 10);

            // Generate PDF from the temporary Blade view
            Pdf::View('content.temp', ['content' => $request->input('pdf_content')])
                ->margins(64, 64, 64, 64, Unit::Pixel)
                ->save($pdfFilePath);


            // Get the URL of the saved PDF file
            $pdfUrl = url('storage/pdf/' . basename($pdfFilePath));
        }

        $exerciseDetailsJson = null;
        if ($request->input('type') == 'exercise') {
            $exerciseDetails = [];
            $questionList = $request->input('question');
            $mcList = $request->input('mc');
            $answerList = $request->input('answer');

            // Re-index questions to avoid missing indices
            $reindexedPayload = [];
            foreach ($questionList as $index => $question) {
                $type = $request->input('question_type')[$index];

                $reindexedPayload[$index] = [
                    'question' => $question,
                    'type' => $type,
                    'answer' => $answerList[$index],
                ];

                // If it's a multiple choice question, add the choices
                if ($type == 'mc') {
                    $mcInput = $mcList[$index];
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
            'pdf_url' => $pdfUrl,
            'exercise_details' => $exerciseDetailsJson,
            'tags' => json_encode(array_unique(array_merge($tags, $request->input('tags')))),
        ]);

        return redirect()->route('content.create');
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
            $fileToDelete = basename($path);

            $filePath = storage_path('app/public/pdf/' . $fileToDelete);

            if (file_exists($filePath)) {
                // Delete the file
                unlink($filePath);
            }
        }

        $content->delete();
        return redirect()->route('content');
    }

    public function temp(Request $request)
    {
        return view('content.temp', [
            'content' => $request->input('content'),
        ]);
    }

    private function iteratedLda(string $str, int $it)
    {
        // Array to store words from each iteration
        $wordsCount = [];

        // Run topic modeling for multiple iterations
        for ($i = 0; $i < $it; $i++) {
            $result = DataProcessing::topicModeling($str); // Execute topic modeling for each iteration

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
        return array_keys(array_filter($wordsCount, function ($count) use ($it) {
            return $count > ($it / 3);
        }));
    }
}
