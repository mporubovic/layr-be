<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Content\File;
use App\Models\Content\Todo;
use App\Models\Content\Url;
use App\Models\Content\Text;
use App\Models\Content\Embed;
use App\Models\Content\Whiteboard;
use App\Models\Content;
use Illuminate\Http\Request;

use App\Http\Resources\Card as CardResource;
use App\Http\Resources\CardCollection as CardResourceCollection;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Traits\CardTraits;
use App\Traits\CardErrors;


use Illuminate\Support\Arr;

class CardController extends Controller
{

    use CardTraits;
    use CardErrors;

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

        Validator::make($request->all(), [
            'type' => [
                'required',
                Rule::in(['image', 'video', 'pdf', '3dobject', 'todo', 'url', 'embed', 'text', 'youtube', 'whiteboard'])
            ],
            // 'program' => 'required',

            'boardId' => 'required|integer',
            'title' => 'required',
            'content' => 'sometimes|array',
            'open' => 'sometimes|boolean',
            'settings' => 'required|json'
            
        ])->validate();


        // return ('hello');
        
        $user = $request->user();
        // return ($request);
        $boardId = $request->boardId; // TODO: ADD STACK VALIDATION
        $cardTitle = $request->title ?? 'New Card';
        $cardType = $request->type;
        $content = $request->content ?? null;
        $cardOpen = (int)$request->open ?? 0;
        $requestSettings = Arr::only(json_decode($request->settings, true), ['dimensions', 'program', 'styling']);


        $card = $user->cards()->create([
                                        'title' => $cardTitle, 
                                        'type' => $cardType, 
                                        'settings' => $requestSettings
                                        ]);
        
        if ($content) {
            $cardContentType = $this->cardTypeToContentType($cardType);
            
            
            switch($cardContentType) {
                
                case('file'):
                    if (!$request->hasFile('content')) {
                        $files = $request->content;
                        $cardContent = $this->cardLinkedFileHandler($files, $cardType);
                        // return $this->cardNoFileUploadedError();
                    } else {
                        $files = $request->file('content');
                        $cardContent = $this->cardUploadedFileHandler($files, $cardType, $user->id);
                    }
                    
                    
                    $eagerLoadContent = 'files';
                    break;

                case('todo'):
                    
                    $todos = $request->content;
                    // return $todos;
                    // return var_dump($todos);
                    // return $todos;
                    $cardContent = $this->cardTodoHandler($todos);
                    $eagerLoadContent = 'todos';
                    break;

                case ('url'):

                    $urls = $request->content;
                    // return var_dump($urls);
                    $cardContent = $this->cardUrlHandler($urls);
                    $eagerLoadContent = 'urls';
                    break;

                case ('embed'):

                    $embeds = $request->content;
                    // return var_dump($urls);
                    $cardContent = $this->cardEmbedHandler($embeds);
                    $eagerLoadContent = 'embeds';
                    break;
                
                case ('text'):

                    $text = $request->content;
                    // return var_dump($urls);
                    $cardContent = $this->cardTextHandler($text);
                    $eagerLoadContent = 'texts';
                    break;

                case ('whiteboard'):

                    $whiteboard = $request->content;
                    // return var_dump($urls);
                    $cardContent = $this->cardWhiteboardHandler($whiteboard);
                    $eagerLoadContent = 'whiteboards';
                    break;
            }

            // return $user->stacks()->attach($card);

            // return ([$content, gettype($content)]);
            $this->cardContentHandler($cardContent, $card, $cardContentType);
        }

        $board = \App\Models\Board::with('cards')->find($boardId);
        // return $board;

        $board->cards()->attach($card);
        
        $cardWithPivot = $board->cards()->find($card->id);
        // return $cardWithPivot;
        // $card->fakePivot['position'] = $boardCardLast + 1;
        // return $card;

        if (isset($eagerLoadContent)) {
            // return $eagerLoadContent;
            return new CardResource($cardWithPivot->load($eagerLoadContent));
        } else {
            return new CardResource($cardWithPivot);

            // return new CardResource($card->load('stacks'));
        }
        
        // return CardResource::collection($card->load($eagerLoadContent));



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

        if (!$card->boards[0]->stacks[0]->users->contains($user)) {
            return $this->cardNoPermissionError();    
        }
        
        $cardType = $card->type;
        
        $cardContentType = $this->cardTypeToContentType(array($cardType));
        
        $cardContentType = array_values(preg_filter('/$/', 's', $cardContentType));

        // $eagerLoadRelations = array_push($cardContentType, 'stacks');

        // return [1 => $cardContentType];
        // // return $cardContentType;


        // $card->load([$cardContentType, 'stacks']);
        $card->load($cardContentType);
        // return $card;
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

        if (!$card->boards[0]->stacks[0]->users->contains($user)) {
            return $this->cardNoPermissionError();
        }

        $attributes = $request->only('title');
        
        $updatedProperties = $attributes;
        
        if ($request->settings) {
            $requestSettings = Arr::only($request->settings, ['dimensions', 'program', 'styling']);
            // return [$card->settings, $requestSettings];
            $originalSettings = $card->settings;
            $settings = array_merge_recursive_distinct($originalSettings, $requestSettings);
            $updatedProperties = $updatedProperties + compact("settings");

        }

        // if(isset($request->open)) {
        //     $cardOpen = (int)$request->open;
        //     $stackId = $card->stacks[0]->id; 

        //     $card->stacks()->updateExistingPivot($stackId, ['open' => $cardOpen]);
        // }
        // $requestSettings = Arr::only($request->settings, ['dimensions', 'program', 'styling']);
        // return [$card->settings, $requestSettings];
        // $originalSettings = $card->settings;
        // $settings = array_merge_recursive_distinct($originalSettings, $requestSettings);
        // return $settings;
        // return [$card->settings, $requestSettings, $settings];
        // return compact("settings", "attributes");
        // $updatedProperties = array($attributes, compact('settings'));
        // $updatedProperties = compact("settings") + $attributes;
        // array_push($updatedProperties, array_values($attributes));
        // return $updatedProperties;
        // $array1 = $card->settings;
        // $array2 = $requestSettings;
        // $merged = $array1;

        // foreach ($array2 as $key => &$value) {
        //     if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
        //         $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
        //     } else {
        //         $merged[$key] = $value;
        //     }
        // }

        // return $merged;
        // $attributes = json_decode(stripslashes($attributes), true);
        // return json_decode(stripslashes($request->attributes), true);
        // $n = Card::where('id', $card->id)
        //     ->update($attributes);
        // return [$updatedProperties];
        // return [$attributes, compact('settings')];
        // if ($attributes) {
            $card->fill($updatedProperties)->save();
        // }


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
        
        $cardId = $request->cardId;
        $card = Card::find($cardId);

        if ($card == null) {
            return $this->cardNotFoundError();
        }
        
        $user = $request->user();
        
        if (!$card->boards[0]->stacks[0]->users->contains($user)) {
            return $this->cardNoPermissionError();
        }

        $cardType = $card->type;
        
        $cardContentType = implode($this->cardTypeToContentType(array($cardType)));
        
        // dd($cardContentType);
        
        // switch($cardContentType) {
        //     case('file'):
        //         $card->load('files');
        //         File::whereIn('id', $card->files->pluck('id'))->delete();
        // }
        
        
        switch($cardContentType) {
            case ('file'):
                $card->load('files');
                if ($card->files) File::whereIn('id', $card->files->pluck('id'))->delete();
                break;
            case ('todo'):
                $card->load('todos');
                if ($card->todos) Todo::whereIn('id', $card->todos->pluck('id'))->delete();
                break;
            case ('url'):
                $card->load('urls');
                if ($card->urls) Url::whereIn('id', $card->urls->pluck('id'))->delete();
                break;
            case ('embed'):
                $card->load('embeds');
                if ($card->embeds) Embed::whereIn('id', $card->embeds->pluck('id'))->delete();
                break;
            case ('text'):
                $card->load('texts');
                if ($card->texts) Text::whereIn('id', $card->texts->pluck('id'))->delete();
                break;
            case ('whiteboard'):
                $card->load('whiteboards');
                if ($card->whiteboards) Whiteboard::whereIn('id', $card->whiteboards->pluck('id'))->delete();
                break;
        }


        $card->contents()->delete(); // card_content pivot
        
        // $card->load('stacks')->with('cards');
        $card->delete(); // including card_stack pivot
    }


}
