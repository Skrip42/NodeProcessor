<?php

namespace Skrip42\NodeProcessor\Node\Arithmetic;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Ariphmetic multiply node
 *
 *          ┌───────────────┐
 *    in1  ─┤               │
 *          │               │
 *    in2  ─┤               │
 *          │ MultiplyNode  ├─ out
 *    ...  ─┤               │
 *          │               │
 *    in$n ─┤               │
 *          └───────────────┘
 *    out = in1 * in2 * .. * in$n
 *
 */
class MultiplyNode extends NodeAbstract
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
        $val = 1;
        foreach ($this->input as $in => $data) {
            $val *= $data['data'];
        }
        $this->emit('out', $val);
    }
}
