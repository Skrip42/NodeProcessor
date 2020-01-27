<?php

namespace Skrip42\NodeProcessor\Node\Arrays;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Array extractor node
 *
 *           ┌───────────────┐
 *           │               │
 *    array ─┤  ExtractNode  ├─ out
 *           │               │
 *           └───────────────┘
 *    out = array[$key] ($key is node constructor parameter)
 */
class ExtractNode extends NodeAbstract
{
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
        return parent::__construct();
    }

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
        $this->emit('out', $this->input['array']['data'][$this->key]);
    }
}
