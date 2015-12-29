### 配置管理
 - warper::get_cfg("cache");//"redis://cache.wefit.com"
 - 获取配置，如果配置不存在，自动生成

### socketlog 用法
https://github.com/luofei614/SocketLog

### 依赖注入
订阅模式是最通用的模式，但没有rpc直接。
绝大部分订阅模式只有一个消费者，所有要做精简版本的。


万能
打桩
buf-异步
并行

#### dns 概念
数据源：
$config['cache'] = "redis://cache.wefit.com/";
$config['weixinapi'] = 'http://api.wefit.com';

$loacal_dns = array(
	'cache.wefit.com'=>'127.0.0.1', //直接解析
	'api.wefit.com' => array('127.0.0.1', '10.0.0.1'), //轮询,权重todo
);

如果读写分离，需要设置两个数据源


#### 用法
 - $my_log = warper::di("log"), 返回一个全局log实例。 $my_log->trace("kk");
 - warper::batch(array('callback', param));