<?php

namespace App\Jobs;

use App\Models\Record;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * OrderRecord constructor.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $data = Record::orderId($this->data['order_id'])->first();
        if ($data) {
            $duplicate_count = $data['duplicate_count'] + 1;
            Record::orderId($this->data['order_id'])->update(compact('duplicate_count'));
        } else {
            Record::create($this->data);
        }
        return true;
    }
}
