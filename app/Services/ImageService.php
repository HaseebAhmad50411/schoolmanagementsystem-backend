<?php

namespace App\Services;

use App\Models\User;
use App\Models\Image;
use App\Models\Student;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreImageRequest;
use App\Services\Interfaces\UserInterface;

class ImageService
{
    public function updateImage($imageId, $imageableId, $imageableType)
    {
            $image = Image::findOrFail($imageId);
            $image->update([
                'imageable_id' => $imageableId,
                'imageable_type' => $imageableType,
            ]);
    }



}
