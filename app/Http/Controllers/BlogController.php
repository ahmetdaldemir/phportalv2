<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Services\Blog\BlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    private BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    protected function index()
    {
        $data['blogs'] = $this->blogService->all();
        return view('module.blog.index', $data);
    }

    protected function create()
    {
        return view('module.blog.form');
    }

    protected function edit(Request $request)
    {
        $data['blogs'] = $this->blogService->find($request->id);
        dd($data);
        return view('module.blog.form', $data);
    }

    protected function delete(Request $request)
    {
        $this->blogService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        if (empty($request->id)) {
            $blog = new Blog();
        } else {
           $blog = Blog::find($request->id);
        }

        $blog->title = $request->title;
        $blog->slug = Str::slug($request->slug);
        $blog->description = $request->description;
        $blog->image = $blog->uploadFile($request->image);
        $blog->meta_title = $request->meta_title;
        $blog->meta_description = $request->meta_description;
        $blog->labels = $request->labels;
        $blog->save();
        return redirect()->route('blog.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->blogService->update($request->id, $data);
    }
}
