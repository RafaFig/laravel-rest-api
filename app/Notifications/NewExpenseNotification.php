<?php

namespace App\Notifications;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Date;

class NewExpenseNotification extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * User's instance
   * 
   * @var User $user
   */
  private $user;

  /**
   * Expense's instance
   * 
   * @var Expense $expense
   */
  private $expense;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(User $user, Expense $expense)
  {
    $this->user = $user;
    $this->expense = $expense;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    $userFirstName = explode(' ', $this->user->name)[0];
    $createdAtFormatted = Date::createFromDate($this->expense->created_at)
      ->format('d/m/Y \\à\\s H:i');

    return (new MailMessage)
      ->greeting("Olá, {$userFirstName}! Belezinha?")
      ->subject('Despesa cadastrada')
      ->line("Passando apenas para confirmar que a sua despesa foi cadastrada com sucesso em {$createdAtFormatted}.")
      ->action('Acessar plataforma', url('/'))
      ->line('Agradecemos imensamente por você estar utilizando o nosso sistema e qualquer problema não hesite em entrar em contato conosco!');
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
