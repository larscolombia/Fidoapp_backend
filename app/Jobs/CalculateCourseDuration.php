<?php

namespace App\Jobs;

use App\Models\CoursePlatformVideo;
use App\Models\CursoPlataforma;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateCourseDuration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $course;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($course)
    {
        $this->course = $course;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $courseDuration = 0;
        $coursePlatformVideos = CoursePlatformVideo::where('course_platform_id', $this->course->id)->get();
        foreach ($coursePlatformVideos as $video) {
            list($hours, $minutes, $seconds) = explode(':', $video->duration);
            $courseDuration += ($hours * 3600) + ($minutes * 60) + $seconds;
            // Convertir la duración total a formato H:i:s
            $courseDurationFormat = gmdate("H:i:s", $courseDuration);
        }
        CursoPlataforma::where('id', $this->course->id)->update(['duration' => $courseDurationFormat]);
    }
}
