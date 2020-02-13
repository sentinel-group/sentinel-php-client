namespace java com.alibaba.csp.sentinel.sidecar.rpc
namespace php Sentinel.Rpc

exception BlockException {

	1:string ruleLimitApp;
	
	2:string message;
	
}

service Sentinel {

	i32 entry(1:string name) throws ( 1:BlockException ex );
	
	void exit(1:i32 id);

}
