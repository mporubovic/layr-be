<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Stack;
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
        return new BoardResourceCollection($user->boards->load('subdomains'));
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
        if ($request->user() !== null) {
            $user = $request->user();
            
            if (!$user->boards->contains($board)) {
                $this->boardNoPermissionError();
            }
        } else {
            $requestUrl = $request->headers->get('origin');
            // $subdomainName = explode('.', $requestUrl)[0];
            $parsedUrl = parse_url($requestUrl);
            $parts = explode('.', $parsedUrl['host']);

            if (App::environment('production')) $subdomainName = $parts[count($parts)-3];
            if (App::environment('local')) $subdomainName = 'local';
            
            $subdomain = Subdomain::where('name', $subdomainName)->first();

            if ($subdomain === null) abort(404);

            if (!$subdomain->boards->contains($board)) {
                abort(404);
            }
        }


        // return $cards;
        
        // $cardTypes = array_values(array_unique($cards->pluck('type')->toArray())); // image, video...
        // $cardContentTypes = $this->cardTypeToContentType($cardTypes);
        // $cardContentTypes = preg_filter('/$/', 's', $cardContentTypes); //add s to types   files, todos...

        // return new BoardResource($board->load('stacks.cards.files'));
        // return new BoardResource($board->load('stacks.cards.files'));
        return new BoardResource($board->load(['stacks.cards.files', 
                                                'stacks.cards.todos', 
                                                'stacks.cards.user', 
                                                'stacks.cards.urls', 
                                                'stacks.cards.embeds',
                                                'stacks.cards.texts',
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
            'studentId' => 'sometimes|integer',
            'settings' => 'required|json',
            'public' => 'sometimes|boolean'
        ]);

        $user = $request->user();
        $requestSettings = Arr::only(json_decode($request->settings, true), ['dimensions']);

        // return $requestSettings;

        
        // return [$request->title, $request->user()];
        
        $board = $user->boards()->create(['title' => $request->title,
                                            'settings' => $requestSettings,    
                                            ]);
        
        
        if ($request->public) {
            $user->subdomains->first()->boards()->attach($board);
        } 
        
        
        if (isset($request->studentId)) {
            $user->subdomains->first()->users()->find($request->studentId)->boards()->attach($board);
        }

        // $board = Board::create([
        //     // 'title' => $request->title,
        //     'title' => $request->title,
        //     'user_id' => $request->user()->id,
        // ]);

        $stack = $user->stacks()->create();



        $board->stacks()->attach($stack);
        // Board::create($this->validateBoard());

        // return redirect(route('boards.index'));

        // return $board;
        return new BoardResource($board->load('subdomains'));
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

        
        // $filteredRequest = $request->except('user_id');
        
        $validatedData = $request->validate([
            'title' => 'required|min:1',
        ]);

        
        $board = Board::find($request->boardId);


        if ($board == null
        ) {
            return $this->boardNotFoundError();
        }
        
        $user = $request->user();
        

        if (!$user->boards->contains($board)) {
            return $this->boardNoPermissionError();
        }

        $fields = $request->only('title');
        // $fields = json_decode(stripslashes($fields), true);
        // return json_decode(stripslashes($request->fields), true);
        // $n = board::where('id', $board->id)
        //     ->update($fields);
        $board->fill($fields)->save();
        
        return;
        // return new BoardResource($board);
        
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
