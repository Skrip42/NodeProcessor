<?php

namespace Skrip42\NodeProcessor\Node\Strings;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * String concatination node
 *
 *          ┌───────────────┐
 *    in1  ─┤               │
 *          │               │
 *    in2  ─┤               │
 *          │   ConcatNode  ├─ out
 *    ...  ─┤               │
 *          │               │
 *    in$n ─┤               │
 *          └───────────────┘
 *    out = in1 . in2 . ... . in$n
 *
 */
class ConcatNode extends NodeAbstract
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

    public function set(string $in, $data)
    {
        if (!is_string($data)) {
            throw new NodeException('Property must be string');
        }
        parent::set($in, $data);
    }

    public function eval()
    {
        $string = '';

        foreach ($this->input as $in => $data) {
            $string .= $data['data'];
        }
        $this->emit('out', $string);
    }
}
