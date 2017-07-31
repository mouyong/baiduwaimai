<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConfirmOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order_id;
    public $source;

    public function __construct($order_id, $source)
    {
        $this->order_id = $order_id;
        $this->source = $source;
    }

    public function handle()
    {
        return app('baidu')->confirm($this->source, $this->order_id);
    }
}
