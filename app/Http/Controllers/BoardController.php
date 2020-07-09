<?php

namespace App\Http\Controllers;

use App\Board;
use Illuminate\Http\Request;

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
    public function show(Board $board)
    {
        return view('boards.show', compact('board'));
        // return view(route('boards.show', $board), $board);
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
    public function store()
    {
    
        
        // $board = new Board;
        // $board->title = request('title');
        // $board->owner = 'mawej1';

        // $board->save();

        Board::create($this->validateBoard());

        return redirect(route('boards.index'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function edit(Board $board)
    {

        return view('boards.edit', compact('board'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function update(Board $board)
    {

        $board->update($this->validateBoard());

        // $board->title = request('title');
        // $board->save();

        // return redirect('boards');
        return redirect($board->path());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function destroy(Board $board)
    {
        //
    }

    public function validateBoard() {
        return request()->validate([
            'title' => 'required',
            'owner' => 'required'
        ]);
    }
}
