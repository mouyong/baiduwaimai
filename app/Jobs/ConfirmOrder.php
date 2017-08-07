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
    
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        return app('baidu', (array) $this->data['source'])->confirm($this->data['source'], $this->data['order_id']);
    }
}
