<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\DataTables\UserDataTable;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserDataTable $dataTable, Request $request)
    {
        $page_title = "Users";
        return $dataTable->render('backend.pages.users.index', compact('page_title'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function getUserData()
    {
        $users = User::whereHas("roles", function($q){ $q->where("name", "user"); })->orderBy('id', 'DESC')->get();
       
        $i = 0;
        foreach ($users as $user) {
            $user_data[$i]['name'] = $user->name;
            $user_data[$i]['email'] = $user->email;
            $user_data[$i]['mobile_number'] = $user->mobile_number;
            $user_data[$i]['country'] = $user->country;
           
            $i++;
        }
        return \DataTables::of($user_data)->make();
    }
}
