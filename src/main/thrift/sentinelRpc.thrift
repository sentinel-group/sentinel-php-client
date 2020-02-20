// vim: set noet ts=4 sw=4:

include "sentinelClient.thrift"

namespace java com.alibaba.csp.sentinel.sidecar.rpc
namespace php  Sentinel.Rpc

/**
* sentinel 客户端与 sidecar 通信的底层 RPC 接口定义。
**/
service SentinelRpc {

	i32 entry(1:string name) throws ( 1:sentinelClient.BlockException ex );

	// TODO "exit" 在 php 中是关键字, php 7.0 才支持使用此作为自定义名字. 要支持 php 5.5 则需要改名 ?
	void exit(1:i32 id);

}
