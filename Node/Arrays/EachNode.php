<?php

namespace Skrip42\NodeProcessor\Node\Arrays;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Array iterator node
 *
 *           ┌───────────────┐
 *           │               ├─ out
 *           │               │
 *    array ─┤    EachNode   ├─ key
 *           │               │
 *           │               ├─ complete
 *           └───────────────┘
 *    emit 'out' and 'key' for each 'array' element
 *    emit 'complete' when 'array' is end
 */
class EachNode extends NodeAbstract
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
            'key' => [
            ],
            'complete' => [
                'data' => true
            ]
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
        foreach ($this->input['array']['data'] as $key => $value) {
            $this->emit('key', $key);
            $this->emit('out', $value);
        }
        $this->emit('complete');
    }
}
