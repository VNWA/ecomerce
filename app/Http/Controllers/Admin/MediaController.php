<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;

class MediaController extends Controller
{
    public function uploadFiles(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file',
            'basePath' => 'nullable|string',
        ]);

        $paths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $basePath = $request->input('basePath') ? '/' . $request->input('basePath') : '';
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $fileName = $originalName . '.' . $extension;
                    $path = 'public/uploads' . $basePath . '/' . $fileName;

                    // Kiểm tra và thêm hậu tố nếu tệp đã tồn tại
                    $counter = 1;
                    while (Storage::exists($path)) {
                        $fileName = $originalName . '(' . $counter . ').' . $extension;
                        $path = 'public/uploads' . $basePath . '/' . $fileName;
                        $counter++;
                    }

                    $storedPath = $file->storeAs('public/uploads' . $basePath, $fileName);
                    $paths[] = Storage::url($storedPath); // Trả về URL công khai của tệp
                }
            }
            return response()->json(['message' => 'Uploads files thành công', 'paths' => $paths], 200);
        }
        return response()->json(['message' => 'Lỗi trong quá trình uploads files'], 400);
    }

    public function createDirectory(Request $request)
    {
        $request->validate([
            'directoryName' => 'required|string|max:255',
            'basePath' => 'nullable|string',
        ]);

        $directoryName = $request->input('directoryName');
        $basePath = trim($request->input('basePath', ''), '/');
        $fullPath = 'uploads/' . ($basePath ? $basePath . '/' : '') . $directoryName;

        if (Storage::disk('public')->exists($fullPath)) {
            return response()->json(['message' => 'Đã tồn tại thư mục này'], 400);
        }

        try {
            Storage::disk('public')->makeDirectory($fullPath);
            return response()->json(['message' => 'Khởi tạo thư mục thành công'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }


    public function loadDataFiles($basePath = '')
    {
        $items = [];

        // Lấy danh sách thư mục
        $directories = Storage::disk('public')->directories('uploads/' . $basePath);
        foreach ($directories as $directory) {
            $createdAt = Storage::disk('public')->lastModified($directory);
            $items[] = [
                'name' => basename($directory),
                'type' => 'dir',
                'path' => Storage::disk('public')->url($directory),
                'created_at' => Carbon::createFromTimestamp($createdAt)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::createFromTimestamp($createdAt)->format('Y-m-d H:i:s'),
            ];
        }

        // Lấy danh sách các tệp tin
        $files = Storage::disk('public')->files('uploads/' . $basePath);
        foreach ($files as $file) {
            $createdAt = Storage::disk('public')->lastModified($file);
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            // Xác định loại tệp tin dựa trên phần mở rộng
            $fileType = $this->getFileType($extension);

            $fileInfo = [
                'name' => basename($file),
                'type' => $fileType,
                'path' => Storage::disk('public')->url($file),
                'created_at' => Carbon::createFromTimestamp($createdAt)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::createFromTimestamp($createdAt)->format('Y-m-d H:i:s'),
                'size' => Storage::disk('public')->size($file),
            ];

            // Nếu là tệp hình ảnh, lấy kích thước
            if ($fileType === 'image') {
                $imageSize = getimagesize(Storage::disk('public')->path($file));
                $fileInfo['width'] = $imageSize[0];
                $fileInfo['height'] = $imageSize[1];
            }

            $items[] = $fileInfo;
        }
        return $items;
    }
    function getDataFiles(Request $rq)
    {
        return $this->loadDataFiles($rq->basePath);
    }
    // Phương thức để xác định loại tệp tin dựa trên phần mở rộng
    private function getFileType($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'bmp'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'mkv'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

        if (in_array(strtolower($extension), $imageExtensions)) {
            return 'image';
        } elseif (in_array(strtolower($extension), $videoExtensions)) {
            return 'video';
        } elseif (in_array(strtolower($extension), $documentExtensions)) {
            return 'document';
        } else {
            return 'other';
        }
    }

    public function rename(Request $request)
    {
        $currentName = $request->currentName;
        if ($request->path) {
            $path = 'uploads' . $request->path;
        } else {
            $path = 'uploads';
        }
        $newName = $request->newName;

        if ($request->type != 'dir') {
            $fileInfo = pathinfo($currentName);
            $fileName = $fileInfo['filename'];
            $fileExtension = $fileInfo['extension'];
            Storage::disk('public')->move($path . '/' . $currentName, $path . '/' . $newName . '.' . $fileExtension);
            return response()->json(['data' => 'Change File']);
        } else {
            if (!Storage::disk('public')->exists($path . '/' . $currentName)) {
                return response()->json(['message' => 'Thư mục không tồn tại.'], 404);
            }
            try {
                Storage::disk('public')->move($path . '/' . $currentName, $path . '/' . $newName);
            } catch (\Throwable $th) {
                return $th;
            }
        }
    }

    function delete(Request $request)
    {
        $data = $request->data;
        $path = $request->path;
        try {
            foreach ($data as $key => $value) {
                if ($value['type'] == 'dir') {
                    Storage::disk('public')->deleteDirectory('uploads/' . $path . '/' . $value['name']);
                } else {
                    Storage::disk('public')->delete('uploads/' . $path . '/' . $value['name']);
                }
            }
        } catch (\Throwable $th) {
            return $th;
        }

        return response()->json(['type' => 'success', 'message' => 'Xóa file, thư mục thành công']);
    }
}
