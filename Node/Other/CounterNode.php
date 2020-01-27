<?php

namespace Skrip42\NodeProcessor\Node\Other;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Counter node
 *
 *         ┌───────────────┐
 *         │               │
 *    in  ─┤  CounterNode  ├─ out
 *         │               │
 *         └───────────────┘
 *    out = count of in signals
 */
class CounterNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true
            ]
        ],
        'output' => [
            'out' => [
                'data' => 0
            ]
        ]
    ];

    public function eval()
    {
        $this->output['out']['data']++;
        $this->emit('out');
    }
}
