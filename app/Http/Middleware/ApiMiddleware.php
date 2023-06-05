<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiMiddleware extends BaseMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next)
  {
    try {
      // Realiza a autenticação do usuário
      $user = JWTAuth::parseToken()->authenticate();

      // Recupera o usuário atual
      $currentUser = $request->route('user');

      // Verifica se o token do usuário autenticado pertence ao usuário do path params informado
      if ($user->id !== $currentUser->id) {
        throw new Exception('Forbidden');
      }
    } catch (\Exception $e) {
      if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return ApiResponse::error('Token informado invalido', null, 401);
      } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return ApiResponse::error('Token informado expirado', null, 401);
      } elseif ($e->getMessage() === "Forbidden") {
        return ApiResponse::error('Forbidden', null, 403);
      } else {
        return ApiResponse::error('Token de autorizacao nao encontrado', null, 401);
      }
    }

    return $next($request);
  }
}
