<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\CompanyCreateMail;
use Mail;

class SendEmailQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sendMail;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($sendMail,$data)
    {
        $this->sendMail = $sendMail;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $details = [
            'title' => 'Congratulation New Compnay is created',
            'body' => "Name : ".$this->data['name']
        ];
        
        $email = new CompanyCreateMail($details);
        Mail::to($this->sendMail)->send($email);
    }
}
