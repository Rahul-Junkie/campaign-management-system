<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Campaign;

class CampaignProcessed extends Notification
{
    use Queueable;

    protected $campaign;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Campaign $campaign
     * @return void
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Campaign Processed Successfully')
            ->line('The campaign "' . $this->campaign->name . '" has been successfully processed.')
            ->line('Total contacts processed: ' . $this->campaign->processed_contacts)
            ->line('Thank you for using our application!');
    }
}
