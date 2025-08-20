<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManagerNotification extends Notification
{
    use Queueable;

    public $entityName;
    public $entity;
    public $action; // new property

    public function __construct($entityName,$entity, $action)
    {
        $this->entityName = $entityName;
        $this->entity = $entity;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'modele' => $this->entityName,
            'id'     => $this->entity->id,
            'name'   => $this->entity->name,
            'action' => $this->action, // so you can tell if it's created or updated
        ];
    }
}
