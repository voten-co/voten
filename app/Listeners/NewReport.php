<?php

namespace App\Listeners;

use App\Events\ReportWasCreated;
use App\Mail\BackendNewReport;
use App\Notifications\CommentReported;
use App\Notifications\SubmissionReported;
use App\Permissions;
use App\Traits\CachableCategory;
use App\User;

class NewReport
{
    use CachableCategory, Permissions;

    /**
     * Handle the event.
     *
     * @param ReportWasCreated $event
     *
     * @return void
     */
    public function handle(ReportWasCreated $event)
    {
        $category = $this->getCategoryById($event->report->category_id);

        $category_mods = $category->moderators;

        foreach ($category_mods as $user) {
            if ($event->report->reportable_type == 'App\Submission') {
                $user->notify(new SubmissionReported($category, $event->report->submission));
            } elseif ($event->report->reportable_type == 'App\Comment') {
                $user->notify(new CommentReported($category, $event->report->comment));
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
