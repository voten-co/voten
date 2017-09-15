<?php

Artisan::command('update-submissions', function () {
    $submissions = \App\Submission::all();

    foreach ($submissions as $submission) {
        $submission->update([
            'url'    => $submission->type === 'link' ? $submission->data['url'] : config('app.url').'/c/'.$submission->category_name.'/'.$submission->slug,
            'domain' => $submission->type === 'link' ? domain($submission->data['url']) : null,
        ]);
    }

    $this->info($submissions->count().' records have been successfully updated.');
})->describe('Fill "url" and "domain" fields.');
