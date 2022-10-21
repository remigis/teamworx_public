<?php

namespace App\Http\Controllers;

use App\Models\User;

class ApiController extends Controller
{
    public function getusers($string)
    {
        $users = User::where('name', 'LIKE', "%" . $string . "%")->orWhere('email', 'LIKE', "%" . $string . "%")->get();
        $userList = [];
        foreach ($users as $user) {
            $userList[] = ['userId' => $user->id, 'userNameAndEmail' => $user->name . ", " . $user->email];
        }
        return response()->json($userList);
    }
}
