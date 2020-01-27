<?php

namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;
use Skrip42\NodeProcessor\Process;

/**
 * Subprocess fork node
 *
 *          ┌─────────────────────┐
 *    in1  ─┤                     ├─ out1
 *          │                     │
 *    in2  ─┤                     ├─ out2
 *          │ MultiSubProcessNode │
 *    ...  ─┤                     ├─ ...
 *          │                     │
 *    in$n ─┤                     ├─ ount$n
 *          └─────────────────────┘
 *    Requires subprocess scheme to controller (see Process::build function for detail)
 *    run subprocess instance fro each array node input value
 *    map all input value to scheme pattern value
 *    collect all output subprocess value to arrays and map it to node output
 *    forward all subprocess request to node request
 *    forward all node response to subprocess response
 */
class MultiSubProcessNode extends NodeAbstract
{
    private $includeScheme = [];

    private $childProcesses = [];
    private $requestMap = [];

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
                    'data' => [],
                    'emitCount' => 0
                ];
            }
        }
    }

    public function reset()
    {
        parent::reset();
        $this->mapScheme();
        $this->childProcesses = [];
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
        $result['subprocess'] = [];
        foreach ($this->childProcesses as $process) {
            $result['subprocess'][] = $process->getState();
        }
        return $result;
    }
    
    protected function prepareInput()
    {
        $params = [];
        $maxArrayCount = null;
        foreach ($this->input as $key => $input) {
            if (!is_array($input['data'])) {
                continue;
            }
            if (is_null($maxArrayCount)) {
                $maxArrayCount = count($input['data']);
                continue;
            }
            if (count($input['data']) > $maxArrayCount) {
                $maxArrayCount = count($input['data']);
            }
        }
        if (is_null($maxArrayCount)) {
            throw new NodeException('at least one argument must be array');
        }
        for ($i = 0; $i < $maxArrayCount; $i++) {
            $params[] = [];
        }
        foreach ($this->input as $key => $input) {
            if (!is_array($input['data'])) {
                for ($i = 0; $i < $maxArrayCount; $i++) {
                    $params[$i][$key] = $input['data'];
                }
            }
            $dataArray = array_values($input['data']);
            for ($i = 0; $i < $maxArrayCount; $i++) {
                if (!is_null($dataArray[$i])) {
                    $params[$i][$key] = $dataArray[$i];
                }
            }
        }
        return $params;
    }

    public function eval()
    {
        $params = $this->prepareInput();
        foreach ($params as $key => $param) {
            $this->childProcesses[$key] = Process::build($this->includeScheme, $param);
            $this->childProcesses[$key]->run();
        }
        $this->processRequest();
        if (empty($this->requestMap)) {
            $this->emitResults();
        }
    }

    public function processRequest()
    {
        foreach ($this->childProcesses as $key => $process) {
            $output = $process->getResult();
            if ($output['status'] == Process::STATUS_WAIT_RESPONSE) {
                foreach ($output['requests'] as $rid => $data) {
                    $this->requestMap[$rid] = $key;
                    $this->sendRequest($data, $rid);
                }
            }
        }
    }

    public function setResponse($id, $data)
    {
        $output = $this->childProcesses[$this->requestMap[$id]]->setResponse($id, $data);
        if ($output['status'] == Process::STATUS_WAIT_RESPONSE) {
            foreach ($output['requests'] as $rid => $data) {
                $this->requestMap[$rid] = $key;
                $this->sendRequest($data, $rid);
            }
        }
        unset($this->requestMap[$id]);
        if (empty($this->requestMap)) {
            $this->emitResults();
        }
    }

    public function emitResults()
    {
        foreach ($this->childProcesses as $key => $process) {
            $output = $process->getResult();
            foreach ($output['output'] as $out => $data) {
                $this->output[$out]['data'][$key] = $data;
            }
        }
        foreach ($this->output as $out => $data) {
            $this->emit($out, $data['data']);
        }
    }
}
