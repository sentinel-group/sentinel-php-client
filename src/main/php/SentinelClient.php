<?php

namespace Sentinel;

use Sentinel\Rpc\SentinelRpcClient;
use Thrift\Exception\TException;
use Thrift\Exception\TTransportException;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocket;

/**
 * Sentinel 客户端。通过 RPC 调用 sidecar 实现。
 * TODO 多线程时底层 RPC 通信必须加锁串行访问
 *
 * @package Sentinel
 */
class SentinelClient
{
    /**
     * 底层 RPC 通信 socket 。
     *
     * @var TSocket
     */
    protected $socket_ = null;

    /**
     * @var SentinelRpcClient
     */
    protected $client_ = null;

    /**
     * SentinelClient constructor.
     * TODO 是否可使用 persistent socket 优化性能 ?
     *
     * @param string $host Sentinel sidecar socket hostname
     * @param int $port Sentinel sidecar socket port
     * @param bool $persist Whether to use a persistent socket
     */
    public function __construct(
        $host = 'localhost',
        $port = 9090,
        $persist = false
    )
    {
        $this->socket_ = new TSocket($host, $port, $persist);

        $trans = new TFramedTransport($this->socket_);
        $protocol = new TBinaryProtocolAccelerated($trans, true, true);
        $input = $protocol;
        $output = $protocol;
        $this->client_ = new SentinelRpcClient($input, $output);
    }

    /**
     * 确认打开底层 socket 连接。
     *
     * @throws TTransportException
     * @throws TException
     */
    protected function ensureOpen() {
        if (!$this->socket_->isOpen()) {
            $this->socket_->open();
        }
    }

    /**
     * 获取受保护的资源访问入口。
     * 返回对象不再被引用后, 将自动释放访问入口。
     *
     * 注意: 必须定义一个变量持有访问入口, 否则返回对象被自动销毁, 将自动释放访问入口。
     *
     * @param string $name 资源名称
     * @return SentinelEntry
     * @throws BlockException
     * @throws TException
     */
    public function entry($name) {
        $this->ensureOpen();
        $id = $this->client_->entry($name);
        return new SentinelEntry($this->client_, $id);
    }
}
