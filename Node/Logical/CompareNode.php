<?php

namespace Skrip42\NodeProcessor\Node\Logical;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Logical compare node
 *
 *          ┌───────────────┐
 *          │               ├─ more
 *    in1  ─┤               │
 *          │               │
 *          │               ├─ less
 *          │               │
 *    in2  ─┤               │
 *          │               ├─ equal
 *          └───────────────┘
 *    more  = in1 > in2
 *    less  = in1 < in2
 *    equal = in1 == in2
 */
class CompareNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'in1' => [
                'required' => true,
            ],
            'in2' => [
                'required' => true,
            ]
        ],
        'output' => [
            'more' => [
                'data' => true
            ],
            'less' => [
                'data' => true
            ],
            'equal' => [
                'data' => true
            ]
        ]
    ];

    public function eval()
    {
        if ($this->input['in1']['data'] > $this->input['in2']['data']) {
            $this->emit('more');
        } else if ($this->input['in1']['data'] < $this->input['in2']['data']) {
            $this->emit('less');
        } else {
            $this->emit('equal');
        }
    }
}
