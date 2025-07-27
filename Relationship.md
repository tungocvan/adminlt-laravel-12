- One to One Eloquent Relationship Tutorial
    Users (one) => Phones (one)
    Mode: User, Phone

    $user = User::find(1);
    $phone = new Phone;
    $phone->phone = '9429343852';         
    $user->phone()->save($phone);

     $phone = User::find(1)->phone;  
     dd($phone);

     $user = Phone::find(1)->user;
     dd($user);

     $phone = Phone::find(1);   
     $user = User::find(10);           
     $phone->user()->associate($user)->save();

- One to Many Eloquent Relationship Tutorial
    posts (one) => comments (many)
    Model: Post, Comment
    // Model post:
       $post = Comment::find(1)->post;

       // Model comment 
       $post = Post::find(1);   // 1 : id post
       $comment = new Comment;
       $comment->comment = "Hi ItSolutionStuff.com";           
       $post = $post->comments()->save($comment);
       
       $post = Post::find(1);   
        $comment1 = new Comment;
        $comment1->comment = "Hi ItSolutionStuff.com Comment 1";           
        $comment2 = new Comment;
        $comment2->comment = "Hi ItSolutionStuff.com Comment 2";           
        $post = $post->comments()->saveMany([$comment1, $comment2]);


        $comment = Comment::find(1);
        $post = Post::find(2);           
        $comment->post()->associate($post)->save();

- Many to Many Eloquent Relationship Tutorial
role_user
Users (many) => role_user (bảng trung gian) <= Roles(many)
Model: User, Role, UserRole

$user = User::find(1);   
dd($user->roles);

$role = Role::find(1);   
dd($role->users)

Create Records:

$user = User::find(2);   
$roleIds = [1, 2];
$user->roles()->attach($roleIds);

$user = User::find(3);   
$roleIds = [1, 2];
$user->roles()->sync($roleIds);

$role = Role::find(1);   
$userIds = [10, 11];
$role->users()->attach($userIds);

$role = Role::find(2);   
$userIds = [10, 11];
$role->users()->sync($userIds);
