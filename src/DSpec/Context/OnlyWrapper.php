<?php

namespace DSpec\Context;

class OnlyWrapper
{
    protected $example;
    protected $closure;
    protected $context;
    protected $test;

    public function __construct(
        $example = null,
        \Closure $closure = null,
        SpecContext $context,
        $test = true
    ) {
        $this->example = $example;
        $this->closure = $closure;
        $this->context = $context;
        $this->test = $test;
    }

    public function only($example = null, \Closure $closure = null)
    {
        if ($example !== null && $closure !== null) {
            $this->example = $example;
            $this->closure = $closure;
        }

        return $this->context->only($this->example, $this->closure, $this->test);
    }
}
