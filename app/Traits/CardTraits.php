<?php

namespace App\Traits;

use App\Models\Content\File;
use App\Models\Content\Todo;
use App\Models\Content\Url;
use App\Models\Content\Embed;
use App\Models\Content\Text;
use App\Models\Content\Whiteboard;

trait CardTraits
{
    public function cardContentHandler(array $cardContent, $card, $cardContentType)
    {

        $cardContentLast = $card->contents()->max('content_position');
        $offset = $cardContentLast === null ? 0 : $cardContentLast + 1;
        $cardContentInDatabase = [];
        foreach ($cardContent as $index => $content) {
            $c = $card->contents()->create([
                'content_type' => $cardContentType,
                'content_id' => $content->id,
                'content_position' => $offset + $index,
            ]);
            array_push($cardContentInDatabase, $c);
        }

        return $cardContentInDatabase;

    }

    public function cardTypeToContentType($types)
    {

        $cardTypes = [
            'image' => 'file',
            'video' => 'file',
            'pdf' => 'file',
            '3dobject' => 'file',

            'text' => 'text',

            'embed' => 'embed',
            'youtube' => 'embed',

            'word' => 'iframe',
            'powerpoint' => 'iframe',
            'excel' => 'iframe',

            'instagram' => 'iframe',

            'todo' => 'todo',
            'url' => 'url',

            'folder' => 'file',
            
            'whiteboard' => 'whiteboard',
        ];

        $cardContentTypes = [];

        if (!is_array($types)) {
            return $cardTypes[$types];
        }

        foreach ($types as $type) {


            $cardContentType = $cardTypes[$type] ?? 'other';


            if ($cardContentType == 'other') {

                array_push($cardContentTypes, 'file');
            } else {

                array_push($cardContentTypes, $cardContentType);
            }
        }
        return $cardContentTypes;
    }

    public function cardValidateFileCombination(array $files, string $cardType)
    {


        $legalFileCombinations = [
            'image' => ['jpg', 'jpeg', 'bmp', 'gif', 'ico', 'png', 'tif', 'tiff', 'ps', 'eps', 'svg', 'heic'],
            'video' => ['avi', 'h264', 'm4v', 'mp4', 'wmv', 'mpg', 'mpeg', 'mov', 'heif', 'hevc'],
            'pdf' => ['pdf'],
            '3dobject' => ['stl', 'obj'],
            'audio' => ['mid', 'midi', 'mp3', 'mpa', 'ogg', 'wav', 'wma'],
            'text' => ['jpg', 'jpeg', 'bmp', 'gif', 'ico', 'png', 'tif', 'tiff']
        ];
        if (empty(array_diff($files, $legalFileCombinations[$cardType]))) {

            return true;
        } else {
            return false;
        }
    }


    public function cardUploadedFileHandler($files, $cardType, $userId)
    {


        $fileExtensions = [];
        foreach ($files as $file) {
            if (!$file->isValid()) {
                $this->cardFileUploadError();
            }


            array_push($fileExtensions, $file->extension());
        }

        $fileCombinationValidation = $this->cardValidateFileCombination($fileExtensions, $cardType);
        if ($fileCombinationValidation == false) {
            return abort(400, 'Illegal file combination for ' . $cardType);
        }

        $filesInDatabse = [];

        foreach ($files as $index => $file) {

            $filePath = $file->store('a');
            $publicUrl = env('APP_API_URL');
            $storagePath = '/storage';
            $filePublicUrl = $publicUrl . $storagePath . '/' . $filePath;

            $fileOriginalName = $file->getClientOriginalName();
            $fileExtension = $file->extension();
            $fileSize = $file->getSize();

            $splitDelimiter = '.';
            // Removes the first extension after the last dot
            $fileNameSplit = array_reverse(array_map('strrev', explode($splitDelimiter, strrev($fileOriginalName), 2)))[0];

            $fileInDatabase = new File([
                'extension' => $fileExtension,
                'path' => $filePublicUrl,
                'size' => $fileSize,
                'name' => $fileNameSplit,
                'original_name' => $fileOriginalName,
                'user_id' => $userId,
            ]);



            $fileInDatabase->save();

            array_push($filesInDatabse, $fileInDatabase);
        }

        return $filesInDatabse;
    }

    public function cardLinkedFileHandler($files, $cardType) {
        
    }

    public function cardTodoHandler(array $todos)
    {

        $todosInDatabse = [];
        foreach ($todos as $index => $todo) {

            $todoInDatabase = new Todo([
                'body' => $todo['todo']['body'],
            ]);

            $todoInDatabase->save();

            array_push($todosInDatabse, $todoInDatabase);
        }

        return $todosInDatabse;
    }

    public function cardUrlHandler(array $urls)
    {

        $urlsInDatabase = [];
        foreach ($urls as $index => $url) {

            $urlInDatabase = new Url([
                'path' => $url['url']['path'],
                // 'name' => $url['url']['path'],
            ]);

            $urlInDatabase->save();

            array_push($urlsInDatabase, $urlInDatabase);
        }

        return $urlsInDatabase;
    }

    public function cardEmbedHandler(array $embeds)
    {

        $embedsInDatabase = [];
        foreach ($embeds as $index => $embed) {

            $embedInDatabase = new Embed([
                'path' => $embed['embed']['path'],
                // 'name' => $embed['path'],
            ]);

            $embedInDatabase->save();

            array_push($embedsInDatabase, $embedInDatabase);
        }

        return $embedsInDatabase;
    }
    
    public function cardTextHandler($text)
    {
            $textInArray = [];
            $textInDatabase = new Text([
                'text' => $text[0]['text']['text'],
            ]);

            $textInDatabase->save();
            array_push($textInArray, $textInDatabase);
        return $textInArray;
    }    
    
    public function cardWhiteboardHandler($whiteboard)
    {
            $whiteboardInArray = [];
            $whiteboardInDatabase = new Whiteboard([
                'data' => json_encode($whiteboard[0]['whiteboard']['data']),
            ]);

            $whiteboardInDatabase->save();
            array_push($whiteboardInArray, $whiteboardInDatabase);
        return $whiteboardInArray;
    }

    public function cardAssignInterpreter($extension)
    {

        switch ($extension) {

            case 'jpg':
            case 'jpeg':
            case 'bmp':
            case 'gif':
            case 'ico':
            case 'png':
            case 'tif':
            case 'tiff':
            case 'ps':
            case 'eps':
            case 'svg':
            case 'heic':

                return 'image';



            case 'avi':
            case 'h264':
            case 'm4v':
            case 'mp4':
            case 'wmv':
            case 'mpg':
            case 'mpeg':
            case 'mov':
            case 'heif':
            case 'hevc':

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
