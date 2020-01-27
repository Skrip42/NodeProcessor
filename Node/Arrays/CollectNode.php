<?php

namespace Skrip42\NodeProcessor\Node\Arrays;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Array collector mode
 *
 *          ┌───────────────┐
 *    in   ─┤               │
 *          │  CollectNode  ├─ out
 *    emit ─┤               │
 *          └───────────────┘
 *    collect array from iteration of input
 *    out = [in(1 iteration), in(2 iteration), ...]
 *    emit output when signal in 'emit'
 */
class CollectNode extends NodeAbstract
{
    private $array = [];

    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true,
            ],
            'emit' => [
                'required' => true
            ]
        ],
        'output' => [
            'out' => [
            ],
        ]
    ];

    public function set(string $in, $data)
    {
        if ($in == 'in') {
            $this->array[] = $data;
        }
        parent::set($in, $data);
    }

    public function eval()
    {
        $this->emit('out', $this->array);
    }
}
