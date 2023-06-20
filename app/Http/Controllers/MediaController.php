<?php


namespace App\Http\Controllers;


use App\Helpers\MediaHelper;
use App\Models\Attachment;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController
{
    private MediaService $_service;

    public function __construct(MediaService $service)
    {
        $this->_service = $service;
    }

    public function upload(Request $request): JsonResponse
    {
        $files = request()->file('files');
        if (!$files) {
            return resError(trans('system.files_not_found'));
        }
        $data = [];
        $date = date('Ymd');
        $path = $this->createDirectory($date);
        foreach ($files as $file) {
            $fileName = getUuid().'.'.$file->getClientOriginalExtension();
            $file->move($path, $fileName);
            $pathName = "/uploads/$date/$fileName";
            $data[] = ['name' => $fileName, 'path' => $pathName, "full_path" => MediaHelper::getURL($pathName)];
        }
        return resSuccessWithinData($data);
    }

    public function single(Request $request): JsonResponse
    {
        $file = request()->file('file');
        if (!$file) {
            return resError(trans('system.files_not_found'));
        }
        $date = date('Ymd');
        $path = $this->createDirectory($date);
        $fileName = getUuid().'.'.$file->getClientOriginalExtension();
        $file->move($path, $fileName);
        $pathName = "/uploads/$date/$fileName";
        $data = ['name' => $fileName, 'path' => $pathName, 'full_path' => MediaHelper::getURL($pathName)];

        return resSuccessWithinData($data);
    }

    private function createDirectory(string $date): string
    {
        $path = public_path("storage/uploads/$date");
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    /**
     * @return JsonResponse
     */
    public function uploads(): JsonResponse
    {
        $basePath = $this->_service->makeDirectory();
        $files = request()->file('files');
        $attachments = [];
        foreach ($files as $file) {
            $originalFileName = $file->getClientOriginalName();
            $fileName = (getUuid()).'.'.$extension = $file->getClientOriginalExtension();
            $attachments[] = [
                'id' => getUuid(),
                'path' => date('Ymd').'/'.$fileName,
                'name' => $originalFileName,
                'extension' => $extension,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'storage' => "uploads",
                'created_at' => now()
            ];
            $file->move($basePath, $fileName);
        }
        Attachment::query()->insert($attachments);
        return resSuccessWithinData($attachments);
    }

    /**
     * @param  Attachment  $attachment
     * @return \never|StreamedResponse
     */
    public function getFileId(Attachment $attachment)
    {
        $storage = Storage::disk('uploads');
        /** @var Attachment $attachment */
        if ($attachment && $storage->exists($attachment->path)) {
            return $storage->download($attachment->path, $attachment->name);
        }
        return abort(Response::HTTP_NOT_FOUND, 'File không tồn tại');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function singleFile(Request $request): JsonResponse
    {
        $attachment = $this->_service->singleFile($request->file('file'));
        return resSuccessWithinData($attachment->only('id', 'url'));
    }
}