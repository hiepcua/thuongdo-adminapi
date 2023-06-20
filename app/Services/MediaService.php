<?php


namespace App\Services;


use App\Models\Attachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class MediaService
{
    /**
     * @param $file
     * @return Builder|Model|JsonResponse
     */
    public function singleFile($file)
    {
        $basePath = $this->makeDirectory();
        if (!$file) {
            return resError(trans('system.files_not_found'));
        }
        $fileName = getUuid().'.'.$file->getClientOriginalExtension();
        $attachment = Attachment::query()->create(
            [
                'path' => '/'.date('Ymd').'/'.$fileName,
                'name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'storage' => 'uploads'
            ]
        );
        $file->move($basePath, $fileName);

        return $attachment;
    }

    /**
     * @return string
     */
    public function makeDirectory(): string
    {
        $basePath = storage_path($storage = 'uploads').'/'.date('Ymd');
        if (!is_dir($basePath)) {
            mkdir($basePath);
        }
        return $basePath;
    }
}