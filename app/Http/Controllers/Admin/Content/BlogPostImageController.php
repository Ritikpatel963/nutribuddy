<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\UploadBlogPostImageRequest;
use Illuminate\Support\Facades\Storage;

class BlogPostImageController extends Controller
{
    public function upload(UploadBlogPostImageRequest $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;
            $request->file('upload')->storeAs('public/blog-images', $fileName);

            return response()->json([
                'uploaded' => 1,
                'fileName' => $fileName,
                'url' => Storage::url('blog-images/' . $fileName),
            ]);
        }

        return response()->json([
            'uploaded' => 0,
            'error' => ['message' => 'No image uploaded'],
        ]);
    }
}
