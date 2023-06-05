<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    try {
      $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|string|min:8'
      ]);

      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password)
      ]);

      if ($user) {
        // Realiza a autenticação do usuário
        $token = auth('api')->login($user);

        return ApiResponse::success('Usuario criado com sucesso', [
          'credentials' => [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60 // 1 hora de utilização
          ]
        ], 201);
      }

      return ApiResponse::error('Houve um erro ao criar o usuario', null, 500);
    } catch (ValidationException $e) {
      return ApiResponse::invalidParams($e->errors());
    }
  }
}
