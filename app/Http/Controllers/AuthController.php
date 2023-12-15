<?php

namespace App\Http\Controllers;

// php artisan make:controller AuthController
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
  public function register(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email|unique:App\Models\User,email',
      'password' => 'required',
      'password_confirmation' => 'required|same:password',
      'role' => 'required|integer',
    ], [
      'name.required' => 'Kötelező kitölteni!',
      'email.required' => 'Kötelező kitölteni!',
      'email.email' => 'Hibás email cím!',
      'email.unique' => 'Az email cím már létezik!',
      'password.required' => 'Kötelező kitölteni!',
      'password_confirmation.required' => 'Kötelező kitölteni!',
      'password_confirmation.same' => 'A két a jelszó nem egyforma!',
      'role.required' => 'Kötelező kitölteni!',
      'role.integer' => 'Csak szám lehet!',
    ]);

    if ($validator->fails()) {
      return $this->sendError('Bad Request', $validator->errors(), 400);
    }

    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $user = User::create($input); // insert into ...

    $success['name'] = $user->name;
    $success['token'] = $user->createToken('Secret')->plainTextToken;

    return $this->sendResponse($success, 'Sikeres regisztráció');
  }

  public function login(Request $request)
  {
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
      // sikeres bejelentkezés
      $user = Auth::user();
      $success['name'] = $user->name;
      $success['id'] = $user->id;
      $success['role'] = $user->role;
      $success['token'] = $user->createToken('Secret')->plainTextToken;

      return $this->sendResponse($success, 'Sikeres bejelentkezés');

    } else {
      // hibás bejelentkezés
      return $this->sendError('Unauthorized', ['error' => 'Sikertelen bejelentkezés!'], 401);
    }
  }

  public function logout(Request $request){
    auth()->user()->tokens()->delete();
    return $this->sendResponse('', 'Sikeres kijelentkezés');
  }
}
