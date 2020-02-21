// vim: set noet ts=4 sw=4:

namespace java com.alibaba.csp.sentinel.sidecar.client
namespace php  Sentinel

exception BlockException {

	1:string ruleLimitApp;

	2:string message;

}
