<?php

namespace App\Livewire\Upload;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class UploadImage extends Component
{
    public function render()
    {
        return view('livewire.upload.upload-image');
    }
    public function store(Request $request): RedirectResponse
    {
        //dd($request->all());
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
          
        $imageName = time().'.'.$request->image->extension();  
           
        $request->image->move(public_path('images'), $imageName);
        
        /* 
            Write Code Here for
            Store $imageName name in DATABASE from HERE 
        */
          
        return back()->with('success', 'Image uploaded successfully!')
                     ->with('image', $imageName);
    }
}
