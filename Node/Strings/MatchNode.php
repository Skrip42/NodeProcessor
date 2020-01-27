<?php

namespace Skrip42\NodeProcessor\Node\Strings;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * String match node
 *
 *         ┌───────────────┐
 *         │               ├─ out
 *    in  ─┤   MatchNode   │
 *         │               ├─ iout
 *         └───────────────┘
 *    out = preg_match($pattern, in) ($pattern is node constructor parameter)
 *    iout = !out
 */
class MatchNode extends NodeAbstract
{
    private $pattern = '';
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
        return parent::__construct();
    }

    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true,
            ],
        ],
        'output' => [
            'out' => [
                'data' => true
            ],
            'iout' => [ //inverted output
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
        if (preg_match($this->pattern, $this->input['in']['data']) !== false) {
            $this->emit('out');
        } else {
            $this->emit('iout');
        }
    }
}
