<?php

namespace App\Notifications;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyNotification extends Notification
{
    use Queueable;

    protected $user_id;
    protected $code;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
        $this->code = random_int(100000, 999999);
        $user = Member::find($user_id);
        $user->validation_code = $this->code;
        $user->save();
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
        return (new MailMessage)
                    ->from('noreply@'.env('MAIL_SRV'))
                    ->greeting(__('home.verifyEmail.greeting'))
                    ->line(__('home.verifyEmail.p1').$this->code)
                    ->line(__('home.verifyEmail.p2'))
                    ->action(__('home.verifyEmail.btn'), route('verify.post', [
                        'member_id' => $this->user_id,
                        'vCode' => $this->code
                        ]));
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
