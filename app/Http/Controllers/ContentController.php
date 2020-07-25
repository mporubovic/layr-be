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

use App\Traits\CardTraits;
use App\Traits\CardErrors;

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

        if ($card->user_id != $user->id) {
            return $this->cardNoPermissionError();
        }
        
        // $cards = $request->user()->cards;

        
        $cardType = $card->type;
        $cardContentType = $this->cardTypeToContentType($cardType);


        switch($cardContentType) {
            case('file'):
                if (!$request->hasFile('content')) {
                    return $this->cardNoFileUploadedError();
                }
                
                $files = $request->file('content');
                $cardContent = $this->cardFileHandler($files, $cardType);        
                $eagerLoadContent = 'files';
                // return new FileResourceCollection($filesInDatabse); // doesn't return pivot table info
            
                break;

            case('todo'):
                
                $todos = $request->content;
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

        $this->cardContentHandler($cardContent, $card, $cardContentType);

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
        $attributes = $request->attributes;

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

                $filteredAttributes = $request->only('attributes.body');
                try {
                    $todo = Todo::findOrFail($contentId);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
                $todo->fill($filteredAttributes['attributes'])->save();

                break;

            case ('url'):

                $filteredAttributes = $request->only('attributes.path');
                try {
                    $url = Url::findOrFail($contentId);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
                $url->fill($filteredAttributes['attributes'])->save();

                break;
        }

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


}
