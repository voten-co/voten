<?php

namespace App\Listeners;

use App\Events\ReportWasCreated;
use App\Mail\BackendNewReport;
use App\Notifications\CommentReported;
use App\Notifications\SubmissionReported;
use App\Permissions;
use App\Traits\CachableChannel;
use App\User;

class NewReport
{
    use CachableChannel, Permissions;

    /**
     * Handle the event.
     *
     * @param ReportWasCreated $event
     *
     * @return void
     */
    public function handle(ReportWasCreated $event)
    {
        $channel = $this->getChannelById($event->report->channel_id);

        $channel_mods = $channel->moderators;

        foreach ($channel_mods as $user) {
            if ($event->report->reportable_type == 'App\Submission') {
                $user->notify(new SubmissionReported($channel, $event->report->submission));
            } elseif ($event->report->reportable_type == 'App\Comment') {
                $user->notify(new CommentReported($channel, $event->report->comment));
            }
        }

        $this->notifyVotenAdmins($event->report);
    }

    /**
     * Notify Voten amdins.
     *
     * @param \App\Report $report
     *
     * @return void
     */
    protected function notifyVotenAdmins($report)
    {
        $admins_ids = $this->getVotenAdministrators();

        $admins = User::whereIn('id', $admins_ids)->get();

        foreach ($admins as $admin) {
            \Mail::to($admin->email)->queue(new BackendNewReport($report));
        }
    }
}
