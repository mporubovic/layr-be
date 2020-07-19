<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Content\File;
use App\Models\Content\Todo;
use App\Models\Content;
use Illuminate\Http\Request;

use App\Http\Resources\Card as CardResource;
use App\Http\Resources\CardCollection as CardResourceCollection;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $cards = $request->user()->cards;

        $cardTypes = array_values(array_unique($cards->pluck('type')->toArray())); // image, video...
        $cardContentTypes = $this->cardTypeToContentType($cardTypes);
        $cardContentTypes = preg_filter('/$/', 's', $cardContentTypes); //add s to types   files, todos...


        $queryMode = $request->queryMode ?? 'index';
        if ($queryMode == 'index') {
            
        } elseif ($queryMode == 'deep') {
            $cards->load($cardContentTypes);
            
        } else {
            abort(400, 'Consult the API docs for accepted queryMode parameters.');
        }
       

        return new CardResourceCollection($cards); // TODO: this will perform SQL query with all cards for all types
                                                    // rather should group cards by type and loop to get ids


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

        $stackId = $request->stackId;
        
        $cardTitle = $request->title ?? null;
        
        $cardType = $request->type;
        $cardContentType = $this->cardTypeToContentType($cardType);

        if ($cardContentType == 'file' && !$request->hasFile('file')) {
            return $this->cardNoFileUploadedError();
        }
        $card = $user->cards()->create(['title' => $cardTitle, 'type' => $cardType]);

        // return $user->stacks()->attach($card);
        $stack = \App\Models\Stack::with('cards')->find($stackId);

        $stackCardLast = $stack->cards()->max('position');
        $stack->cards()->attach($card, ['position' => $stackCardLast + 1]);
        

        
        switch($cardContentType) {
            
            case('file'):
                if (!$request->hasFile('file')) {
                    return $this->cardNoFileUploadedError();
                }
                
                $files = $request->file('file');
                $this->cardFileHandler($files, $card, $cardType, $cardContentType, 0);        
                $eagerLoadContent = 'files';
                break;

            case('todo'):
                
                $todos = $request->content;
                // return var_dump($todos);
                $this->cardTodoHandler($todos, $card, $cardType, $cardContentType, 0);
                $eagerLoadContent = 'todos';
                break;
        }

        return new CardResource($card->load($eagerLoadContent));
        // return CardResource::collection($card->load($eagerLoadContent));



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

        if (!$files->isValid()) {
            $this->cardFileUploadError();
        }


        $fileExtensions = [$files->extension()];



        $fileCombinationValidation = $this->cardValidateFileCombination($fileExtensions, $cardType);
        if ($fileCombinationValidation == false) {
            abort(400, 'Illegal file combination for ' . $cardType);
        }

        $fileArray = array($files);
        foreach ($fileArray as $index=>$file) {
            
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

            

            $card->contents()->create([
                'content_type' => $cardContentType,
                'content_id' => $fileInDatabase->id,
                'content_position' => $cardContentLast + $index + 1, // 1-based position index
            ]);
        }

    }

    public function cardTodoHandler(array $todos, $card, $cardType, $cardContentType, $cardContentLast) {

        foreach ($todos as $index=>$todo) {

            $todoInDatabse = new Todo([
                'body' => $todo['body'],
            ]);

            $todoInDatabse->save();

            $card->contents()->create([
                'content_type' => $cardContentType,
                'content_id' => $todoInDatabse->id,
                'content_position' => $cardContentLast + $index + 1, // 1-based position index
            ]);
            
        }

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

        // $card = Card::find($request->cardId)->load('files');
        $user = $request->user();
        
        $card = Card::find($request->cardId);

        if ($card == null) {
            $this->cardNotFoundError();
        }

        if ($card->user_id != $user->id) {
            return $this->cardNoPermissionError();    
        }
        
        $cardType = $card->type;
        
        $cardContentType = $this->cardTypeToContentType(array($cardType));
        
        $cardContentType = array_values(preg_filter('/$/', 's', $cardContentType));

        $eagerLoadRelations = array_push($cardContentType, 'stacks');

        // return [1 => $cardContentType];
        // // return $cardContentType;


        // $card->load([$cardContentType, 'stacks']);
        $card->load($cardContentType);
        // return ($card->has('files')->get());
        // return Card::whereHas('files', function (Builder $query) {
        //     $query->join('card_content', 'card_content.content_id', '=', 'files.id');
        // })->get();

        // return $card;
        // return $card;
        return new CardResource($card);
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

        $user = $request->user();
        $card = Card::find($request->cardId);

        if ($card == null) {
            return $this->cardNotFoundError();
        }

        if ($card->user_id != $user->id) {
            return $this->cardNoPermissionError();
        }

        $fields = $request->fields;
        // $fields = json_decode(stripslashes($fields), true);
        // return json_decode(stripslashes($request->fields), true);
        // $n = Card::where('id', $card->id)
        //     ->update($fields);
        $card->fill($fields);

        $card->save();

        return new CardResource($card);
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
