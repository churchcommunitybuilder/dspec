<?php

namespace DSpec\Event;

use Symfony\Component\EventDispatcher\Event;
use DSpec\ExampleGroup;

/**
 * This file is part of dspec
 *
 * Copyright (c) 2012 Dave Marshall <dave.marshall@atstsolutuions.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ExampleGroupEvent extends Event
{
    protected $exampleGroup;

    public function __construct(ExampleGroup $exampleGroup)
    {
        $this->exampleGroup = $exampleGroup;
    }

    /**
     * Get example
     *
     * @return ExampleGroup
     */
    public function getExampleGroup()
    {
        return $this->exampleGroup;
    }
}
