<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DynamicNotification extends Notification
{
   // use Queueable;


    protected $template;
    protected $data;

    public function __construct($template, array $data = [])
    {
        $this->template = $template;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->template->title,
            'content' => $this->parseContent(),
            'type' => $this->template->type,
        ];
    }

    protected function parseContent()
    {
        $content = $this->template->content;

        foreach ($this->data as $key => $value) {
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
        }

        return $content;
    }
}
