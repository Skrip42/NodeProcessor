<?php

namespace Skrip42\NodeProcessor\Node\Other;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Rand node
 *
 *          ┌───────────────┐
 *          │               │
 *    emit ─┤    RandNode   ├─ out
 *          │               │
 *          └───────────────┘
 *    out = rand($min, $max)  ($min and $max is node constructor parameter)
 *    emit out when 'emit' signal
 */
class RandNode extends NodeAbstract
{
    private $min;
    private $max;

    public function __construct($min = 0, $max = 1)
    {
        $this->min = $min;
        $this->max = $max;
        return parent::__construct();
    }

    protected static $scheme = [
        'input' => [
            'emit' => [
            ]
        ],
        'output' => [
            'out' => [
            ]
        ]
    ];

    public function eval()
    {
        $this->emit('out', rand($this->min, $this->max));
    }
}
