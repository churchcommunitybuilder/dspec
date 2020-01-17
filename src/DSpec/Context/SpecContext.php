<?php

namespace DSpec\Context;

use DSpec\Event\FileEvent;
use DSpec\Events;
use SplStack;
use DSpec\ExampleGroup;
use DSpec\Example;
use DSpec\Hook;
use DSpec\Expectation\Subject;
use DSpec\Exception\SkippedExampleException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This file is part of dspec
 *
 * Copyright (c) 2012 Dave Marshall <dave.marshall@atstsolutuions.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SpecContext extends AbstractContext
{
    /**
     * Prefixed to avoid clashes with execution scope
     */
    protected $__stack;

    protected $hasOnly = false;

    /**
     */
    public function __construct()
    {
        $this->__stack = new SplStack();
    }

    /**
     * Describe something
     *
     * @param string $description - The thing we're describing
     * @param Closure $closure - How we're describing it
     */
    public function describe($description = null, \Closure $closure = null)
    {
        if ($description === null && $closure === null) {
            return new OnlyWrapper($description, $closure, $this, false);
        }

        $context = clone $this;

        $parent = $this->__stack->top();
        if ($parent) {
            $context->setParentContext($parent->getContext());
        }

        $group = new ExampleGroup($description, $context, $parent);
        $this->__stack->top()->add($group);
        $this->__stack->push($group);
        $context->run($closure);
        $this->__stack->pop();
        return $group;
    }

    public function xdescribe($description, \Closure $closure)
    {
        $that = $this;
        $skippedClosure = function() use ($that, $closure) {
            beforeEach(function() use ($that) {
                $that->skip();
            });
            $closure();
        };
        return $this->describe($description, $skippedClosure);
    }

    /**
     * Proxy to Describe
     *
     * @param string $description
     * @param Closure $closure
     */
    public function context($description = null, \Closure $closure = null)
    {
        return $this->describe($description, $closure);
    }

    public function xcontext($description, \Closure $closure)
    {
        return $this->xdescribe($description, $closure);
    }

    /**
     * Let
     *
     * @param string $name
     * @param \Closure $closure
     */
    public function let($name, \Closure $closure)
    {
        $this->__stack->top()->getContext()->setFactory($name, $closure);
    }

    /**
     * It
     *
     * @param string $example
     * @param Closure $closure
     */
    public function it($example = null, \Closure $closure = null)
    {
        if ($example === null && $closure === null) {
            return new OnlyWrapper($example, $closure, $this, true);
        }

        if ($closure === null) {
            $that = $this;
            $closure = function() use ($that) { $that->pending(); };
        }
        $example = new Example($example, $closure);
        $example->setParent($this->__stack->top());
        $this->__stack->top()->add($example);
        return $example;
    }

    public function xit($example = null, \Closure $closure = null)
    {
        return $this->it($example, function() {
            throw new SkippedExampleException();
        });
    }

    public function test($example = null, \Closure $closure = null)
    {
        return $this->it($example, $closure);
    }

    public function xtest($example = null, \Closure $closure = null)
    {
        return $this-xit($example, $closure);
    }

    public function only($example, \Closure $closure = null, $test)
    {
        if ($test) {
            if ($closure === null) {
                $that = $this;
                $closure = function() use ($that) { $that->pending(); };
            }

            $example = new Example($example, $closure);
            $example->setParent($this->__stack->top());
            $example->markOnly();
            $this->markHasOnly();
            $this->__stack->top()->add($example);
            return $example;
        } else {
            $context = clone $this;

            $parent = $this->__stack->top();
            if ($parent) {
                $context->setParentContext($parent->getContext());
            }

            $group = new ExampleGroup($example, $context, $parent);
            $this->__stack->top()->add($group);
            $this->__stack->push($group);
            $this->markHasOnly();
            $group->markHasOnly();
            $context->run($closure);
            $this->__stack->pop();
            return $group;
        }
    }

    public function hasOnly()
    {
        return $this->hasOnly;
    }

    protected function markHasOnly()
    {
        $this->hasOnly = true;
        if ($this->__parentContext) {
            $this->__parentContext->markHasOnly();
        }
    }

    /**
     * Before Context
     *
     * @param $closure
     */
    public function beforeContext($closure)
    {
        $this->__stack->top()->add(new Hook('beforeContext', $closure));
    }

    /**
     * After Context
     *
     * @param $closure
     */
    public function afterContext($closure)
    {
        $this->__stack->top()->add(new Hook('afterContext', $closure));
    }

    /**
     * Before Each
     *
     * @param \Closure $closure
     */
    public function beforeEach($closure)
    {
        $this->__stack->top()->add(new Hook('beforeEach', $closure));
    }

    /**
     * After Each
     *
     * @param \Closure $closure
     */
    public function afterEach($closure)
    {
        $this->__stack->top()->add(new Hook('afterEach', $closure));
    }

    /**
     * @param array $files
     * @return ExampleGroup
     */
    public function load(array $files, ExampleGroup $eg = null, EventDispatcherInterface $dispatcher = null)
    {
        $eg = $eg ?: new ExampleGroup("Suite", $this);

        $this->__stack->push($eg);

        foreach($files as $f)
        {
            if ($dispatcher) $dispatcher->dispatch(new FileEvent($f), Events::COMPILER_FILE);

            include $f;
        }

        return $this->__stack->bottom();
    }

}
