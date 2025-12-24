<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewEntryController extends Controller
{
public function register(Request $request){
    $request->validate([
        'name'=> ['required','max:255'],
        'email' => ['required','email','max:225','unique:users'],
        'password' => ['required','confirmed'],
       
    ]);
    dd('pass');
}
}
