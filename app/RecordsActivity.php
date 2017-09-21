<?php

namespace App;

use Auth;
use ReflectionClass;
use WhichBrowser\Parser;

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
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
            return;
        }

        $user_agent_parser = new Parser($_SERVER['HTTP_USER_AGENT']);

        Activity::create([
            'subject_id'           => $this->id,
            'ip_address'           => $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'user_agent'           => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'country'              => $_SERVER['HTTP_CF_IPCOUNTRY'] ?? 'unknown',
            'device'               => $user_agent_parser->device->model ?? 'unknown',
            'os'                   => optional($user_agent_parser->os)->toString() ?? 'unknown',
            'browser_name'         => $user_agent_parser->browser->name ?? 'unknown',
            'browser_version'      => optional($user_agent_parser->browser->version)->toString() ?? 'unknown',
            'subject_type'         => get_class($this),
            'name'                 => $this->getActivityName($this, $event),
            'user_id'              => Auth::id(),
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
