<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\File as FileResource;
use App\Http\Resources\Todo as TodoResource;
use App\Http\Resources\Url as UrlResource;
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
                // 'stackId' => $this->whenPivotLoaded('card_stack', function () {
                //     return $this->stacks()->pluck('id');
                // }),            
                'title' => $this->title,            
                'signed_by' => [
                    'name' => $this->user->name,
                    'id' => $this->user_id
                ],

                'type' => $this->type,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                // 'stacks' => $this->whenLoaded('stacks'),
                'stacks' => $this->when($this->relationLoaded('stacks'), function () {
                                return $this->stacks->pluck('id');
                            }),
            ],

            // 'content' => new ContentCollection($this->contents),
            // 'content' => $this->when(($request->queryMode == 'deep'), function () {
            //     return ContentResource::collection($this->content());
            // })
            // 'content' => $this->when(($request->queryMode == 'deep'), function () {
            //                 switch($this->cardContentType('image')) {
            //                     case('file'):
            //                         return FileResource::collection($this->files); 
            //                     }
            //                 }),

            // 'content' => FileResource::collection($this->whenLoaded('files')),
            // 'content' => TodoResource::collection($this->whenLoaded('todos')),
            'content' => $this->cardGetContentResource($this->type),
                

            
        ];
            
    }
    
    public function cardGetContentResource ($type) {

        switch($type) {

            case 'image':
            case 'video':
            case 'eps':
            case 'pdf':
            // case 'file':

                return FileResource::collection($this->whenLoaded('files'));



            case 'todo':

                return TodoResource::collection($this->whenLoaded('todos'));

            case 'url':

                return UrlResource::collection($this->whenLoaded('urls'));



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

                return 'other';



        }

    }
}
