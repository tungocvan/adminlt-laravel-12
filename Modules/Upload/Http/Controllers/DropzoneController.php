<?php

namespace Modules\Upload\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\Image;

class DropzoneController extends Controller
{
    public function __construct()
    {
         $this->middleware('permission:upload-list|upload-create|upload-edit|upload-delete', ['only' => ['index','show']]);
         $this->middleware('permission:upload-create', ['only' => ['create','store']]);
         $this->middleware('permission:upload-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:upload-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $images = Image::all();        
        return view('Upload::dropzone',compact('images'));
    }
    public function store(Request $request): JsonResponse
    {
        // Initialize an array to store image information
        $images = [];
  
        // Process each uploaded image
        foreach($request->file('files') as $image) {
            // Generate a unique name for the image
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
              
            // Move the image to the desired location
            $image->move(public_path('images'), $imageName);
  
            // Add image information to the array
            $images[] = [
                'name' => $imageName,
                'path' => asset('/images/'. $imageName),
                'filesize' => filesize(public_path('images/'.$imageName))
            ];
        }
  
        // Store images in the database using create method
        foreach ($images as $imageData) {
            Image::create($imageData);
        }
     
        return response()->json(['success'=>$images]);
    }
}
