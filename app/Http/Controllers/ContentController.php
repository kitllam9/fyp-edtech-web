<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Tag;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Unit;
use App\DataProcessing;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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
            ],
            'tags' => json_encode(Tag::pluck('name')),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContentRequest $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'type' => 'required|string|in:notes,exercise',
        ]);

        $tags = [];

        $pdfUrl = null;
        if ($request->input('type') == 'notes') {

            if ($request->input('pdf_content') == '<p></p>') {
                return response()->json(['error' => 'Notes cannot be empty.'], 422);
            }

            $snake_title = snakeTitle($request->input('title'));

            $string = preg_replace('/[^A-Za-z ]/', '', strip_tags($request->input('pdf_content')));
            $string = Str::replace('gtgtgt', '', $string);
            $tags = $this->iteratedLda($string, 10);

            $pdfId = DB::select("SHOW TABLE STATUS LIKE 'content'")[0]->Auto_increment;
            $dir = 'app/public/pdf/' . $pdfId . '/';
            File::makeDirectory(storage_path('app/public/pdf/' . $pdfId));

            $pdfFilePath = storage_path($dir . $snake_title . '.pdf');
            $htmlFilePath = storage_path($dir . $snake_title . '.txt');

            File::put($htmlFilePath, $request->input('pdf_content'));

            // Generate PDF from the temporary Blade view
            Pdf::View('content.temp', ['content' => $request->input('pdf_content')])
                ->margins(64, 64, 64, 64, Unit::Pixel)
                ->save($pdfFilePath);

            // Get the URL of the saved PDF file
            $pdfUrl = url('storage/pdf/' . $pdfId . '/' . basename($pdfFilePath));
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

        $inputTags = array_map(function ($item) {
            return $item['value'];
        }, json_decode($request->input('tags'), true));

        $mergedTagArray = array_values(array_unique(array_merge($tags, $inputTags)));

        Content::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'pdf_url' => $pdfUrl,
            'exercise_details' => $exerciseDetailsJson,
            'tags' => json_encode($mergedTagArray),
        ]);

        Tag::insertOrIgnore(
            collect($mergedTagArray)->map(function ($item, $key) {
                return ['name' => $item];
            })->all()
        );

        return redirect()->route('content');
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
        $defaultTags = json_encode(array_map(function ($item) {
            return ['value' => $item];
        }, json_decode($content->tags)));

        if ($content->pdf_url) {
            $pdfContent = File::get(storage_path('app\public\pdf\\' . $content->id . '\\' . snakeTitle($content->title) . '.txt'));
        }

        return view('content.edit', [
            'content' => $content,
            'default_content_type' => $content->pdf_url ? 'notes' : 'exercise',
            'tags' => json_encode(Tag::pluck('name')),
            'default_tags' => $defaultTags,
            'pdf_content' => $content->pdf_url ? $pdfContent : '',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContentRequest $request, Content $content)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'type' => 'required|string|in:notes,exercise',
            'difficulty' => 'required|string|in:easy,medium,advanced',
        ]);

        $tags = [];

        $pdfUrl = null;
        if ($request->input('type') == 'notes') {

            if ($request->input('pdf_content') == '<p></p>') {
                return response()->json(['error' => 'Notes cannot be empty.'], 422);
            }

            $snake_title = snakeTitle($request->input('title'));

            if ($request->input('regenerate_tags')) {
                $string = preg_replace('/[^A-Za-z ]/', '', strip_tags($request->input('pdf_content')));
                $string = Str::replace('gtgtgt', '', $string);
                $tags = $this->iteratedLda($string, 10);
            }

            $pdfId = $content->id;
            $dir = 'app/public/pdf/' . $pdfId . '/';

            $files = File::allFiles(storage_path('app/public/pdf/' . $pdfId));

            foreach ($files as $file) {
                File::delete($file);
            }

            $pdfFilePath = storage_path($dir . $snake_title . '.pdf');
            $htmlFilePath = storage_path($dir . $snake_title . '.txt');

            File::put($htmlFilePath, $request->input('pdf_content'));

            // Generate PDF from the temporary Blade view
            Pdf::View('content.temp', ['content' => $request->input('pdf_content')])
                ->margins(64, 64, 64, 64, Unit::Pixel)
                ->save($pdfFilePath);

            $pdfUrl = url('storage/pdf/' . $pdfId . '/' . basename($pdfFilePath));
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

        $inputTags = array_map(function ($item) {
            return $item['value'];
        }, json_decode($request->input('tags'), true));

        $mergedTagArray = array_values(array_unique(array_merge($tags, $inputTags)));

        $content->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'pdf_url' => $pdfUrl,
            'exercise_details' => $exerciseDetailsJson,
            'tags' => json_encode($mergedTagArray),
            'points' => $request->input('points'),
            'difficulty' => $request->input('difficulty'),
        ]);

        Tag::insertOrIgnore(
            collect($mergedTagArray)->map(function ($item, $key) {
                return ['name' => $item];
            })->all()
        );

        $this->checkUnusedTags();

        return redirect()->route('content');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        if ($content->pdf_url) {
            $filePath = storage_path('app/public/pdf/' . $content->id);
            File::deleteDirectory($filePath);
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

    private function checkUnusedTags()
    {
        $contents = Content::all();

        // Extract all tags into a single collection using Laravel collection methods
        $allTags = $contents->flatMap(function ($content) {
            return json_decode($content->tags, true);
        })->unique();

        Tag::whereNotIn('name', $allTags)->delete();
    }
}
