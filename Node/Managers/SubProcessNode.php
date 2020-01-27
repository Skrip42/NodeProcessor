<?php

namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;
use Skrip42\NodeProcessor\Process;

/**
 * Subprocess node
 *
 *          ┌─────────────────────┐
 *    in1  ─┤                     ├─ out1
 *          │                     │
 *    in2  ─┤                     ├─ out2
 *          │    SubProcessNode   │
 *    ...  ─┤                     ├─ ...
 *          │                     │
 *    in$n ─┤                     ├─ ount$n
 *          └─────────────────────┘
 *
 *    Requires subprocess scheme to controller (see Process::build function for detail)
 *    run subprocess in node inside
 *    map all input value to scheme pattern value
 *    output subprocess value map to node output
 *    forward all subprocess request to node request
 *    forward all node response to subprocess response
 */
class SubProcessNode extends NodeAbstract
{
    private $includeScheme = [];

    private $childProcess;

    public function __construct(array $scheme)
    {
        $this->includeScheme = $scheme;
        $this->mapScheme();
        return parent::__construct();
    }

    protected function mapScheme()
    {
        if (!isset($this->includeScheme['node'])
            || !isset($this->includeScheme['link'])
        ) {
            throw new NodeException('Uncorect process scheme');
        }
        foreach ($this->includeScheme['node'] as $node) {
            foreach ($node as &$value) {
                $matches = [];
                if (is_string($value)
                    && preg_match('~\{\$(\w[\w\d]*)\}~', $value, $matches)
                ) {
                    $this->input[$matches[1]] = [
                        'required' => true,
                        'setCount' => 0,
                        'data' => null
                    ];
                }
            }
        }
        foreach ($this->includeScheme['link'] as $links) {
            if ($links[3] == 'end') {
                $this->output[$links[2]] = [
                    'data' => null,
                    'emitCount' => 0
                ];
            }
        }
    }

    public function reset()
    {
        parent::reset();
        $this->mapScheme();
        $this->childProcess = null;
    }

    protected static $scheme = [
        'input' => [
        ],
        'output' => [
        ]
    ];

    public function getState() : array
    {
        $result = parent::getState();
        $result['subprocess'] = $this->childProcess->getState();
        return $result;
    }


    public function eval()
    {
        $params = [];
        foreach ($this->input as $key => $data) {
            $params[$key] = $data['data'];
        }
        $this->childProcess = Process::build($this->includeScheme, $params);
        $this->childProcess->run();
        $this->processResult();
    }

    public function processResult()
    {
        $output = $this->childProcess->getResult();
        if ($output['status'] == Process::STATUS_COMPLETE) {
            foreach ($output['output'] as $key => $value) {
                if (!is_null($value)) {
                    $this->emit($key, $value);
                }
            }
        } else if ($output['status'] == Process::STATUS_WAIT_RESPONSE) {
            foreach ($output['requests'] as $id => $data) {
                $this->sendRequest($data, $id);
            }
        }
    }

    public function setResponse($id, $data)
    {
        $this->childProcess->setResponse($id, $data);
        $this->processResult();
    }
}
