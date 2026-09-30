<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentVoterVerified extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Voter verification approved',
            'message' => 'Your account has been verified. You can now vote in the election.',
            'url' => route('election.details'),
        ];
    }
}
