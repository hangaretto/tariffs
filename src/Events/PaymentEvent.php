<?php namespace Magnetar\Tariffs\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Event;

class PaymentEvent extends Event implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $response;
    public $category = 'default';

    public $created_at;
    public $updated_at;

    /**
     * MagnetarLogEvent constructor.
     *
     * @param array $response
     */
    public function __construct($response)
    {
        $this->response = $response;
        $this->updated_at = $this->created_at = Carbon::now()->toDateTimeString();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('magnetar_tariffs_payment');
    }
}
