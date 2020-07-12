<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\File;
use App\Models\Content;
use App\Models\Content\Video;
use App\Models\Content\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->hasFile('file')) {
            $this->cardNoFileUploadedError();
        }        
        
        $file = $request->file('file');
        
        if (!$file->isValid()) {
            $this->cardFileUploadError();
        }


        $user = $request->user();

        
        $filePath = $file->store('a');
        $fileOriginalName = $file->getClientOriginalName();
        $fileExtension = $file->extension();

        $publicUrl = env('APP_PUBLIC_URL');
        $storagePath = '/storage';
        $filePublicUrl = $publicUrl . $storagePath . '/' . $filePath;

        $fileInDatabase = new File([
            'extension' => $fileExtension,
            'path' => $filePath,
            'original_name' => $fileOriginalName,
        ]);

        $fileInDatabase->save();      
        
        $splitDelimiter = '.';
        // Removes the first extension after the last dot
        $fileNameSplit = array_reverse(array_map('strrev', explode($splitDelimiter, strrev($fileOriginalName), 2)))[0];
        
        // return [$cardTitle, $filePublicUrl];
        $newCard = $user->cards()->create(['title' => $fileNameSplit, 'interpreter' => 'none']);


        // $newCard->contents()->save($fileInDatabase);


        $newCard->contents()->create(['content_type' => 'file', 
                                    'content_id' => $fileInDatabase->id,
                                    'content_title' => $fileNameSplit,
                                    'position' => '1']);
        
        return $newCard->load('contents.getContent');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function show(Card $card)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function edit(Card $card)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Card $card)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function destroy(Card $card)
    {
        //
    }

    public function cardAssignInterpreter ($extension) {

        switch($extension) {

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
            case 'heif':
            case 'hevc':
                
                return 'image';



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
    
    
    public function cardNotFoundError() {

        abort(403, 'The authenticated user does not have access to the requested board.');

    }

    public function cardNoPermissionError() {

        abort(403, 'The authenticated user does not have access to the requested board.');

    }

    public function cardNoFileUploadedError() {

        abort(400, 'No file was included with the request');

    } 
    
    public function cardFileUploadError() {

        abort(400, 'There was an error with the file upload.');

    }

    


}
