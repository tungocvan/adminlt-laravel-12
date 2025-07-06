<?php

namespace App\Livewire\Upload;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Image;

class UploadImages extends Component
{
    public function render()
    {
        return view('livewire.upload.upload-images');
    }
    public function store(Request $request): RedirectResponse
    {
        // Validate incoming request data
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
  
        // Initialize an array to store image information
        $images = [];
  
        // Process each uploaded image
        foreach($request->file('images') as $image) {
            // Generate a unique name for the image
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
              
            // Move the image to the desired location
            $image->move(public_path('images'), $imageName);
  
            // Add image information to the array
            $images[] = ['name' => $imageName];
        }
  
        // Store images in the database using create method
        foreach ($images as $imageData) {
            Image::create($imageData);
        }
          
        return back()->with('success', 'Images uploaded successfully!')
                     ->with('images', $images); 
    }
}
