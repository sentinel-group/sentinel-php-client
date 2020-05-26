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
     * 当前进程 PID 。
     * @var int
     */
    protected $pid_ = null;

    /**
     * @var string
     */
    protected $addr_ = null;

    /**
     * @var bool
     */
    protected $persist_ = null;

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
        $port = -1,
        $persist = false
    )
    {
        // CentOS 上 posix_getpid 可能被移动到 php-process 软件包, 基础环境可能缺少此方法.
        if (function_exists('\posix_getpid')) {
            $this->pid_ = \posix_getpid();
        } else if (function_exists('\getmypid')) {
            $this->pid_ = \getmypid();
        }

        if ($port == -1) {
            $this->addr_ = $host;
        } else {
            $this->addr_ = $host . ":" . "$port";
        }

        $this->persist_ = $persist;

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
            # 打印连接成功日志? 应使用调试日志并默认关闭, 避免频繁打印刷屏。
            #error_log("PID=$this->pid_, SentinelClient(persist=$this->persist_) connected to sidecar $this->addr_ .");
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
     *      流控通过时返回资源访问入口对象。
     *      客户端异常 (如连接服务器失败等情况) 时返回 null, 此时流控防护失效。
     * @throws BlockException 当前访问被限流时抛出 \Sentinel\BlockException 异常。
     */
    public function entry($name) {
        try {
            return $this->doEntry($name);
        } catch (BlockException $e) {
            throw $e;
        } catch (\Exception $e) {
            // 捕获除 BlockException 之外的所有异常, 打印错误日志并返回 null 。
            // TODO 打印错误日志, 使用 error_log() 函数还是 monolog 库 ?
            #error_log("SentinelClient error: $e");
            return null;
        }
    }

    /**
     * 底层操作, 应用代码请使用 `entry($name)` 。
     *
     * @param string $name 资源名称
     * @return SentinelEntry
     * @throws BlockException
     * @throws TException
     * @throws TTransportException
     * @see entry
     */
    protected function doEntry($name) {
        $this->ensureOpen();
        $id = $this->client_->entry($name);
        return new SentinelEntry($this->client_, $id);
    }
}
