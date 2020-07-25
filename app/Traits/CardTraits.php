<?php

namespace App\Traits;

use App\Models\Content\File;
use App\Models\Content\Todo;
use App\Models\Content\Url;

trait CardTraits
{
    public function cardContentHandler(array $cardContent, $card, $cardContentType)
    {

        $cardContentLast = $card->contents()->max('content_position') ?? 0;
        $cardContentInDatabase = [];
        foreach ($cardContent as $index => $content) {
            $c = $card->contents()->create([
                'content_type' => $cardContentType,
                'content_id' => $content->id,
                'content_position' => $cardContentLast + $index + 1, // 1-based position index
            ]);
            //  array_push($cardContentInDatabase, $c);
        }

        // return $cardContentInDatabase;

    }

    public function cardTypeToContentType($types)
    {

        $cardTypes = [
            'image' => 'file',
            'video' => 'file',
            'pdf' => 'file',
            '3dobject' => 'file',

            'text' => 'richtext',

            'word' => 'iframe',
            'powerpoint' => 'iframe',
            'excel' => 'iframe',

            'instagram' => 'iframe',

            'todo' => 'todo',
            'url' => 'url',

            'folder' => 'file',
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
        ];
        if (empty(array_diff($files, $legalFileCombinations[$cardType]))) {

            return true;
        } else {
            return false;
        }
    }


    public function cardFileHandler($files, $cardType)
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
            $fileOriginalName = $file->getClientOriginalName();
            $fileExtension = $file->extension();
            $fileSize = $file->getSize();

            $splitDelimiter = '.';
            // Removes the first extension after the last dot
            $fileNameSplit = array_reverse(array_map('strrev', explode($splitDelimiter, strrev($fileOriginalName), 2)))[0];

            $fileInDatabase = new File([
                'extension' => $fileExtension,
                'path' => $filePath,
                'size' => $fileSize,
                'name' => $fileNameSplit,
                'original_name' => $fileOriginalName,
            ]);



            $fileInDatabase->save();

            array_push($filesInDatabse, $fileInDatabase);
        }

        return $filesInDatabse;
    }

    public function cardTodoHandler(array $todos)
    {

        $todosInDatabse = [];
        foreach ($todos as $index => $todo) {

            $todoInDatabase = new Todo([
                'body' => $todo['body'],
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
                'path' => $url['path'],
                'name' => $url['path'],
            ]);

            $urlInDatabase->save();

            array_push($urlsInDatabase, $urlInDatabase);
        }

        return $urlsInDatabase;
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
