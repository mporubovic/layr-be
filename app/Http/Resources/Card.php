<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\File as FileResource;
use App\Http\Resources\Todo as TodoResource;
use App\Http\Resources\Url as UrlResource;
use App\Http\Resources\Embed as EmbedResource;
use App\Http\Resources\Text as TextResource;
use App\Http\Resources\Whiteboard as WhiteboardResource;
// use App\Http\Resources\Content as ContentResource;
// use App\Http\Resources\ContentCollection as ContentResourceCollection;

class Card extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    
    // public static $wrap = 'cards';
    public static $wrap = 'card';
    
    public function toArray($request)
    {
        // return parent::toArray($request);
        
        
        // return 'hello';
        
        return [
            'info' => [
                'id' => $this->id,
                'title' => $this->title,            
                'type' => $this->type,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'stacks' => $this->when($this->relationLoaded('stacks'), function () {
                                return $this->stacks->pluck('id');
                            }),
            ],

            'content' => $this->cardGetContentResource($this->type),
            
            'settings' => $this->settings,
            
        ];
            
    }
    
    public function cardGetContentResource ($type) {

        switch($type) {

            case 'image':
            case 'video':
            case 'eps':
            case 'pdf':
                
                if ($this->whenLoaded('files') === null) {
                    return;
                } else {
                    return FileResource::collection($this->whenLoaded('files'));
                }
                
                


            case 'todo':

                if ($this->whenLoaded('todos') === null) {
                    return;
                } else {
                    return TodoResource::collection($this->whenLoaded('todos'));
                }
                

            case 'url':

                if ($this->whenLoaded('urls') === null) {
                    return;
                } else {
                    return UrlResource::collection($this->whenLoaded('urls'));
                }

            case 'youtube':

                if ($this->whenLoaded('embeds') === null) {
                    $arr = array();
                    // return "hello";
                    return $arr;
                } else {
                    return EmbedResource::collection($this->whenLoaded('embeds'));
                }
            
            case 'text':

                if ($this->whenLoaded('texts') === null) {
                    return;
                } else {
                    // return "hello";
                    // return TextResource::collection($this->whenLoaded('texts'));
                    return TextResource::collection($this->whenLoaded('texts'));
                }

            case 'whiteboard':

                if ($this->whenLoaded('whiteboards') === null) {
                    return;
                } else {
                    // return "hello";
                    // return TextResource::collection($this->whenLoaded('texts'));
                    return WhiteboardResource::collection($this->whenLoaded('whiteboards'));
                }


            case 'doc':
            case 'docx':
            case 'rtf':
            case 'txt':
            case 'pdf':

            case 'key':
            case 'pps':
            case 'ppt':
            case 'pptx':

            case 'xls':
            case 'xlsm':
            case 'xlsx':
            case 'csv':

                return 'document';


            case 'mid':
            case 'midi':
            case 'mp3':
            case 'mpa':
            case 'ogg':
            case 'wav':
            case 'wma':

                return 'audio';



            default:

                return '***unsupported card type***';



        }

    }
}
