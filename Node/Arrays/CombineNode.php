<?php

namespace Skrip42\NodeProcessor\Node\Arrays;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Array combine node
 *
 *          ┌───────────────┐
 *    in1  ─┤               │
 *          │               │
 *    in2  ─┤               │
 *          │  CombineNode  ├─ out
 *    ...  ─┤               │
 *          │               │
 *    in$n ─┤               │
 *          └───────────────┘
 *    out = [in1, in2, ..., in$n]
 */
class CombineNode extends NodeAbstract
{
    protected static $scheme = [
        'user_input' => [
            'property' => [
                'required' => true,
            ]
        ],
        'output' => [
            'out' => [
            ],
        ]
    ];

    public function eval()
    {
        $array = [];
        foreach ($this->input as $key => $value) {
            $array[$key] = $value['data'];
        }
        $this->emit('out', $array);
    }
}
