<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use PhpParser\Node\Expr\PostDec;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;



class PostsController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }


    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $posts= Post::all(); // to get all function
        // return Post::where('name','Post Two')->get(); // where funcrion
        // $posts= DB:: select('SELECT * FROM posts'); //direct SQL
        // $posts= Post::orderBy('name','ASC')->take(1)->get();
        // $posts = Post::latest()->paginate(6);

        $posts= Post::orderBy('name','ASC')->paginate(8);
        return view('posts.index')->with('posts',$posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=> 'required',
            'body'=>   'required'
        ]);

       
        $user= User::all();
        $post = new Post;
        $post->name = $request->input('name');
        $post->body = $request->input('body');
        $post->user_id = Auth::user()->id;
        $post->post_category = Auth::user()->id;
        $post->save();

        return redirect('/posts')->with('success','Post Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post= Post::find($id);
        return view('posts.show')->with('post',$post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post= Post::find($id);

        if(Auth::user()->id !== $post->user_id){
            return redirect('/posts')->with('error','Unauthorized User');
        }
        return view('posts.edit')->with('post',$post);

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post= Post::find($id);
        $post->name = $request->input('name');
        $post->body = $request->input('body');
        $post->save();

        return redirect('/posts')->with('success','Post Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post= Post::find($id);
        if(Auth::user()->id !== $post->user_id){
            return redirect('/posts')->with('error','Unauthorized User');
        }
        $post->delete();
        return redirect('/dashboard')->with('success','Post Deleted');
    }
}
