<?php

namespace App;

use Auth;
use ReflectionClass;

trait RecordsActivity
{
    /**
     * Register the necessary event listeners.
     *
     * @return void
     */
    protected static function bootRecordsActivity()
    {
        foreach (static::getModelEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }
    }

    /**
     * Record activity for the model.
     *
     * @param string $event
     *
     * @return void
     */
    public function recordActivity($event)
    {
        Activity::create([
            'subject_id'   => $this->id,
            'ip_address'   => $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'user_agent'   => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'country'      => $_SERVER['HTTP_CF_IPCOUNTRY'] ?? 'unknown',
            'subject_type' => get_class($this),
            'name'         => $this->getActivityName($this, $event),
            'user_id'      => Auth::user()->id,
        ]);
    }

    /**
     * Prepare the appropriate activity name.
     *
     * @param mixed  $model
     * @param string $action
     *
     * @return string
     */
    protected function getActivityName($model, $action)
    {
        $name = strtolower((new ReflectionClass($model))->getShortName());

        return "{$action}_{$name}";
    }

    /**
     * Get the model events to record activity for.
     *
     * @return array
     */
    protected static function getModelEvents()
    {
        if (isset(static::$recordEvents)) {
            return static::$recordEvents;
        }

        return [
            'created', 'deleted', 'updated',
        ];
    }
}
