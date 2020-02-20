<?php

namespace Sentinel;

use Sentinel\Rpc\SentinelRpcClient;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocket;

class SentinelClient
{
    protected $client = null;

    /**
     * Client constructor.
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
        $socket = new TSocket($host, $port, $persist);
        if (!$socket->isOpen()) {
            $socket->open();
        }

        $trans = new TFramedTransport($socket);
        $protocol = new TBinaryProtocolAccelerated($trans, true, true);
        $input = $protocol;
        $output = $protocol;
        $this->client = new SentinelRpcClient($input, $output);
    }

    public function entry($name) {
        $id = $this->client->entry($name);
    }
}
