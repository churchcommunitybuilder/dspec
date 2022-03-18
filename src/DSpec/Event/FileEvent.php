<?php

namespace DSpec\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * This file is part of dspec
 *
 * Copyright (c) 2012 Dave Marshall <dave.marshall@atstsolutuions.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class FileEvent extends Event
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Get filename
     *
     * @return String
     */
    public function getFilename()
    {
        return $this->file;
    }
}