Sentinel PHP Client
===

## 快速体验

如果您是第一次接触 Sentinel，可通过 sentinel-php-demo 快速体验 Sentinel 流量防护能力。
Linux 下可使用 docker 启动 demo ，启动命令如下 (选项 `--net=host` 表示容器使用宿主机网络)：

```sh
docker run --name=demo --net=host -d registry.cn-hangzhou.aliyuncs.com/ahas/sentinel-php-demo
```

使用结束后，可通过如下命令删除 demo 容器：

```sh
docker rm demo -f
```

demo 容器内包含：

* php + nginx 环境，nginx 默认监听 8080 端口。
* sentinel sidecar 服务，默认监听 9090 端口。
* php demo 测试代码。

demo 启动后，可通过 http://localhost:8080/ 访问 demo 测试页面。
demo 中默认定义了一个名为 `hello` 的资源，保护该资源每秒钟最多只允许被访问 3 次。
页面 `/hello.php` 使用了该资源，每秒请求压力超过 3 次时，将提示超过压力限制的请求稍后再重试。
demo 主页是一个简单的测试页面，快速连续点击 **请求** 按钮对 `/hello.php` 页面发起请求，可看到类似如下的测试结果。

![demo](demo/demo.png)

## 在 PHP 中使用 Sentinel

## 接入 AHAS

## 配置限流规则
