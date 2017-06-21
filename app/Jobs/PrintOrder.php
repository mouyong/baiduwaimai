<?php

namespace App\Jobs;

use App\Traits\Printer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PrintOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Printer;

    protected $order_id;
    protected $content;

    /**
     * Create a new job instance.
     *
     * @param string $order_id
     * @param string $content
     */
    public function __construct(string $order_id, $content = '')
    {
        $this->order_id = $order_id;
        $this->content = $content;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Printer::print($this->content);
    }
}
