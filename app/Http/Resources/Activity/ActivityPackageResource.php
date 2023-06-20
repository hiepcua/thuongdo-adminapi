<?php

namespace App\Http\Resources\Activity;

use App\Constants\ActivityConstant;
use App\Constants\PackageConstant;
use App\Helpers\TimeHelper;
use App\Http\Resources\ReportStatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $content = $this->log_name === ActivityConstant::PACKAGE_STATUS ? new ReportStatusResource(
            $this->content,
            PackageConstant::STATUSES,
            PackageConstant::STATUSES_COLOR
        ) : $this->content;
        return [
            'time' => TimeHelper::format($this->created_at),
            'staff' => optional($this->causer)->only(['id', 'name']),
            'content' => $content
        ] + ['properties' => json_decode($this->properties) ?? null];
    }
}
