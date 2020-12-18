<?php

namespace App\Http\Controllers;

// use Illuminate\Foundation\Application;

use App\Models\Board;
use App\Models\Stack;
use App\Models\Tag;
use Illuminate\Http\Request;

use App\Http\Resources\Board as BoardResource;
use App\Http\Resources\BoardCollection as BoardResourceCollection;

use Illuminate\Support\Arr;

use App\Subdomain;



class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // return $user->with('boards.stacks.cards.files.content')->get();
        // return $user->boards;
        return new BoardResourceCollection($user->boards);
        // return new BoardResourceCollection($user->boards);
        
        // $boards = Board::orderBy('updated_at', 'desc')->get();

        // // return $boards->all();

        // return view('boards.boards', compact('boards'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // return view('boards.show', compact('board'));

        // return view(route('boards.show', $board), $board);
    
        // return $request->boardId;
        
        // $validatedData = $request->validate([
        //     'boardId' => 'numeric',
        // ]);
        
        
        // $user = $request->user();
        // $board = Board::find($request->boardId);
        $board = Board::find($request->boardId);

        if ($board == null) {
            $this->boardNotFoundError();
        }
        // return response()->json($boards);
        // if ($request->user() !== null) {
        $user = $request->user();
        
        if (!$user->stacks()->boards->contains($board)) {
            $this->boardNoPermissionError();
        }
        // } else {
        //     $requestUrl = $request->headers->get('origin');
        //     // $subdomainName = explode('.', $requestUrl)[0];
        //     $parsedUrl = parse_url($requestUrl);
        //     $parts = explode('.', $parsedUrl['host']);

        //     if (\App::environment('production')) $subdomainName = $parts[count($parts)-3];
        //     if (\App::environment('local')) $subdomainName = 'local';
            
        //     $subdomain = Subdomain::where('name', $subdomainName)->first();

        //     if ($subdomain === null) abort(404);

        //     if (!$subdomain->boards->contains($board)) {
        //         abort(404);
        //     }
        // }

        // return $cards;
        
        // $cardTypes = array_values(array_unique($cards->pluck('type')->toArray())); // image, video...
        // $cardContentTypes = $this->cardTypeToContentType($cardTypes);
        // $cardContentTypes = preg_filter('/$/', 's', $cardContentTypes); //add s to types   files, todos...

        // return new BoardResource($board->load('stacks.cards.files'));
        // return new BoardResource($board->load('stacks.cards.files'));
        return new BoardResource($board->load(['cards.files', 
                                                'cards.todos', 
                                                'cards.user', 
                                                'cards.urls', 
                                                'cards.embeds',
                                                'cards.texts',
                                                'cards.whiteboards',
                                                'tags'
                                                ]));
        // return new BoardResource($board->load(['stacks.cards.files', 'stacks.cards.todos']));
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        // $board = new Board;
        // $board->title = request('title');
        // $board->owner = 'mawej1';

        // $board->save();

        $validatedData = $request->validate([
            'title' => 'required|min:3',
            'tag' => 'sometimes|integer',
            'stackId' => 'required|integer'
        ]);

        $user = $request->user();

        // return $requestSettings;

        
        // return [$request->title, $request->user()];
        
        $board = $user->boards()->create(['title' => $request->title]);
        
        
        
        if (isset($request->tag)) {
            $tag = Tag::find($request->tag);
            if ($user->tags->contains($tag)) {
                $board->tags()->attach($tag);
            }
        }

        $user->stacks()->find($request->stackId)->boards()->attach($board);
        // return $board;
        return new BoardResource($board);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    
    
     // public function edit(Board $board)
    // {

    //     return view('boards.edit', compact('board'));
    // }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $user = $request->user();
        $board = Board::find($request->boardId);

        if ($board == null) {
            return $this->boardNotFoundError();
        }

        if (!$board->stacks[0]->users->contains($user)) {
            return $this->boardNoPermissionError();
        }

        $attributes = $request->only('title');
        
        $updatedProperties = $attributes;
        
        if ($request->settings) {
            $requestSettings = Arr::only($request->settings, ['layout']);
            $originalSettings = $board->settings ?? [];
            $settings = array_merge_recursive_distinct($originalSettings, $requestSettings);
            $updatedProperties = $updatedProperties + compact("settings");

        }

        $board->fill($updatedProperties)->save();
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        
        $board = Board::find($request->boardId);

        if ($board == null) {
            $this->boardNotFoundError();
        }

        $boards = $request->user()->boards;
        // return response()->json($boards);
        
        if ($boards->contains($board)) {
            $board->delete();
            return null;
        } else {
            $this->boardNoPermissionError();
        }
    }

    public function validateBoard() {
        return request()->validate([
            'title' => 'required',
            'owner' => 'required'
        ]);
    }

    public function boardNotFoundError() {

        abort(403, 'The authenticated user does not have access to the requested board.');

    }

    public function boardNoPermissionError() {

        abort(403, 'The authenticated user does not have access to the requested board.');

    }
}
