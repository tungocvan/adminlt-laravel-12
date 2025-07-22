<?php

namespace Modules\Upload\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
// use Spatie\Image\Image;
use Intervention\Image\Laravel\Facades\Image;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:upload-list|upload-create|upload-edit|upload-delete', ['only' => ['index','show']]);
         $this->middleware('permission:upload-create', ['only' => ['create','store']]);
         $this->middleware('permission:upload-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:upload-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('Upload::upload');
    }
    public function imageResize()
    {
        return view('Upload::imageUpload');
    }


    public function storeImageResize(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
   
        $image = $request->file('image');
        $imageName = time().'.'.$image->extension();
       
        $destinationPathThumbnail = public_path('images/thumbnail');
        $img = Image::read($image->path());
        $img->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail.'/'.$imageName);
     
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $imageName);
     
        return back()->with('success', 'Image Uploaded successfully!')
                     ->with('imageName', $imageName);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
