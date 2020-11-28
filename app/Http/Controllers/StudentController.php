<?php

namespace App\Http\Controllers;

use App\Http\Resources\Student as StudentResource;
use App\Http\Resources\StudentCollection as StudentResourceCollection;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Mail\InviteCreated;
use App\Invite;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $group = $user->groups->first();

        $students = $group->users->except($user->id)->load('roles');

        return new StudentResourceCollection($students);

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
        $group = $user->groups->first();
        // return strlen($request->password);
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
        ])->validate();

        $newStudent = User::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $role = Role::find(2);

        $newStudent->roles()->save($role);

        $group->users()->attach($newStudent);

        $token = Str::random(32);

        $invite = Invite::create(['user_id' => $newStudent->id, 'token' => $token]);

        Mail::to($request->email)->send(new InviteCreated($token, $user, $newStudent));

        return new StudentResource($newStudent);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $group = $user->groups->first();

        $student = $group->users()->find($request->studentId);

        return new StudentResource($student->load('stacks.boards'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $group = $user->groups->first();

        $student = $group->users()->find($request->studentId);

        $attributes = $request->only('name', 'password');

        if (array_key_exists('password', $attributes)) {
            $attributes['password'] = Hash::make($attributes['password']); 
            DB::table('sessions')->where('user_id', $student->id)->delete();
        }

        $student->fill($attributes)->save();



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
