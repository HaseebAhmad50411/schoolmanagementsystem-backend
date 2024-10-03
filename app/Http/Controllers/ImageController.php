<?php

namespace App\Http\Controllers;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreImageRequest;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $image = Image::all();
        return $image;
    }

    public function store(StoreImageRequest $request, Image $image)
    {
        $validated = $request->validated();

        $file = Storage::disk('public')->put('/images', $request->file('url'));


       $image =  Image::create(array_merge($validated, [
            'url' => $file
        ]));

        return response()->json([ 'image' => $image , 'message' => 'File stored successfully.'], 201);
    }
}
