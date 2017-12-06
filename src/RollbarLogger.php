<?php

namespace Mlezitom\NetteRollbar;

use Nette\Security\IIdentity;
use Psr\Log\LogLevel;
use Rollbar\Rollbar;
use Tracy\Debugger;
use Tracy\ILogger;

class Logger implements ILogger
{
    public $identity;

    /**
     * @param Rollbar $client
     */
    public function __construct($rollbarKey, $env, IIdentity $identity = null, $autoBindToTracy = true)
    {
        if (!$rollbarKey || !$env) {
            // disabled
            return;
        }
        $this->identity = $identity;
        Rollbar::init([
            'access_token' => $rollbarKey,
            'environment' => $env,
        ]);
        if ($autoBindToTracy) {
            $this->bindToTracy();
        }
    }


    /**
     * @param mixed $value
     * @param string $priority
     */
    public function log($value, $priority = null)
    {
        if (!$priority && is_object($value) && $value instanceof \Exception) {
            $priority = self::EXCEPTION;
        }
        else if (!$priority) {
            $priority = self::ERROR;
        }
        // priority constraint by PSR-3
        $priority = $this->sanizizeLogLevel($priority);


        Rollbar::log($priority, $value, [
            'identity' => $this->identity,
        ]);
    }


    /**
     * @param mixed $value
     */
    public function logInfo($value)
    {
        return $this->log($value, self::INFO);
    }


    public function bindToTracy()
    {
        Debugger::setLogger($this);
    }


    /**
     * @param $priority
     * @return string
     */
    private function sanizizeLogLevel($priority)
    {
        if (in_array($priority, [
            LogLevel::INFO,
            LogLevel::ERROR,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::DEBUG,
            LogLevel::EMERGENCY,
            LogLevel::NOTICE,
            LogLevel::WARNING,
        ])) {
            return $priority;
        }
        return LogLevel::WARNING;
    }
}