<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

use function PHPUnit\Framework\fileExists;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search()
    {
        return $this->success(data: Content::paginate(10));
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

            $filePath = storage_path('app/public/pdf/' . $fileName);

            if (fileExists($filePath)) {
                $file = File::get($filePath);
                $response = Response::make($file, 200);
                $response->header('Content-Type', 'application/pdf');
                return $response;
            }
        }
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
