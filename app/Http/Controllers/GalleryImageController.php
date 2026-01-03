<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class GalleryImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_public' => 'required|boolean'
        ]);

        foreach ($request->file('images') as $file) {
            $path = $file->store('gallery', 'public');
            
            Auth::user()->images()->create([
                'category_id' => $request->category_id,
                'path' => $path,
                'is_public' => $request->is_public,
                'share_token' => $request->is_public ? Str::random(20) : null,
                'name' => $file->getClientOriginalName(),
            ]);
        }

        return back()->with('success', 'Images uploaded successfully.');
    }

    public function show($token)
    {
        $image = GalleryImage::where('share_token', $token)->where('is_public', true)->firstOrFail();

        // Log Visitor Data
        try {
            $ip = request()->ip();
            $userAgent = request()->header('User-Agent');
            $referrer = request()->header('referer');
            
            // Simple location lookup (can be improved with a package or API)
            // For now, we'll just log the IP and let the dashboard handle visualization
            $image->visitorLogs()->create([
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'referrer' => $referrer,
                // These could be populated by an IP-Location service
                'country' => 'Unknown', 
                'city' => 'Unknown',
            ]);
        } catch (\Exception $e) {
            // Silently fail to not interrupt user experience
            \Log::error("Visitor tracking error: " . $e->getMessage());
        }

        return view('images.public', compact('image'));
    }

    public function destroy(GalleryImage $image)
    {
        if (Auth::id() !== $image->user_id) {
            abort(403);
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image deleted successfully.');
    }

    public function logs(GalleryImage $image)
    {
        if (Auth::id() !== $image->user_id) {
            abort(403);
        }

        $logs = $image->visitorLogs()->latest()->paginate(20);
        return view('images.logs', compact('image', 'logs'));
    }

    public function allLogs(Request $request)
    {
        $query = Auth::user()->images()->with('visitorLogs')->get()->pluck('visitorLogs')->flatten();
        
        // Let's do it better using the VisitorLog model and filtering by user's images/categories
        $imageIds = Auth::user()->images->pluck('id');
        $categoryIds = Auth::user()->categories->pluck('id');

        $logs = \App\Models\VisitorLog::whereIn('gallery_image_id', $imageIds)
            ->orWhereIn('category_id', $categoryIds)
            ->latest()
            ->when($request->category_id, function($q) use ($request) {
                return $q->where('category_id', $request->category_id);
            })
            ->when($request->image_id, function($q) use ($request) {
                return $q->where('gallery_image_id', $request->image_id);
            })
            ->paginate(30);

        return view('analytics.index', compact('logs'));
    }
}
