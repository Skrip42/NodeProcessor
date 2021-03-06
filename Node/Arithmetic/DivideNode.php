<?php

namespace Skrip42\NodeProcessor\Node\Arithmetic;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Ariphmetic deivide node
 *
 *          ┌───────────────┐
 *    in1  ─┤               │
 *          │               │
 *    in2  ─┤               │
 *          │   DivideNode  ├─ out
 *    ...  ─┤               │
 *          │               │
 *    in$n ─┤               │
 *          └───────────────┘
 *    out = in1 / in2 / .. / in$n
 *
 */
class DivideNode extends NodeAbstract
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
        $val = $this->input['in1']['data'];
        foreach ($this->input as $in => $data) {
            if ($in == 'in1') {
                continue;
            }
            $val /= $data['data'];
        }
        $this->emit('out', $val);
    }
}
