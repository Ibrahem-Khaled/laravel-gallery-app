<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Auth::user()->categories()->whereNull('parent_id')->with('children')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        Auth::user()->categories()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'parent_id' => $request->parent_id
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        $category->load(['children', 'images']);
        return view('categories.show', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if (Auth::id() !== $category->user_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if (Auth::id() !== $category->user_id) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

    public function share(Category $category)
    {
        if (Auth::id() !== $category->user_id) {
            abort(403);
        }

        $category->update([
            'is_public' => !$category->is_public,
            'share_token' => $category->share_token ?? Str::random(20),
        ]);

        return back()->with('success', $category->is_public ? 'Category is now public!' : 'Category is now private.');
    }

    public function showPublic($token)
    {
        $category = Category::where('share_token', $token)->where('is_public', true)->with('images')->firstOrFail();

        // Track Visit
        $visitorLog = null;
        try {
            $visitorLog = $category->visitorLogs()->create([
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
                'referrer' => request()->header('referer'),
                'country' => 'Unknown',
                'city' => 'Unknown',
            ]);
        } catch (\Exception $e) {
            \Log::error("Category tracking error: " . $e->getMessage());
        }

        return view('categories.public', compact('category', 'visitorLog'));
    }
}
