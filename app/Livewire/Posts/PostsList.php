<?php

namespace App\Livewire\Posts;

use Livewire\Component;
use App\Models\Post;

class PostsList extends Component
{
    public $posts, $title, $body, $post_id;
    
    public $updateMode = false;


    public function render()
    {
        $this->posts = Post::all();
        //$this->posts = Post::paginate(5);
        return view('livewire.posts.posts-list');
    }
    private function resetInputFields(){
        $this->title = '';
        $this->body = '';
    }
    public function store()
    {
        $validatedDate = $this->validate([
            'title' => 'required',
            'body' => 'required',
        ]); 
        $validatedDate['user_id'] = auth()->id();
        
        Post::create($validatedDate);
        session()->flash('message', 'Post Created Successfully.'); 
        $this->resetInputFields();

    }
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->title = $post->title;
        $this->body = $post->body; 
        $this->updateMode = true;
    }
    public function cancel()
    {
        $this->updateMode = false;
        $this->resetInputFields();
    }
    public function update()
    {
        $validatedDate = $this->validate([
            'title' => 'required',
            'body' => 'required',
        ]); 
        $post = Post::find($this->post_id);
        $post->update([
            'title' => $this->title,
            'body' => $this->body,
        ]); 
        $this->updateMode = false; 
        session()->flash('message', 'Post Updated Successfully.');
        $this->resetInputFields();
    }
    public function delete($id)
    {
        Post::find($id)->delete();
        session()->flash('message', 'Post Deleted Successfully.');
    }
}
 