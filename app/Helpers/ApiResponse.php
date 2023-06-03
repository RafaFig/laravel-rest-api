<?php

namespace App\Helpers;

class ApiResponse
{
  public static function success(string $message, $data = null, $statusCode = 200)
  {
    return response()->json(
      array_merge([
        'success' => true,
        'message' => $message
      ], $data),
      $statusCode
    );
  }

  public static function error(string $message, $data = null, $statusCode = 400)
  {
    return response()->json(
      array_merge([
        'success' => false,
        'message' => $message,
      ], $data ?? []),
      $statusCode
    );
  }

  public static function invalidParams(array $errors, $statusCode = 400)
  {
    return response()->json([
      'success' => false,
      'message' => 'Parametros obrigatorios invalidos',
      'errors' => $errors
    ], $statusCode);
  }
}
