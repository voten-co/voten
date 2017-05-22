<?php

namespace App\Listeners;

use App\Traits\CachableCategory;
use App\Events\CategoryWasUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdatedComment
{
    use CachableCategory;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CategoryWasUpdated  $event
     * @return void
     */
    public function handle(CategoryWasUpdated $event)
    {
        $this->putCategoryInTheCache($event->category);
    }
}
