<?php

namespace Skrip42\NodeProcessor;

use Skrip42\NodeProcessor\Node\Managers\BeginNode;
use Skrip42\NodeProcessor\Node\Managers\EndNode;
use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\BuildException;
use Exception;

/**
 * Process class
 */
class Process
{
    const STATUS_REDY = 0;
    const STATUS_RUNN = 1;
    const STATUS_ERROR = 2;
    const STATUS_WAIT_RESPONSE = 3;
    const STATUS_COMPLETE = 4;

    /* current process status */
    private $status = self::STATUS_REDY;
    private $nodes = [];
    private $requests = [];

    private $beginNode = null;
    private $endNode = null;

    /**
     * Init begin and and node
     *
     * @param mixed $value
     *
     * @return null
     */
    public function __construct($value = null)
    {
        $this->beginNode = $this->createNode(BeginNode::class, $value);
        $this->endNode = $this->createNode(EndNode::class);
    }

    /**
     * Return current process state
     *
     * @return array
     */
    public function getState() : array
    {
        $result = [
            'status' => $this->status,
            'nodes' => [],
            'requests' => []
        ];
        foreach ($this->nodes as $node) {
            $result['nodes'][$node->getId() . '_' . get_class($node) . '(' . $node->getRunCounter() . ')'] = $node->getState();
        }
        foreach ($this->requests as $id => $request) {
            $result['requests'][$id] = [
                'from node' => $request[0]->getId() . '_' . get_class($request[0]),
                'data' => $request[1]
            ];
        }
        return $result;
    }

    /**
     * Get actual request list
     *
     * @return array
     */
    public function getRequests() : array
    {
        $requests = [];
        foreach ($this->requests as $id => $request) {
            $requests[$id] = $request[1];
        }
        return $requests;
    }

    /**
     * Return begin node
     *
     * @return BeginNode
     */
    public function getBeginNode() : BeginNode
    {
        return $this->beginNode;
    }

    /**
     * Return end node
     *
     * @return EndNode
     */
    public function getEndNode() : EndNode
    {
        return $this->endNode;
    }

    /**
     * Add new request
     *
     * @param string       $id
     * @param NodeAbstract $node // target node
     * @param mixed        $data
     *
     * @return null
     */
    public function addRequest(string $id, NodeAbstract $node, $data)
    {
        $this->requests[$id] = [$node, $data];
    }

    /**
     * Set response to request owner
     *
     * @param string $id
     * @param mixed  $data
     *
     * @return null
     */
    public function setResponse(string $id, $data)
    {
        if (empty($this->requests[$id])) {
            return $this->getResult();
        }
        $this->requests[$id][0]->setResponse($id, $data);
        unset($this->requests[$id]);
        if (empty($this->requests)) {
            $this->status = self::STATUS_COMPLETE;
        } else {
            $this->status = self::STATUS_WAIT_RESPONSE;
        }
        return $this->getResult();
    }

    /**
     * Return current status
     *
     * @return int
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * Return last iteration result
     *
     * @return null
     */
    public function getResult()
    {
        $result = [
            'status' => $this->status,
            'output' => [],
            'requests' => []
        ];
        $result['output'] = $this->endNode->getResult();
        $result['requests'] = $this->getRequests();
        return $result;
    }

    /**
     * Run process from begin
     *
     * @return null
     */
    public function run()
    {
        try {
            $this->beginNode->eval();
            if (empty($this->requests)) {
                $this->status = self::STATUS_COMPLETE;
            } else {
                $this->status = self::STATUS_WAIT_RESPONSE;
            }
            return $this->getResult();
        } catch (Exception $e) {
            $this->status = self::STATUS_ERROR;
            throw $e;
        }
    }

    /**
     * Reset process state
     *
     * @return null
     */
    public function reset()
    {
        $this->status = self::STATUS_REDY;
        foreach ($this->nodes as $node) {
            $node->reset();
        }
    }

    /**
     * Create process node
     *
     * @param string $nodeClassName
     * @param mixed  ...$params // this params map to node constructor
     *
     * @return NodeAbstract //return new node
     */
    public function createNode(string $nodeClassName, ...$params) : NodeAbstract
    {
        $node = new $nodeClassName(...$params);
        $node->setProcess($this);
        $this->nodes[$node->getId()] = $node;

        return $node;
    }

    /**
     * Link output from outputNode to input from inputNode
     *
     * @param string       $out
     * @param NodeAbstract $outputNode
     * @param string       $in
     * @param NodeAbstract $inputNode
     *
     * @return null
     */
    public function linkNode(string $out, NodeAbstract $outputNode, string $in, NodeAbstract $inputNode)
    {
        $outputNode->bindOutput($out, $in, $inputNode);
        $inputNode->bindInput($in, $outputNode);
    }

    /**
     * Emit data to target node
     *
     * @param string $nodeId
     * @param string $in
     * @param bool   $data
     *
     * @return null
     */
    public function emit(string $nodeId, string $in, $data = true)
    {
        $this->nodes[$nodeId]->set($in, $data);
    }

    /**
     * Build new process from scheme
     *
     * @param array $scheme //process scheme see example
     * @param array $params //this params map to scheme
     *
     * @example:
     *      $processScheme = [
     *          'node' => [
     *              'vn' => [ValueNode::class, '{$val}'],
     *          ]
     *          'link' => [
     *              ['out1', 'begin', 'emit', 'vn'],
     *              ['out', 'vn', 'result', 'end']
     *          ]
     *      ];
     *      Process::build(
     *          $processScheme,
     *          ['val' => 'some value']
     *      );
     *
     * @return self
     */
    public static function build(array $scheme, array $params = []) : self
    {
        $tree = new self();
        $nodes = [];
        $nodes['begin'] = $tree->getBeginNode();
        $nodes['end'] = $tree->getEndNode();
        foreach ($scheme['node'] as $key => $node) {
            foreach ($node as &$value) {
                $matches = [];
                if (is_string($value) && preg_match('~\{\$(\w[\w\d]*)\}~', $value, $matches)) {
                    if (!isset($params[$matches[1]])) {
                        throw new BuildException('unknow parameter name: ' . $matches[1]);
                    }
                    $value = $params[$matches[1]];
                }
            }
            $nodes[$key] = $tree->createNode(...$node);
        }
        foreach ($scheme['link'] as $link) {
            list($out, $outnode, $in, $innode) = $link;
            if (empty($nodes[$outnode])) {
                throw new BuildException('unknow node name: ' . $outnode);
            }
            if (empty($nodes[$innode])) {
                throw new BuildException('unknow node name: ' . $innode);
            }
            $tree->linkNode($out, $nodes[$outnode], $in, $nodes[$innode]);
        }
        return $tree;
    }
}
