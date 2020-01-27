<?php

namespace Skrip42\NodeProcessor\Node\Logical;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Logical and node
 *
 *          ┌───────────────┐
 *    in1  ─┤               │
 *          │               ├─ out
 *    in2  ─┤               │
 *          │    AndNode    │
 *    ...  ─┤               │
 *          │               ├─ iout
 *    in$n ─┤               │
 *          └───────────────┘
 *    out = in1 && in2 && .. && in$n
 *    iout = !out
 */
class AndNode extends NodeAbstract
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
            ],
            'iout' => [
                'data' => true
            ]
        ]
    ];

    public function eval()
    {
        foreach ($this->input as $in => $data) {
            if ($data['data'] != $this->input['in1']['data']) {
                $this->emit('iout');
                return;
            }
        }
        $this->emit('out');
    }
}
