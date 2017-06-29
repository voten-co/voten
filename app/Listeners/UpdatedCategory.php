<?php

namespace App\Listeners;

use App\Events\CategoryWasUpdated;
use App\Traits\CachableCategory;

class UpdatedCategory
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
     * @param CategoryWasUpdated $event
     *
     * @return void
     */
    public function handle(CategoryWasUpdated $event)
    {
        $this->putCategoryInTheCache($event->category);
    }
}
