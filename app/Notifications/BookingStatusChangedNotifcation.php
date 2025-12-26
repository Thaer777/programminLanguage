<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusChangedNotifcation extends Notification
{
    use Queueable;
    protected $booking,$old_status;
    /**
     * Create a new notification instance.
     */
    public function __construct($booking,$old_status)
    {
        $this->booking = $booking;
        $this->old_status = $old_status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
      public function todataBase(object $notifiable): array
    {
        return
          [
            'booking_id' => $this->booking->id,
            'old_status' => $this->old_status,
            'new_status' => $this->booking->status,
            'message'    => "Your booking status has changed from {$this->old_status} to {$this->booking->status}."
          ];
    }



    // public function toArray(object $notifiable): array
    // {
    //     return [

    //     ];
    // }
}
