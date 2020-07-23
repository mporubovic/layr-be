<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Content\File;
use App\Models\Content\Todo;
use App\Models\Content\Url;
use App\Models\Content;
use Illuminate\Http\Request;

use App\Http\Resources\Card as CardResource;
use App\Http\Resources\CardCollection as CardResourceCollection;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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
       
        // load('user') for signed_by in Card resource
        return new CardResourceCollection($cards->load('user')); // TODO: this will perform SQL query with all cards for all types WITH DEEP QUERY MODE
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
        // $validatedData = $request->validate([
        //     'type' => ['required', Rule::in(['image', 'video'])],
        //     'stackId' => 'required|integer'
        // ]
        // );

        // $validator = Validator::make($request->all(), [
        //     'type' => [
        //         'required',
        //         Rule::in(['image', 'video']),
        //     ],
        // ])->validate()->withErrors($validator, 'login');


        // return ('hello');
        
        $user = $request->user();

        $stackId = $request->stackId; // TODO: ADD STACK VALIDATION
        
        $cardTitle = $request->title ?? 'Default title';
        
        
        $cardType = $request->type;
        $cardContentType = $this->cardTypeToContentType($cardType);
        
        
        switch($cardContentType) {
            
            case('file'):
                if (!$request->hasFile('content')) {
                    return $this->cardNoFileUploadedError();
                }
                
                $files = $request->file('content');
                $cardContent = $this->cardFileHandler($files, $cardType);        
                $eagerLoadContent = 'files';
                break;

            case('todo'):
                
                $todos = $request->content;
                // return var_dump($todos);
                $cardContent = $this->cardTodoHandler($todos);
                $eagerLoadContent = 'todos';
                break;

            case ('url'):

                $urls = $request->content;
                // return var_dump($urls);
                $cardContent = $this->cardUrlHandler($urls);
                $eagerLoadContent = 'urls';
                break;
        }

        
        $card = $user->cards()->create(['title' => $cardTitle, 'type' => $cardType]);

        // return $user->stacks()->attach($card);



        $this->cardContentHandler($cardContent, $card, $cardContentType);

        $stack = \App\Models\Stack::with('cards')->find($stackId);

        $stackCardLast = $stack->cards()->max('position');
        $stack->cards()->attach($card, ['position' => $stackCardLast + 1]);

        return new CardResource($card->load($eagerLoadContent));
        // return CardResource::collection($card->load($eagerLoadContent));



    }

    public function cardContentHandler(array $cardContent, $card, $cardContentType) {
        
        $cardContentLast = $card->contents()->max('content_position') ?? 0;
        $cardContentInDatabase = [];
        foreach ($cardContent as $index=>$content) {
            $c = $card->contents()->create([
                        'content_type' => $cardContentType,
                        'content_id' => $content->id,
                        'content_position' => $cardContentLast + $index + 1, // 1-based position index
                    ]);
            //  array_push($cardContentInDatabase, $c);
        }

        // return $cardContentInDatabase;
        
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


    public function cardFileHandler($files, $cardType) {

            
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
        
        foreach ($files as $index=>$file) {
            
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

    public function cardTodoHandler(array $todos) {

        $todosInDatabse = [];
        foreach ($todos as $index=>$todo) {

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

            array_push($UrlsInDatabse, $urlInDatabase);
        }

        return $urlsInDatabase;
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

        
        $attributes = $request->only('title', 'settings');

        // $attributes = json_decode(stripslashes($attributes), true);
        // return json_decode(stripslashes($request->attributes), true);
        // $n = Card::where('id', $card->id)
        //     ->update($attributes);
        $card->fill($attributes);

        $card->save();

        // return new CardResource($card);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        
        $card = Card::find($request->cardId);

        if ($card == null) {
            return $this->cardNotFoundError();
        }
        
        $user = $request->user();
        
        if ($card->user_id != $user->id) {
            return $this->cardNoPermissionError();
        }

        $cardType = $card->type;

        $cardContentType = implode($this->cardTypeToContentType(array($cardType)));

        switch($cardContentType) {
            case ('file'):
                return $card->files();
                $card->files()->delete();
                break;
            case ('todo'):
                $card->todos()->delete();
                break;
            case ('url'):
                $card->urls()->delete();
                break;
        }


        $card->contents()->delete(); // card_content pivot
        $card->delete(); // including card_stack pivot
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
