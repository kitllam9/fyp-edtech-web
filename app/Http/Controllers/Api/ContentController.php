<?php

namespace App\Http\Controllers\Api;

use App\DataProcessing;
use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\History;
use App\Models\Recommendation;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Pagination\LengthAwarePaginator;

use Tigo\Recommendation\Recommend;

use function PHPUnit\Framework\fileExists;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {
        $default = Content::whereNotIn('id', function ($query) use ($request) {
            $query->select('content_id')
                ->from('histories')
                ->where('user_id', $request->user()->id);
        });

        $query = $request->query('keyword');
        if ($query) {
            $results = $default->where(function ($subQuery) use ($query) {
                $subQuery->where('title', 'LIKE', '%' . $query . '%')
                    ->orWhere('tags', 'LIKE', '%' . $query . '%')
                    ->orWhere('description', 'LIKE', '%' . $query . '%');
            });

            return $this->success(
                data: $results->latest('updated_at')->paginate(15),
            );
        }

        if (Recommendation::exists()) {
            $client = new Recommend();
            $rank = $client->euclidean(Recommendation::all()->toArray(), $request->user()->id);
            $tags = Tag::whereIn('id', array_keys($rank));

            $tagNames = $tags->pluck('name')->toArray();

            if (!empty($tagNames)) {
                // Create the WHERE LIKE clause for the tag names
                $likeConditions = collect($tagNames)->map(function ($name) {
                    return "tags LIKE '%" . $name . "%'";
                })->implode(' OR ');


                // Retrieve records from OtherTable where the names are in the JSON encoded string
                $recommendations = Content::whereRaw($likeConditions)->get();

                // Assign the values in $rank to the fetched records
                $recommendations->each(function ($content) use ($rank) {
                    $contentTags = $content->tag_ids;
                    $score = 0;

                    foreach ($contentTags as $tag) {
                        if (array_key_exists($tag, $rank)) {
                            $score += $rank[$tag];
                        }
                    }

                    $content->recommendation_score = $score / count($contentTags);
                });

                $latestUnviewed = $default->get();
                $recommendations = collect($recommendations);
                $merged = $latestUnviewed->merge($recommendations);
                $sorted = $merged->sortByDesc('recommendation_score');

                $perPage = 15;
                $page = LengthAwarePaginator::resolveCurrentPage('page');

                $paginatedData = new LengthAwarePaginator($sorted->forPage($page, $perPage)->values(), $sorted->count(), $perPage, $page, [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'query' => request()->query(),
                ]);

                return $this->success(
                    data: $paginatedData,
                );
            }
        }

        return $this->success(
            data: $default->latest('updated_at')->paginate(15),
        );
    }

    public function complete(Request $request, int $id)
    {
        $content = Content::find($id);
        $tags = json_decode($content->tags);
        $user = $request->user();

        // Create recommendation data
        foreach ($tags as $name) {
            $tag = Tag::where('name', $name)->first();
            Recommendation::insertOrIgnore([
                'product_id' => $tag->id,
                'score' => 1,
                'user_id' => $user->id,
            ]);
        }

        // Create user interests
        $interests = array_unique(array_merge($user->interests ?? [], $tags));
        sort($interests);
        $user->update([
            'interests' => $interests,
        ]);

        History::create([
            'content_id' => $id,
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        History::where('content_id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'bookmarked')
            ->delete();

        DataProcessing::userClustering();
        return $this->success(message: 'Completed');
    }

    public function bookmark(Request $request, int $id)
    {

        $history = History::firstOrCreate([
            'content_id' => $id,
            'user_id' => $request->user()->id,
            'status' => 'bookmarked',
        ]);

        if ($history->wasRecentlyCreated) {
            return $this->success(message: 'Content is added to your bookmarks!');
        } else {
            $history->delete();
            return $this->success(message: 'Content is removed from your bookmarks!');
        }
    }

    public function getBookmarks(Request $request)
    {
        $contentIds = History::where('user_id', $request->user()->id)
            ->where('status', 'bookmarked')
            ->pluck('content_id');
        $content = Content::whereIn('id', $contentIds)->latest('updated_at')->paginate(15);
        return $this->success(data: $content);
    }

    /**
     * TEMPORARY
     * Use the actual url when deployed
     */
    public function getPdf(int $id)
    {
        $content = Content::find($id);
        if ($content->pdf_url) {
            $path = parse_url($content->pdf_url, PHP_URL_PATH);
            $fileName = basename($path);

            $filePath = storage_path('app/public/pdf/' . $id . '/' . $fileName);

            if (fileExists($filePath)) {
                $file = File::get($filePath);
                $response = Response::make($file, 200);
                $response->header('Content-Type', 'application/pdf');
                return $response;
            }
        }
    }

    public function grade(Request $request)
    {

        // return $this->success();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
