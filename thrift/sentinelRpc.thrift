// vim: set noet ts=4 sw=4:

include "sentinelClient.thrift"

namespace java com.alibaba.csp.sentinel.sidecar.rpc
namespace php  Sentinel.Rpc

/**
* sentinel 客户端与 sidecar 通信的底层 RPC 接口定义。
**/
service SentinelRpc {

	/*
	* 获取资源。
	**/
	i32 entry(1:string name) throws ( 1:sentinelClient.BlockException ex );

	/**
	* 释放资源。
	*
	* java 使用 "exit", 但 "exit" 是 php 关键字, php 7.0 才支持使用其作为自定义名字.
	* 修改为使用 "close" 以增强兼容性。
	**/
 	void close(1:i32 id);

}
