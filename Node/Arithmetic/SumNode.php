<?php

namespace Skrip42\NodeProcessor\Node\Arithmetic;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Ariphmetic sum node
 *
 *          ┌───────────────┐
 *    in1  ─┤               │
 *          │               │
 *    in2  ─┤               │
 *          │    SumNode    ├─ out
 *    ...  ─┤               │
 *          │               │
 *    in$n ─┤               │
 *          └───────────────┘
 *    out = in1 + in2 + .. + in$n
 *
 */
class SumNode extends NodeAbstract
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
        'user_input' => [
            'pattern' => '~in\d+~',
            'property' => [
                'required' => true
            ]
        ],
        'output' => [
            'out' => [
                'data' => true
            ]
        ]
    ];

    public function eval()
    {
        $sum = 0;
        foreach ($this->input as $in => $data) {
            $sum += $data['data'];
        }
        $this->emit('out', $sum);
    }
}
