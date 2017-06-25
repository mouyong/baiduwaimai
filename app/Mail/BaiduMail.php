<?php

namespace App\Mail;

use App\Traits\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BaiduMail extends Mailable
{
    use Queueable, SerializesModels;
    use SendEmail;

    public static $trace;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.baidu');
    }

    public static function trance($trace)
    {
        self::$trace = $trace;
        return new static;
    }
}
