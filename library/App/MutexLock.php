<?php

namespace App;

use Monolog\Logger;

class MutexLock
{

    /**
     * Checks if a lock exists, if not, creates it and sets and expiration time
     *
     * @param string $key
     * @param int $time
     * @return bool indicates if a lock created
     * @throws LockException
     */
    public static function lock($key, $time = 0)
    {
        if (!$key) {
            throw LockException('Key must be set');
        }
        $config = \Zend_Registry::get('config');
        try {
            $redis = new \Credis_Client($config->cache->redis->host, $config->cache->redis->port);
            $isCreated = $redis->setnx($key, time());
        } catch(Exception $e) {
            error_log('could not connect to redis');
        }
        if ($isCreated && $time) {
            $redis->expire($key, $time);
        }
        return $isCreated;
    }

}

class LockException extends \Exception {}
