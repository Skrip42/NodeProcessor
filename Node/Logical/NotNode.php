<?php

namespace Skrip42\NodeProcessor\Node\Logical;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Logical not node
 *
 *         ┌───────────────┐
 *         │               │
 *    in  ─┤   CountNode   ├─ out
 *         │               │
 *         └───────────────┘
 *    out = !in
 */
class NotNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true
            ]
        ],
        'output' => [
            'out' => [
            ]
        ]
    ];

    public function eval()
    {
        $this->emit('out', !$this->input['in']['data']);
    }
}
