<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Expense;
use App\Models\User;
use App\Notifications\NewExpenseNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(User $user)
  {
    if (Gate::denies('expenses.viewAny')) {
      return ApiResponse::error('Forbidden', null, 403);
    }

    $expenses = $user->expenses;

    return response()->json($expenses);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, User $user)
  {
    if (Gate::denies('expenses.create')) {
      return ApiResponse::error('Forbidden', null, 403);
    }

    try {
      $request->validate([
        'description' => 'required|string|max:191',
        'amount' => 'required|numeric',
        'occurred_at' => 'required|date|before:tomorrow|date_format:Y-m-d'
      ]);

      $expense = new Expense;
      $expense->description = $request->description;
      $expense->amount = (float) $request->amount;
      $expense->occurred_at = Date::createFromDate($request->occurred_at);

      $expense->user()->associate($user);

      if (!$expense->save()) {
        return ApiResponse::error('Erro ao cadastrar a despesa', null, 500);
      }

      $user->notify((new NewExpenseNotification($user, $expense)));

      $expense->makeHidden('user');
      return response()->json($expense);
    } catch (ValidationException $e) {
      return ApiResponse::invalidParams($e->errors());
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(User $user, $id)
  {
    $expense = Expense::findOrFail($id);

    if (Gate::denies('expenses.view', $expense)) {
      return ApiResponse::error('Forbidden', null, 403);
    }

    return response()->json($expense);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    try {
      $request->validate([
        'description' => 'required|string|max:191',
        'amount' => 'required|numeric',
        'occurred_at' => 'required|date|before:tomorrow|date_format:Y-m-d'
      ]);

      $expense = Expense::findOrFail($id);

      if (Gate::denies('expenses.update', $expense)) {
        return ApiResponse::error('Forbidden', null, 403);
      }

      $expense->description = $request->description ?? $expense->description;
      $expense->amount = (float) $request->amount ?? $expense->amount;
      $expense->occurred_at = $request->occurred_at ?? $expense->occurred_at;

      if (!$expense->update()) {
        return ApiResponse::error('Erro ao atualizar a despesa', null, 500);
      }

      return ApiResponse::success('Despesa atualizada com sucesso');
    } catch (ValidationException $e) {
      return ApiResponse::invalidParams($e->errors());
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user, $id)
  {
    $expense = Expense::findOrFail($id);

    if (Gate::denies('expenses.delete', $expense)) {
      return ApiResponse::error('Forbidden', null, 403);
    }

    if (!$expense->delete()) {
      return ApiResponse::error('Erro ao excluir a despesa', null, 500);
    }

    return ApiResponse::error('Despesa excluida com sucesso', null, 200);
  }
}
