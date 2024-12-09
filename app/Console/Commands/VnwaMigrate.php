<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VnwaMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vnwa:migrate
    {action : The action to perform (migrate, rollback, refresh, reset, status)}
    {--step=0 : Number of migrations to rollback/refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database migration actions for all connections';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databases = ['es_db', 'en_db']; // Danh sách các database connections
        $action = $this->argument('action'); // Lấy hành động từ tham số
        $step = $this->option('step'); // Lấy tham số step (nếu có)

        $validActions = ['migrate', 'rollback', 'refresh', 'reset', 'status'];

        if (!in_array($action, $validActions)) {
            $this->error("Invalid action. Valid actions are: " . implode(', ', $validActions));
            return;
        }

        foreach ($databases as $database) {
            $this->info("Performing '$action' on connection: $database");

            $options = ['--database' => $database, '--force' => true];

            // Thêm step vào rollback/refresh nếu cần
            if (in_array($action, ['rollback', 'refresh']) && $step > 0) {
                $options['--step'] = $step;
            }

            // Gọi lệnh tương ứng
            $this->call("migrate:$action", $options);
        }

        $this->info("All '$action' actions completed successfully for all connections!");
    }
}
