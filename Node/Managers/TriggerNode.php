<?php

namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Trigger node
 *
 *          ┌───────────────┐
 *          │               ├─ before
 *          │               │
 *    in   ─┤  TriggerNode  ├─ out
 *          │               │
 *          │               ├─ after
 *          └───────────────┘
 *
 *    emit before=true before out emit
 *    out = in
 *    emit after=true after out emit
 */
class TriggerNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true
            ]
        ],
        'output' => [
            'before' => [
                'data' => true
            ],
            'out' => [
            ],
            'after' => [
                'data' => true
            ]
        ]
    ];

    public function eval()
    {
        $this->emit('before');
        $this->emit('out', $this->input['in']['data']);
        $this->emit('after');
    }
}
