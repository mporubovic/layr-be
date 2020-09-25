<?php

namespace App\Http\Controllers;

use App\Http\Resources\Student as StudentResource;
use App\Http\Resources\StudentCollection as StudentResourceCollection;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        $subdomain = $user->subdomains->first();

        $students = $subdomain->users->except($user->id);

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
        $subdomain = $user->subdomains->first();
        // return strlen($request->password);
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'digits:6'],
        ])->validate();

        $studentName = $request->name;
        $studentEmail = $request->email;
        $studentPassword = $request->password;

        $newStudent = User::create([
            'name' => $studentName,
            'email' => $studentEmail,
            'password' => Hash::make($studentPassword),
        ]);

        $role = Role::find(2);

        $newStudent->roles()->save($role);

        $subdomain->users()->attach($newStudent);

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
        $subdomain = $user->subdomains->first();

        $student = $subdomain->users()->find($request->studentId);

        return new StudentResource($student->load('boards'));


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
        $subdomain = $user->subdomains->first();

        $student = $subdomain->users()->find($request->studentId);

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
