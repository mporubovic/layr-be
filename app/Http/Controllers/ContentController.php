<?php

namespace App\Http\Controllers;

// use App\Content;
use Illuminate\Http\Request;
use App\Models\Card;

use App\Models\Content\File;
use App\Models\Content\Todo;
use App\Models\Content\Url;
use App\Models\Content;

use App\Http\Resources\Card as CardResource;
use App\Http\Resources\FileCollection as FileResourceCollection;
use App\Http\Resources\CardCollection as CardResourceCollection;

class ContentController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $card = Card::with('user')->find($request->cardId);

        if ($card == null) {
            return $this->cardNotFoundError();
        }

        if ($card->user_id != $user->id) {
            return $this->cardNoPermissionError();
        }
        
        // $cards = $request->user()->cards;

        
        $cardType = $card->type;
        $cardContentType = $this->cardTypeToContentType($cardType);
        $cardContentLast = $card->contents()->max('content_position') ?? 0;


        switch($cardContentType) {
            case('file'):
                if (!$request->hasFile('content')) {
                    return $this->cardNoFileUploadedError();
                }
                
                $files = $request->file('content');
                $this->cardFileHandler($files, $card, $cardType, $cardContentType, $cardContentLast);        
                $eagerLoadContent = 'files';
                // return new FileResourceCollection($filesInDatabse); // doesn't return pivot table info
            
                break;

            case('todo'):
                
                $todos = $request->content;
                $this->cardTodoHandler($todos, $card, $cardType, $cardContentType, $cardContentLast);
                $eagerLoadContent = 'todos';
                break;

            case ('url'):

                $urls = $request->content;
                // return var_dump($urls);
                $this->cardUrlHandler($urls, $card, $cardType, $cardContentType, $cardContentLast);
                $eagerLoadContent = 'urls';
                break;
        }

        return new CardResource($card->load($eagerLoadContent));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content $content)
    {
        //
    }

    public function cardTypeToContentType($types) {

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
        
        foreach($types as $type) {    


            $cardContentType = $cardTypes[$type] ?? 'other';
            

            if ($cardContentType == 'other') {

                array_push($cardContentTypes, 'file');

            } else {

                array_push($cardContentTypes, $cardContentType);
            }
        }
        return $cardContentTypes;


    }

    public function cardValidateFileCombination(array $files, string $cardType) {

        
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


    public function cardFileHandler($files, $card, $cardType, $cardContentType, $cardContentLast) {

        $fileExtensions = [];
        foreach ($files as $file) {
            if (!$file->isValid()) {
                $this->cardFileUploadError();
            }


            array_push($fileExtensions, $file->extension());
        }

        $fileCombinationValidation = $this->cardValidateFileCombination($fileExtensions, $cardType);
        if ($fileCombinationValidation == false) {
            abort(400, 'Illegal file combination for ' . $cardType);
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

            
            array_push($filesInDatabse, $fileInDatabase);
            $fileInDatabase->save();
            
            


            $card->contents()->create([
                'content_type' => $cardContentType,
                'content_id' => $fileInDatabase->id,
                'content_position' => $cardContentLast + $index + 1, // 1-based position index
            ]);
        }

        return $filesInDatabse;


    }

    public function cardTodoHandler(array $todos, $card, $cardType, $cardContentType, $cardContentLast) {

        foreach ($todos as $index=>$todo) {

            $todoInDatabse = new Todo([
                'body' => $todo,
            ]);

            $todoInDatabse->save();

            $card->contents()->create([
                'content_type' => $cardContentType,
                'content_id' => $todoInDatabse->id,
                'content_position' => $cardContentLast + $index + 1, // 1-based position index
            ]);
            
        }

    }

    public function cardUrlHandler(array $urls, $card, $cardType, $cardContentType, $cardContentLast)
    {

        foreach ($urls as $index => $url) {

            $urlInDatabse = new Url([
                'path' => $url['path'],
                'name' => $url['path'],
            ]);

            $urlInDatabse->save();

            $card->contents()->create([
                'content_type' => $cardContentType,
                'content_id' => $urlInDatabse->id,
                'content_position' => $cardContentLast + $index + 1, // 1-based position index
            ]);
        }
    }

    public function cardNotFoundError() {

        abort(403, 'The authenticated user does not have access to the requested card.');

    }

    public function cardNoPermissionError() {

        abort(403, 'The authenticated user does not have access to the requested card.');

    }

    public function cardNoFileUploadedError() {

        abort(400, 'No file was included with the request');

    }

    public function cardFileUploadError() {

        abort(400, 'There was an error with the file upload.');

    }
}
