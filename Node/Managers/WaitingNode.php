<?php

namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * waiting node
 *
 *          ┌───────────────┐
 *    in   ─┤               │
 *          │  WaitingNode  ├─ out
 *    emit ─┤               │
 *          └───────────────┘
 *    emit out=in only 'emit' signal=true
 */
class WaitingNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true,
            ],
            'emit' => [
                'required' => true,
            ]
        ],
        'output' => [
            'out' => [
            ]
        ]
    ];

    public function eval()
    {
        if ($this->input['emit']['data']) {
            $this->emit('out', $this->input['in']['data']);
        }
    }
}
