<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class File extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'originalName' => $this->original_name,
            'extension' => $this->extension,
            'url' => $this->getFileUrl($this->path),
            'size' => Storage::size($this->path),
        ];
    }

    public function getFileUrl($filePath) {
        $publicUrl = env('APP_PUBLIC_URL');
        $storagePath = '/storage';
        $filePublicUrl = $publicUrl . $storagePath . '/' . $filePath;

        return $filePublicUrl;

    }
}
