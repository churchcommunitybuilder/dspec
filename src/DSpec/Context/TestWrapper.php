<?php

namespace DSpec\Context;

class TestWrapper
{
    protected $example;
    protected $closure;
    protected $context;

    public function __construct($example = null, \Closure $closure = null, SpecContext $context)
    {
        $this->example = $example;
        $this->closure = $closure;
        $this->context = $context;
    }

    public function only($example = null, \Closure $closure = null)
    {
        if ($example !== null && $closure !== null) {
            $this->example = $example;
            $this->closure = $closure;
        }

        return $this->context->only($this->example, $this->closure);
    }
}
