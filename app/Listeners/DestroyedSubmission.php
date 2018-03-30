<?php

namespace App\Listeners;

use App\Events\SubmissionWasDeleted;
use App\Photo;
use App\Report;
use App\Traits\CachableChannel;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use Illuminate\Support\Facades\Storage;

class DestroyedSubmission
{
    use CachableUser, CachableSubmission, CachableChannel;

    /**
     * Handle the event.
     *
     * @param SubmissionWasDeleted $event
     *
     * @return void
     */
    public function handle(SubmissionWasDeleted $event)
    {
        $this->updateChannelSubmissionsCount($event->submission->channel_id, -1);

        if ($event->deletedByAuthor) {
            $this->deletedByAuthor($event);

            return;
        }

        $this->deletedByModerator($event);
    }

    /**
     * Handle event, if record was deleted by its author.
     *
     * @param $event
     */
    protected function deletedByAuthor($event)
    {
        $this->updateUserSubmissionsCount($event->submission->user_id, -1);
        $this->removeSubmissionFromCache($event->submission);

        Report::where([
            'reportable_id' => $event->submission->id,
            'reportable_type' => 'App\Submission',
        ])->forceDelete();

        if ($event->submission->type == 'img') {
            Photo::where('submission_id', $event->submission->id)->forceDelete();

            Storage::delete([
                'submissions/img/' . str_after($event->submission->data['path'], 'submissions/img/'),
                'submissions/img/thumbs/' . str_after($event->submission->data['thumbnail_path'], 'submissions/img/thumbs/'),
            ]);
        } elseif ($event->submission->type == 'link') {
            if ($event->submission->data['img'] !== null && $event->submission->data['thumbnail'] !== null) {
               Storage::delete([
                   'submissions/link/' . str_after($event->submission->data['img'], 'submissions/link/'),
                   'submissions/link/thumbs/' . str_after($event->submission->data['thumbnail'], 'submissions/link/thumbs/'),
               ]);
            }
        } 
    }

    /**
     * Handle event, if record was deleted by a moderator.
     *
     * @param $event
     */
    protected function deletedByModerator($event)
    {
        $this->putSubmissionInTheCache($event->submission);

        // remove all the reports related to this model
        Report::where([
            'reportable_id' => $event->submission->id,
            'reportable_type' => 'App\Submission',
        ])->delete();
    }
}
