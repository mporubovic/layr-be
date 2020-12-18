<?php

namespace App\Http\Controllers;

use App\Models\Stack;
use Illuminate\Http\Request;

use App\Http\Resources\StackCollection as StackResourceCollection;
use App\Http\Resources\Stack as StackResource;
use Illuminate\Support\Arr;


class StackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return new StackResourceCollection($request->user()->stacks);
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
        $validatedData = $request->validate([
            'title' => 'required|min:3',
            'studentId' => 'required|integer',

        ]);

        $user = $request->user();
        $student = $user->groups->first()->users()->find($request->studentId);
        
        
        $stack = $user->stacks()->create(['title' => $request->title, 'user_id' => $user->id]);
        
        $student->stacks()->attach($stack);

        $newBoard = $user->boards()->create();

        $stackBoardLast = $stack->boards()->max('position');
        $offset = $stackBoardLast === null ? 0 : $stackBoardLast + 1;
        
        $stack->boards()->attach($newBoard, ['position' => $offset]);

        return new StackResource($stack);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stack  $stack
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $stack = Stack::find($request->stackId);

        if ($stack == null) {
            $this->stackNotFoundError();
        }
        // return response()->json($stacks);
        if ($request->user() !== null) {
            $user = $request->user();
            
            if (!$user->stacks->contains($stack)) {
                $this->stackNoPermissionError();
            }
        }

        return new StackResource($stack->load('boards.cards.files', 
                                                'boards.cards.todos', 
                                                'boards.cards.user', 
                                                'boards.cards.urls', 
                                                'boards.cards.embeds',
                                                'boards.cards.texts',
                                                'boards.cards.whiteboards',));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Stack  $stack
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stack $stack)
    {
        $validatedData = $request->validate([
            'title' => 'required|min:1',
        ]);

        
        $stack = Stack::find($request->stackId);


        if ($stack == null
        ) {
            avirt(404);
        }
        
        $user = $request->user();

        if (!$user->stacks->contains($stack)) {
            avirt(404);

        }

        $fields = $request->only('title');
        $stack->fill($fields)->save();
        
        return;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stack  $stack
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stack $stack)
    {
        //
    }
}
