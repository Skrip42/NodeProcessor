<?php

namespace Skrip42\NodeProcessor\Node\Other;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Value node
 *
 *          ┌───────────────┐
 *          │               │
 *    emit ─┤   ValueNode   ├─ out
 *          │               │
 *          └───────────────┘
 *    out = $value  ($min is node constructor parameter)
 *    emit out when 'emit' signal
 */
class ValueNode extends NodeAbstract
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
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
