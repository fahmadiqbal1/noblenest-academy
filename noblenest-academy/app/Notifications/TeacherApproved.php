<?php

namespace App\Notifications;

use App\Models\TeacherProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeacherApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly TeacherProfile $teacherProfile) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Teacher Application Has Been Approved!')
            ->greeting('Congratulations, ' . $notifiable->name . '!')
            ->line('Your application to become a teacher on Noble Nest Academy has been approved.')
            ->line('You can now create and publish courses for our learners.')
            ->action('Go to Dashboard', url('/teacher/dashboard'))
            ->line('Thank you for joining our community of educators.');
    }
}
