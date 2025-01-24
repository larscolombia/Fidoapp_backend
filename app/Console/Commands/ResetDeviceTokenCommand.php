<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Jobs\ResetDeviceToken;
use Illuminate\Console\Command;

class ResetDeviceTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-device-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar device_token a null para usuarios que no han sido actualizados en las últimas 48 horas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::where('updated_at', '<', Carbon::now()->subHours(48))
        ->chunk(1000, function ($users) {
            dispatch(new ResetDeviceToken($users));
        });
    }
}
