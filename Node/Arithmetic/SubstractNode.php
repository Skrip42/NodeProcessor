<?php

namespace Skrip42\NodeProcessor\Node\Arithmetic;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Ariphmetic substract node
 *
 *          ┌───────────────┐
 *    in1  ─┤               │
 *          │               │
 *    in2  ─┤               │
 *          │ SubstractNode ├─ out
 *    ...  ─┤               │
 *          │               │
 *    in$n ─┤               │
 *          └───────────────┘
 *    out = in1 - in2 - .. - in$n
 *
 */
class SubstractNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'in1' => [
                'required' => true,
                //'data' => null
            ],
            'in2' => [
                'required' => true,
                //'data' => null
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
        $sum = $this->input['in1']['data'];
        foreach ($this->input as $in => $data) {
            if ($in == 'in1') {
                continue;
            }
            $sum -= $data['data'];
        }
        $this->emit('out', $sum);
    }
}
