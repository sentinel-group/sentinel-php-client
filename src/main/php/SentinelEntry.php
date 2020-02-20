<?php

namespace Sentinel;

use Sentinel\Rpc\SentinelRpcClient;

/**
 * 受保护的资源访问入口。
 *
 * 调用 SentinelClient.entry() 成功后返回此类对象，
 * 其持有受保护的资源访问入口，销毁此类对象即自动释放资源访问入口。
 *
 * @package Sentinel
 */
class SentinelEntry
{
    /**
     * @var SentinelRpcClient
     */
    protected $client_ = null;

    /**
     * @var int
     */
    protected $id_ = null;

    /**
     * SentinelEntry constructor.
     * 调用 SentinelClient.entry() 成功后返回此类对象。
     *
     * @param SentinelRpcClient $client
     * @param int $id
     */
    public function __construct($client, $id)
    {
        $this->client_ = $client;
        $this->id_ = $id;
    }

    public function __destruct()
    {
        $this->client_->exit($this->id_);
    }
}
