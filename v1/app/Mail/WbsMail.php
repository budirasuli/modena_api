<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WbsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = env('WHISTLEBLOWER_EMAIL_SUBJECT','MODENA').' - '.$this->data['report_number'];

        $build   = $this->subject($subject);

        foreach ($this->data['file'] as $key => $file) {
            $build = $build->attach(
                $file->getRealPath(), 
                [
                    'as' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                ]
            );
        }

        $build = $build->view(
            'emails.wbs'
        )
        ->with([
            'data' => $this->data
        ]);

        return $build;
        // return $this->view('emails.wbs');
    }
}
