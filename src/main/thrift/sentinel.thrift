namespace java com.alibaba.csp.sentinel.sidecar.rpc
namespace php Sentinel.Rpc

service Sentinel {

	i32 entry(1:string name);
	
	void exit(1:i32 id);

}
