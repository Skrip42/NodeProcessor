<?php

namespace Skrip42\NodeProcessor\Node\Strings;

use Skrip42\NodeProcessor\Node\NodeAbstract ;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Replace node
 *
 *         ┌───────────────┐
 *         │               │
 *    in  ─┤  ReplaceNode  ├─ out
 *         │               │
 *         └───────────────┘
 *    out = preg_replace($pattern, $replacement, in) ($pattern and $replacement is node constructor parameter)
 */
class ReplaceNode extends NodeAbstract
{
    private $pattern = '';
    private $replacement = '';
    public function __construct(string $pattern, string $replacement)
    {
        $this->pattern = $pattern;
        $this->replacement = $replacement;
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
        $this->emit('out', preg_replace($this->pattern, $this->replacement, $this->input['in']['data']));
    }
}
