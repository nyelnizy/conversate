<?php

namespace Amot\Conversate;

class Event
{
    private static $events;
    private static \Ds\Queue $queue;
    public static function listen($event_name,$callback){
        self::$events[$event_name] = $callback;
    }

    public static function signal($event){
        self::$queue = $GLOBALS['api_queue'];
        if(!self::$queue->isEmpty()){
            self::$events[$event](self::$queue->pop());
        }
    }
}
