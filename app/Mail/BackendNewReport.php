<?php

namespace App\Mail;

use App\Traits\CachableCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BackendNewReport extends Mailable
{
    use Queueable, SerializesModels, CachableCategory;

    public $report;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($report)
    {
        $this->report = $report;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $report = $this->report;

        $type = $report->reportable_type == 'App\Submission' ? 'submission' : 'comment';

        $category = $this->getCategoryById($report->category_id);

        return $this->markdown('emails.backend.new-report', compact('report', 'type', 'category'))
                    ->subject('New '.$type.' report.');
    }
}
