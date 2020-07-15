<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\File as FileResource;
use App\Http\Resources\Todo as TodoResource;
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

    
    public static $wrap = 'cards';
    
    public function toArray($request)
    {
        // return parent::toArray($request);
        
        
        // return 'hello';
        
        return [
            'attributes' => [
                'id' => $this->id,
                'title' => $this->title,            
                'type' => 'image'
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

            'content' => FileResource::collection($this->whenLoaded('files'))->sortByDesc('file.position'),
            // 'content' => TodoResource::collection($this->whenLoaded('todos')),
                

            
        ];
            
    }
    
    public function cardContentType ($type) {

        switch($type) {

            case 'image':
            case 'video':
            case 'eps':
            case 'pdf':
            case 'file':

                return 'file';



            case 'avi':
            case 'h264':
            case 'm4v':
            case 'mp4':
            case 'wmv':
            case 'mpg':
            case 'mpeg':
            case 'mov':

                return 'video';



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
