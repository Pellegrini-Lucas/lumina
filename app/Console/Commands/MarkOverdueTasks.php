<?php

namespace App\Console\Commands;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Console\Command;

class MarkOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca como vencidas las tareas pendientes o en progreso que pasaron su fecha de vencimiento';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = Task::withoutGlobalScopes()
            ->whereIn('status', [TaskStatus::Pendiente, TaskStatus::EnProgreso])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->update(['status' => TaskStatus::Vencida]);

        $this->info("Se marcaron {$count} tareas como vencidas.");

        return Command::SUCCESS;
    }
}
