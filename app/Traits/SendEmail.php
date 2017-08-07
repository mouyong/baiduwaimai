<?php
namespace App\Traits;

trait SendEmail
{
    protected $emails;

    public function sendTo()
    {
        foreach (self::emails() as $email) {
            \Mail::to($email)->send(new static);
        }
    }

    public function emails()
    {
        $this->emails = (array) bug_email();
        return $this->emails;
    }
}
