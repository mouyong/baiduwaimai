<?php

namespace App\Jobs;

use App\Http\Controllers\OrderController;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StatusPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $body;
    public $source;

    public function __construct($body, $source)
    {
        $this->body = $body;
        $this->source = $source;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = app(OrderController::class);
        $order->statusPush($this->body, $this->source);
    }
}
