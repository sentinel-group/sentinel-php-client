// vim: set noet ts=4 sw=4:

namespace java com.alibaba.csp.sentinel.sidecar.rpc
namespace php  Sentinel.Rpc

exception BlockException {

	1:string ruleLimitApp;
	
	2:string message;
	
}

service Sentinel {

	i32 entry(1:string name) throws ( 1:BlockException ex );

	// TODO "exit" 在 php 中是关键字, php 7.0 才支持使用此作为自定义名字. 要支持 php 5.5 则需要改名 ?
	void exit(1:i32 id);

}
