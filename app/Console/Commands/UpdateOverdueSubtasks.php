<?php

namespace App\Console\Commands;

use App\Http\Controllers\DeadlineController;
use Illuminate\Console\Command;

class UpdateOverdueSubtasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-overdue-subtasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new DeadlineController();
        $response = $controller->updateOverdue();

        $data = $response->getData(true);
        $this->info($data['message'] ?? 'Cập nhật xong.');
    }
}
