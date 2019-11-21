<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf, $subject)
    {
        $this->pdf = $pdf;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.report')->from(env('MAIL_FROM_ADDRESS', 'admin@casia.xyz'), "Report")
            ->attachData($this->pdf->output(), 'report.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
