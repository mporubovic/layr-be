<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\File;
use App\Models\Content;
use Illuminate\Http\Request;

use App\Http\Resources\Card as CardResource;
use App\Http\Resources\CardCollection as CardResourceCollection;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $queryMode = $request->queryMode ?? 'index';
        if ($queryMode == 'index') {
            $cards = $request->user()->cards;
        } elseif ($queryMode == 'deep') {
            $cards = $request->user()->cards->load(['files']);
        } else {
            abort(400, 'Consult the API docs for accepted queryMode parameters.');
        }
       
        // return dd ($cards);
        // return $cards;
        $cardResourceCollection = new CardResourceCollection($cards);
        // return $cardResourceCollection;

        return $cardResourceCollection;
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
        $fileSize = $file->getSize();

        $fileInDatabase = new File([
            'extension' => $fileExtension,
            'path' => $filePath,
            'size' => $fileSize,
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

        // return $newCard->load('contents.getContent');

        return new CardResource($newCard);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // $card = Card::with('contents.getContent')->find($request->cardId);
        $card = Card::find($request->cardId)->load('files');
        // return $card;
        $cardResource = new CardResource($card);
        return $cardResource;
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
