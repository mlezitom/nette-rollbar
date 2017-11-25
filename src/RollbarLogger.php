<?php

namespace Mlezitom\NetteRollbar;

use Nette\Security\IIdentity;
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

        Rollbar::log($priority, $value, [
            'identity' => $this->identity,
        ]);
    }

    public function logInfo($value)
    {
        return $this->log($value, self::INFO);
    }


    public function bindToTracy()
    {
        Debugger::setLogger($this);
    }
}