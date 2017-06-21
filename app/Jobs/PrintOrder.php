<?php

namespace App\Jobs;

use App\Traits\Printer;
use App\Traits\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PrintOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Printer, Shop;

    private $order_id;
    private $content;
    private $source;

    /**
     * Create a new job instance.
     *
     * @param string $source
     * @param string $order_id
     * @param string $content
     */
    public function __construct($source, $order_id, $content = '')
    {
        $this->source = $source;
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
        $shopInfo = self::shopInfoFromCache($this->source);

        Printer::print($this->content, $shopInfo);

        dispatch(new CreateOrder($this->order_id, $this->content, $shopInfo));
    }
}
