<?php

namespace App\Jobs;

use App\Traits\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Input;

class ConfirmOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Order;

    protected $order_id;

    /**
     * Create a new job instance.
     *
     * @param string $order_id
     */
    public function __construct($order_id, $data)
    {
        $this->set_baidu();
        $this->ticket = $data['ticket'];

        $this->order_id = $order_id;
    }

    /**
     * Execute the job.
     *
     * @return bool|mixed
     */
    public function handle()
    {
        return self::confirm($this->order_id, $this->ticket);
    }
}
