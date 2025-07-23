<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
    
class CkeditorController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(): View
    {
        
        return view('ckeditor');
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function upload(Request $request): JsonResponse
    {
        
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;
      
            $request->file('upload')->move(public_path('media'), $fileName);
      
            $url = asset('media/' . $fileName);
  
            return response()->json(['fileName' => $fileName, 'uploaded'=> 1, 'url' => $url]);
        }
    }
    public function store(Request $request)
    {
        dd($request->all());
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'nullable|string|max:255',
            'body'  => 'required|string',
        ]);

        // Nếu bạn có model Post:
        Post::create($data);

        return redirect()->back()->with('success', 'Post created successfully!');
    }

}