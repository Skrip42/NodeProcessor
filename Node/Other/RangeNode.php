<?php

namespace Skrip42\NodeProcessor\Node\Other;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Range generator node
 *
 *          ┌───────────────┐
 *          │               │
 *    emit ─┤    RageNode   ├─ out
 *          │               │
 *          └───────────────┘
 *    out = range($start, $end, $step)  ($start, $end and $step is node constructor parameter)
 *    emit out when 'emit' signal
 */
class RangeNode extends NodeAbstract
{
    private $value;

    public function __construct($start, $end, $step = 1)
    {
        $this->value = range($start, $end, $step);
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
        $this->emit('out', $this->value);
    }
}
