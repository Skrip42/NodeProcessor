<?php

namespace Skrip42\NodeProcessor\Node\Arrays;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Array count node
 *
 *           ┌───────────────┐
 *           │               │
 *    array ─┤   CountNode   ├─ out
 *           │               │
 *           └───────────────┘
 *    out = count(array)
 */
class CountNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'array' => [
                'required' => true,
            ]
        ],
        'output' => [
            'out' => [
            ],
        ]
    ];

    public function set(string $in, $data)
    {
        if ($in == 'array' && !is_array($data)) {
            throw new NodeException('Property must be array');
        }
        parent::set($in, $data);
    }

    public function eval()
    {
        $this->emit('out', count($this->input['array']['data']));
    }
}
