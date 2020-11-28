<?php

namespace App\Http\Controllers;

// use App\Content;
use Illuminate\Http\Request;
use App\Models\Card;

use App\Models\Content\File;
use App\Models\Content\Todo;
use App\Models\Content\Url;
use App\Models\Content\Embed;
use App\Models\Content\Text;
use App\Models\Content\Whiteboard;
use App\Models\Content;

use App\Http\Resources\Card as CardResource;
// use App\Http\Resources\FileCollection as FileResourceCollection;
use App\Http\Resources\CardCollection as CardResourceCollection;
use App\Http\Resources\File as FileResource;
use App\Traits\CardTraits;
use App\Traits\CardErrors;

use App\Http\Resources\FileCollection as FileResourceCollection;
use App\Http\Resources\TodoCollection as TodoResourceCollection;
use App\Http\Resources\UrlCollection as UrlResourceCollection;
use App\Http\Resources\EmbedCollection as EmbedResourceCollection;
// use App\Http\Resources\Text as TextResource;

class ContentController extends Controller
{
    use CardTraits;
    use CardErrors;
    
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

        if (!$card->boards[0]->stacks[0]->users->contains($user)) {
            return $this->cardNoPermissionError();
        }
        
        // $cards = $request->user()->cards;

        
        $cardType = $card->type;
        $cardContentType = $this->cardTypeToContentType($cardType);

        // return $cardContentType;


        switch($cardContentType) {
            case('file'):
                if (!$request->hasFile('content')) {
                    $files = $request->content;
                    $cardContent = $this->cardLinkedFileHandler($files, $cardType);
                    // return ['one', $request->file('content')];
                    // return $this->cardNoFileUploadedError();
                } else {
                    $files = $request->file('content');
                    $cardContent = $this->cardUploadedFileHandler([$files], $cardType);
                    // return $files;

                }
                $eagerLoadContent = 'files';
                $cardContentResource = FileResource::collection($cardContent);

                // return ['content', $cardContent];

            
                break;

            case('todo'):
                
                $todos = $request->content;
                $cardContent = $this->cardTodoHandler($todos);
                $eagerLoadContent = 'todos';
                $cardContentResource = new TodoResourceCollection($cardContent);

                break;

            case ('url'):

                $urls = $request->content;
                // return var_dump($urls);
                $cardContent = $this->cardUrlHandler($urls);
                $eagerLoadContent = 'urls';
                $cardContentResource = new UrlResourceCollection($cardContent);

                break;

            case ('embed'):

                $embeds = $request->content;
                // return var_dump($embeds);
                $cardContent = $this->cardEmbedHandler($embeds);
                $eagerLoadContent = 'embeds';
                $cardContentResource = new EmbedResourceCollection($cardContent);

                break;
        }

        $this->cardContentHandler($cardContent, $card, $cardContentType);

        return $cardContentResource;

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
        $contentId = $request->contentId;
        $cardId = $request->cardId;
        // $attributes = $request->attributes;

        $user = $request->user();
        $card = Card::with('user')->find($request->cardId);

        if ($card == null) {
            return $this->cardNotFoundError();
        }

        if (!$card->boards[0]->stacks[0]->users->contains($user)) {
            return $this->cardNoPermissionError();
        }

        // $cards = $request->user()->cards;


        $cardType = $card->type;
        $cardContentType = $this->cardTypeToContentType($cardType);


        switch ($cardContentType) {
            case ('file'):
                
                $filteredAttributes = $request->only('attributes.name');
                try {
                    $file = File::findOrFail($contentId);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
                $file->fill($filteredAttributes['attributes'])->save();


                break;

            case ('todo'):

                $filteredAttributes = $request->only('content.todo.body', 'content.todo.completed_at');
                try {
                    $todo = Todo::findOrFail($contentId);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
                $todo->fill($filteredAttributes['content']['todo'])->save();

                break;

            case ('url'):

                $filteredAttributes = $request->only('content.url.path');
                try {
                    $url = Url::findOrFail($contentId);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
                $url->fill($filteredAttributes['content']['url'])->save();

                break;


            case ('embed'):

                $filteredAttributes = $request->only('content.embed.path');
                try {
                    $embed = Embed::findOrFail($contentId);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
                $embed->fill($filteredAttributes['content']['embed'])->save();

                break;
                
                
            case ('text'):

                $filteredAttributes = $request->only('content.text');
                try {
                    $text = Text::findOrFail($contentId);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
                $text->fill($filteredAttributes['content'])->save();

                break;
        
            case ('whiteboard'):

                $filteredAttributes = $request->only('content.whiteboard.data');
                try {
                    $whiteboard = Whiteboard::findOrFail($contentId);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
                $whiteboard->fill($filteredAttributes['content']['whiteboard'])->save();

                break;        
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $card = Card::find($request->cardId);
        // return $request->cardId;

        if ($card == null) {
            return $this->cardNotFoundError();
        }

        $user = $request->user();

        if (!$card->boards[0]->stacks[0]->users->contains($user)) {
            return $this->cardNoPermissionError();
        }

        $cardType = $card->type;

        $cardContentType = implode($this->cardTypeToContentType(array($cardType)));
        $contentId = $request->contentId;
        // dd($cardContentType);

        // switch($cardContentType) {
        //     case('file'):
        //         $card->load('files');
        //         File::whereIn('id', $card->files->pluck('id'))->delete();
        // }


        switch ($cardContentType) {
            case ('file'):
                $card->load('files');
                // File::whereIn('id', $contentId)->delete();
                File::destroy($contentId);
                break;
            case ('todo'):
                // $card->load('todos');
                $todo = $card->todos->find($contentId);
                $todoPos = $todo['content_position'];
                $todosAboveIds = $card->todos->filter(function ($value) use ($todoPos) {
                    return $value['content_position'] > $todoPos;
                })->pluck('content_id');
                // Todo::destroy($contentId);
                $todo->delete();
                $card->contents()->whereIn('content_id', $todosAboveIds)->decrement('content_position');
                // $card->content()->whereIn('content_id', $todosAboveIds)->decrement('content_position');
                break;
            case ('url'):
                $url = $card->urls->find($contentId);
                $urlPos = $url['content_position'];
                $urlsAboveIds = $card->urls->filter(function ($value) use ($urlPos) {
                    return $value['content_position'] > $urlPos;
                })->pluck('content_id');
                $url->delete();
                $card->contents()->whereIn('content_id', $urlsAboveIds)->decrement('content_position');
                break;

            case ('embed'):
                $card->load('embeds');
                // Url::whereIn('id', $contentId)->delete();
                Embed::destroy($contentId);
                break;

            case ('text'):
                $card->load('texts');
                // Url::whereIn('id', $contentId)->delete();
                Text::destroy($contentId);
                break;
            
            case ('whiteboard'):
                $card->load('whiteboards');
                // Url::whereIn('id', $contentId)->delete();
                Whiteboard::destroy($contentId);
                break;
        }


        // $card->contents()->delete(); // card_content pivot
        $card->contents()->where('content_id', $contentId)->delete();
        // $contentPivot->delete(); // card_content pivot
        return;
    }


}
