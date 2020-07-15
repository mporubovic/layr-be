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
            
            
            'meta' => [
                'originalName' => $this->original_name,
                'extension' => $this->extension,
                'size' => $this->size,
                'uploaded_at' => $this->uploaded_at,
                'updated_at' => $this->updated_at,
            ],

            'file' => [
                'url' => $this->getFileUrl($this->path),
                'position' => $this->position,
                'name' => $this->content_title,

            ],
            
        ];
    }

    public function getFileUrl($filePath) {
        $publicUrl = env('APP_PUBLIC_URL');
        $storagePath = '/storage';
        $filePublicUrl = $publicUrl . $storagePath . '/' . $filePath;

        return $filePublicUrl;

    }
}
