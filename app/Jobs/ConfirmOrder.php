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

    public $data;
    public $orderId;
    public $source;

    public function __construct($data, $orderId, $source)
    {
        $this->data = $data;
        $this->orderId = $orderId;
        $this->source = $source;
    }

    public function handle()
    {
        $baidu = app('baidu');
        return $baidu->confirm($this->orderId, $this->source);
    }
}
