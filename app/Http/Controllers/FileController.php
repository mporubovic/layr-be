<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\File as FileResource;
use App\Traits\CardTraits;


class FileController extends Controller
{
    use CardTraits;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $user = $request->user();
        

        if (!$request->hasFile('file')) {
            abort(500);
            // return ['one', $request->file('content')];
            // return $this->cardNoFileUploadedError();
        } else {
            $file = $request->file;
            $fileInDatabase = $this->cardUploadedFileHandler([$file], 'text', $user->id)[0];
            // return $files;

        }
        return new FileResource($fileInDatabase);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
