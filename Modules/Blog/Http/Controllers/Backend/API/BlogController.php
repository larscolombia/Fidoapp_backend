<?php

namespace Modules\Blog\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use App\Models\BlogRating;
use Illuminate\Http\Request;
use Modules\Blog\Models\Blog;
use Modules\Blog\Transformers\BlogResource;

class BlogController extends Controller
{
    public function __construct() {}

    public function blogList(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Get the number of items per page from the request (default: 10)
        // $branchId = $request->input('branch_id');

        $blog = Blog::with('media')->where('status', 1);

        $blog = $blog->orderBy('updated_at', 'desc')->paginate($perPage);
        $items = BlogResource::collection($blog);

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('blog.blog_list'),
        ], 200);
    }

    public function show($id)
    {

        $blog = Blog::findOrFail($id);
        $blog->description = strip_tags($blog->description);
        return response()->json([
            'status' => true,
            'data' => $blog,
            'message' => __('blog.blog_list'),
        ], 200);
    }

    public function rating(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'review_msg' => 'nullable|string|max:255',
                'rating' => 'nullable|numeric|min:1|max:5',
                'blog_id' => 'required|exists:blogs,id',
            ]);

            if(is_null($data['rating'])){
                $data['rating'] = 1;
            }
            if($data['rating']>=3){
                $data['status'] = 1;
            }

            $blogRating = BlogRating::create($data);
            return response()->json([
                'success' => true,
                'data' => $blogRating,
                'message' => $blogRating->status == 0 ? __('messages.comment_review') : null
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la calificación.'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBlogRating(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'blog_id' => 'nullable|exists:blogs,id'
        ]);

        $blogRatings = BlogRating::query();
        if (isset($data['blog_id']) && $data['blog_id']) {
            $blogRatings->where('blog_id', $data['blog_id']);
        }

        if (isset($data['user_id']) && $data['user_id']) {
            $blogRatings->where('user_id', $data['user_id']);
        }

        $blogRatings = $blogRatings->where('status',1)->orderByDesc('id')->get();

        if ($blogRatings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'There are no ratings'
            ], 404);
        }
        // Formatear la respuesta para incluir solo los campos necesarios
        $formattedRatings = $blogRatings->map(function ($rating) {
            return [
                'id' => $rating->id,
                'rating' => $rating->rating,
                'review_msg' => $rating->review_msg,
                'status' => $rating->status,
                'user_full_name' => $rating->user->full_name,
                'user_avatar' => asset($rating->user->avatar),
                'blog_name' => $rating->blog->name,
                'blog_description' => strip_tags($rating->blog->description),
                'blog_tags' => $rating->blog->tags,
            ];
        });
        return response()->json([
            'success' => true,
            'data' =>  $formattedRatings
        ], 200);
    }

    public function updateVisualization($id)
    {
        try {
            // Buscar el video por ID
            $blog = Blog::findOrFail($id);

            // Incrementar el campo visualizations
            $blog->increment('visualizations');

            // Retornar una respuesta JSON
            return response()->json([
                'success' => true,
                'message' => 'Visualización actualizada correctamente',
                'visualizations' => $blog->visualizations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
