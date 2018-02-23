<?php

namespace MutexLock;

class Lock
{

    /**
     * 
     */
    private static $_logger = null;

    /**
     * @var array
     */
    private static $_redisConfig = ['host' => 'localhost', 'port' => '6379'];

    /**
     *
     */
    public static function init(array $params = [])
    {
        if(isset($params['logger'])) {
            self::$_logger = $params['logger'];
        }
        if (isset($params['host']) || isset($params['port'])) {
            self::$_redisConfig['host'] = $params['host'];
            self::$_redisConfig['port'] = $params['port'];
        }
    }

    /**
     * Checks if a lock exists, if not, creates it and sets and expiration time
     *
     * @param string $key
     * @param int $time
     * @return bool indicates if a lock created
     * @throws LockException
     */
    public static function set($key, $time = 0)
    {
        if (!$key) {
            throw LockException('Key must be set');
        }
        try {
            $redis = new \Credis_Client(self::$_redisConfig['host'], self::$_redisConfig['port']);
            $isCreated = $redis->setnx($key, time());
        } catch(Exception $e) {
            if (self::$_logger) {
                self::$_logger->addError('could not connect to redis');
            }
        }
        if ($isCreated && $time) {
            $redis->expire($key, $time);
        }
        if (self::$_logger) {
            if ($isCreated && self::$_logger) {
                self::$_logger->addNotice('cron not locked');
            } elseif(self::$_logger) {
                self::$_logger->addNotice('cron locked');
            }
        }
        return $isCreated;
    }

}

class LockException extends \Exception {}
