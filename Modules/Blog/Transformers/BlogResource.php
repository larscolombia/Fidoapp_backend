<?php

namespace Modules\Blog\Transformers;

use App\Helpers\Functions;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $created_at = !is_null($this->created_at) ? Carbon::parse($this->created_at)->format('d-m-Y') : null;
        $updated_at = !is_null($this->updated_at) ? Carbon::parse($this->updated_at)->format('d-m-Y') : null;
        $deleted_at = !is_null($this->deleted_at) ? Carbon::parse($this->deleted_at)->format('d-m-Y') : null;
        $durationText = Functions::getDurationText($this->duration);
        return [
            'id' => $this->id,
            'description' =>  strip_tags($this->description),
            'name' => $this->name,
            'tags' => $this->tags,
            'status' => $this->status,
            'video' => $this->video,
            'url_video' => !is_null($this->url) ? $this->url : $this->video,
            'duration' => $this->duration,
            'duration_text' =>$durationText,
            'visualizations' => $this->visualizations,
            'blog_image' => $this->getFirstMediaUrl('blog_image'),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => !is_null($created_at) ? (string)$created_at : null,
            'updated_at' => !is_null($updated_at) ? (string)$updated_at : null,
            'deleted_at' => !is_null($deleted_at) ? (string)$deleted_at : null,
        ];
    }
}
