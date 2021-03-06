<?php

namespace DSpec;

use DSpec\Context\AbstractContext;

/**
 * This file is part of dspec
 *
 * Copyright (c) 2012 Dave Marshall <dave.marshall@atstsolutuions.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Example extends Node
{
    const RESULT_PASSED = 'passed';
    const RESULT_FAILED = 'failed';
    const RESULT_PENDING = 'pending';
    const RESULT_SKIPPED = 'skipped';

    protected $closure;
    protected $result;
    protected $failureException;
    protected $pendingMessage;
    protected $skippedMessage;
    protected $startTime;
    protected $endTime;
    protected $only = false;

    public function __construct($example, \Closure $closure)
    {
        $this->title = $example;
        $this->closure = $closure;
    }

    public function hasOnly()
    {
        return $this->only;
    }

    public function markOnly()
    {
        $this->only = true;
    }

    /**
     * {@inheritDoc}
     */
    public function run(AbstractContext $context)
    {
        $context->run($this->closure);
    }

    /**
     * @return \Throwable
     */
    public function getFailureException()
    {
        return $this->failureException;
    }

    /**
     * Set failure
     *
     * @return Example
     */
    public function failed(\Throwable $e = null)
    {
        $this->result = static::RESULT_FAILED;
        if ($e !== null) {
            $this->failureException = $e;
        }
        return $this;
    }

    public function isFailure()
    {
        return $this->result == static::RESULT_FAILED;
    }

    /**
     * Set passed
     *
     * @return Example
     */
    public function passed()
    {
        $this->result = static::RESULT_PASSED;
        return $this;
    }

    /**
     * Set pending
     *
     * @return Example
     */
    public function pending($msg)
    {
        $this->result = static::RESULT_PENDING;
        $this->pendingMessage = $msg;
    }

    /**
     * Set skipped
     *
     * @return Example
     */
    public function skipped($msg)
    {
        $this->result = static::RESULT_SKIPPED;
        $this->skippedMessage = $msg;
    }

    /**
     * Get Closure
     *
     * @return \Closure
     */
    public function getClosure()
    {
        return $this->closure;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function startTimer()
    {
        $this->startTime = microtime();
    }

    public function endTimer()
    {
        $this->endTime = microtime();
    }

    public function getTime()
    {
        $start = array_sum(explode(" ", $this->startTime));
        $end   = array_sum(explode(" ", $this->endTime));

        return $end - $start;
    }
}
