<?php

namespace Skrip42\NodeProcessor\Node\Logical;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Logical xor node
 *
 *          ┌───────────────┐
 *    in1  ─┤               │
 *          │               ├─ out
 *    in2  ─┤               │
 *          │    XorNode    │
 *    ...  ─┤               │
 *          │               ├─ iout
 *    in$n ─┤               │
 *          └───────────────┘
 *    out = (in1 || in2 || .. || in$n) && !(in1 && in2) && !(in2 && in3) && !(in1 && in3) .....
 *    (only one value must be true)
 *    iout = !out
 */
class XorNode extends NodeAbstract
{
    protected static $scheme = [
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
        $counter = 0;
        dump($this->input);
        foreach ($this->input as $in => $data) {
            if ($data['data']) {
                $counter++;
            }
        }
        if ($counter == 1) {
            $this->emit('out');
        } else {
            $this->emit('iout');
        }
    }
}
