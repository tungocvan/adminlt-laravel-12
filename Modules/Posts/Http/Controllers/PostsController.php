<?php

namespace Modules\Posts\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Phone;
use App\Models\User;
use App\Notifications\PostApproved;
use Illuminate\Http\RedirectResponse;
use App\Events\PostCreate;
// use App\Events\MessageSent;


// use App\Events\PostCreate;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:posts-list|posts-create|posts-edit|posts-delete', ['only' => ['index','show']]);
         $this->middleware('permission:posts-create', ['only' => ['create','store']]);
         $this->middleware('permission:posts-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:posts-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
  
        
        $posts = Post::paginate(15);        
       
        return view('Posts::posts',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Posts::postsCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required'
       ]);
  
       $post = Post::create([
           'user_id' => auth()->id(),
           'title' => $request->title,
           'body' => $request->body
       ]);
       
       event(new PostCreate($post));
       return back()->with('success','Post created successfully.');
    }

    public function storePost(Request $request): RedirectResponse
    {
        $this->validate($request, [
             'title' => 'required',
             'body' => 'required'
        ]);
        
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => auth()->id(),
        ]);
        
        return back()
                ->with('success','Post created successfully.');
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
    public function approve(Request $request, $id)
    {
        if (!auth()->user()->is_admin) {
            return back()->with('success', 'you are not super admin.');
        }

        $post = Post::find($id);

        if ($post && !$post->is_approved) {
            $post->is_approved = true;
            $post->save();

            // Notify the user
            $post->user->notify(new PostApproved($post));

            return back()->with('success','Post approved and user notified.');
        }

        return back()->with('success', 'Post not found or already approved.');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = auth()->user()->unreadNotifications->find($id);
        $notification->markAsRead();

        return back()->with('success', 'Added Mark as read.');
    }
}
