<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;
use ZipArchive;
class VinawebappController extends Controller
{

    public function createBackup()
    {
        try {
            // 1. Tạo timestamp dùng cho tất cả các file
            $timestamp = Carbon::now()->format('Ymd_His');

            // 2. Tạo thư mục backup nếu chưa tồn tại
            $backupPath = storage_path('app/public/uploads/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // 3. Đường dẫn cho file SQL
            $sqlFile = "$backupPath/db_backup_$timestamp.sql";

            // 4. Xuất database ra file SQL
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST');

            $command = sprintf(
                'mysqldump -h%s -u%s -p%s %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($sqlFile)
            );

            $output = [];
            $result = null;
            exec($command, $output, $result);
            if ($result !== 0) {
                throw new \Exception('Database backup failed: ' . implode("\n", $output));
            }

            // 5. Đường dẫn cho file ZIP chứa cả storage và SQL
            $zipFile = "$backupPath/backup_$timestamp.zip";
            $this->zipBackupFiles(storage_path('app/public'), $sqlFile, $zipFile);

            // 6. Trả về đường dẫn tải file ZIP cho frontend
            $backupUrl = Storage::url("uploads/backups/backup_$timestamp.zip");

            return response()->json(['backup_url' => $backupUrl], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Hàm nén cả thư mục storage và file SQL vào file ZIP
    private function zipBackupFiles($storagePath, $sqlFile, $zipFile)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('ZIP extension is not loaded.');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Could not create ZIP file.');
        }

        // Thêm các file trong thư mục storage vào ZIP
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($storagePath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($storagePath) + 1);
                $zip->addFile($filePath, "storage/$relativePath");
            }
        }

        // Thêm file SQL vào ZIP
        $zip->addFile($sqlFile, 'database_backup.sql');

        $zip->close();
    }
    function changeStatus(Request $request)
    {
        DB::table($request->tb)
            ->where('id', $request->id)
            ->update(['is_show' => DB::raw('IF(is_show = 1, 0, 1)')]);
        return response()->json('Oke');
    }
    function changeHighlight(Request $request)
    {
        DB::table($request->tb)
            ->where('id', $request->id)
            ->update(['highlight' => $request->highlight]);
        return response()->json('Oke');
    }


    function changeORD(Request $request)
    {
        DB::table($request->tb)
            ->where('id', $request->id)
            ->update(['ord' => $request->value]);
        return response()->json('Update ORD Success');
    }


}
