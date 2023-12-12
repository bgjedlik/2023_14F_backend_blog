<?php

namespace App\Http\Controllers;
// php artisan make:controller AuthController
use App\Models\User;
use Illuminate\Http\Request;


class AuthController extends BaseController
{
    public function register(Request $request){
        $input = $request->all();
        $user = User::create($input); // insert into ...
        return $this->sendResponse('','Sikeres regisztráció');
    }
}
