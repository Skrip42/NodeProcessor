<?php

namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Begin node
 *
 *    ┌───────────────┐
 *    │               ├─ out1
 *    │               │
 *    │               │
 *    │               ├─ out2
 *    │   BeginNode   │
 *    │               │
 *    │               ├─ ...
 *    │               │
 *    │               │
 *    │               ├─ out$n
 *    └───────────────┘
 *
 *    just begin node
 *    emit start value from all output
 *    start value map to node constructor
 */
class BeginNode extends NodeAbstract
{
    private $value = true;

    public function __construct($value = null)
    {
        if (isset($value)) {
            $this->value = $value;
        }
        return parent::__construct();
    }

    protected static $scheme = [
        'output' => [
            'out1' => []
        ],
        'user_output' => [
            'pattern' => '~out\d+~'
        ]
    ];

    public function eval()
    {
        foreach ($this->output as $out => $data) {
            $this->emit($out, $this->value);
        }
    }
}
