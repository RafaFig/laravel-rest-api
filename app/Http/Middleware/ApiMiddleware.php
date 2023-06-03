<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
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
      $user = JWTAuth::parseToken()->authenticate();
    } catch (\Exception $e) {
      if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return ApiResponse::error('Token informado invalido', null, 401);
      } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return ApiResponse::error('Token informado expirado', null, 401);
      } else {
        return ApiResponse::error('Token de autorizacao nao encontrado', null, 401);
      }
    }

    return $next($request);
  }
}
