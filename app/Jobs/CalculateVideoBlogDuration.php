<?php

namespace App\Jobs;

use App\Helpers\Functions;
use Illuminate\Bus\Queueable;
use Modules\Blog\Models\Blog;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CalculateVideoBlogDuration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $blog;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($blog)
    {
        $this->blog = $blog;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $duration = 0;
        $videoPath = public_path('videos/blog/' . $this->blog->video);
        $duracionFormato = Functions::getVideoDuration($videoPath);
        list($hours, $minutes, $seconds) = explode(':', $duracionFormato);
        $duration += ($hours * 3600) + ($minutes * 60) + $seconds;
        // Convertir la duración total a formato H:i:s
        $durationFormat = gmdate("H:i:s", $duration);
        Blog::where('id',$this->blog->id)->update(['duration' => $durationFormat]);
    }
}
