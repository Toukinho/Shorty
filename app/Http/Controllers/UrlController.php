<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'URLs retrieved successfully',
            'data' => Url::all(),
        ], 201);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048', 'unique:urls,url'],
        ]);
        $url = Url::create($validated);

        return response()->json([
            'message' => 'URL created successfully',
            'data' => 
            [
                'original_url' => $url->url,
                'short_url' => url($url->short_url),
                'access_count' => $url->access_count, 
            ],
        ], 201);
    }

    public function show(Url $url)
    {
        return response()->json([
            'message' => 'URL retrieved successfully',
            'data' => 
            [
                'original_url' => $url->url,
                'short_url' => url($url->short_url),
                'access_count' => $url->access_count, 
            ],
        ], 201);
    }

    public function update(Request $request, Url $url)
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048', 'unique:urls,url'],
        ]);

        $url->update($validated);
        return response()->json([
            'message' => 'URL updated successfully',
            'data' => 
            [
                'original_url' => $url->url,
                'short_url' => url($url->short_url),
                'access_count' => $url->access_count, 
            ],
        ], 201);
    }

    public function destroy(Url $url)
    {
        Url::destroy($url->id);

        return response()->json([
            'message' => 'URL deleted successfully',
        ], 201);
    }
}