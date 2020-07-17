<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

use App\Http\Resources\Board as BoardResource;
use App\Http\Resources\BoardCollection as BoardResourceCollection;

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
        return $user->boards;
        
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
        
        
        $user = $request->user();
        $board = Board::find($request->boardId);
        $cards = $board->cards;

        if ($board == null) {
            $this->boardNotFoundError();
        }

        $boards = $user->boards;
        // return response()->json($boards);
        
        if (!$boards->contains($board)) {
            $this->boardNoPermissionError();
        }

        // return $cards;
        
        // $cardTypes = array_values(array_unique($cards->pluck('type')->toArray())); // image, video...
        // $cardContentTypes = $this->cardTypeToContentType($cardTypes);
        // $cardContentTypes = preg_filter('/$/', 's', $cardContentTypes); //add s to types   files, todos...

        return new BoardResource($board->load('stacks.cards.files'));
    
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
    
        
        // $board = new Board;
        // $board->title = request('title');
        // $board->owner = 'mawej1';

        // $board->save();

        $validatedData = $request->validate([
            'title' => 'required|min:3',
        ]);
        
        // return [$request->title, $request->user()];
        
        $board = Board::create([
            // 'title' => $request->title,
            'title' => $request->title,
            'user_id' => $request->user()->id,
        ]);

        // Board::create($this->validateBoard());

        // return redirect(route('boards.index'));

        return $board;
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

        
        $filteredRequest = $request->except('user_id');
        
        $validatedData = $request->validate([
            'title' => 'required|min:3',
        ]);

        
        $board = Board::find($request->boardId);

        if ($board == null) {
            $this->boardNotFoundError();
        }
        
        $boards = $request->user()->boards;
        // return response()->json($boards);
        
        if ($boards->contains($board)) {
            
            $board->update($request->only('title'));

            return null;
        
        } else {
            $this->boardNoPermissionError();
        }
        
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
