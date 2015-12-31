## 开始
### 思想
![k.i.s.s](http://file.100xuexi.com/XXSub/MatUpPT/Image/201301231128293749360.jpg)

 - 模板与程序解绑，开发模式只要模板文件和程序代码存在一个就能访问。
   - 只有程序代码，直接返回json数据
   - 只有模板代码，能直接返回模板执行后样子。模板文件中允许指定默认数据
 - 框架解耦，尽量少用继承，直接通过工具类获取数据以及调度
 - 目录中只包含需要改动文件，库文件使用打包工具统一管理
 - 开发模式下代码自动生成
 - 提供完善测试方案

### 目录结构
>/www/ `网站根目录，只有该目录下文件才能被访问到`
>>/www/static/ `静态资源文件夹`
>>>/www/static/js/ `js目录`
>>>
>>>/www/static/css/ `css目录`
>>>
>>>/www/static/images/ `图片目录`
>>
>>/www/index.php `应用入口`
>>
>>/www/tpl.pc/ `pc端模板目录`
>>>/www/tpl.pc/index.tpl `pc端初始页面`
>>
>>/www/demo.pc/ `pc端demo设计页面`
>
>/page/ `控制器类目录`
>>/page/index.php `根目录页面控制器`
>>
>>/page/user.index.php `用户目录页面控制器`
>
>/data/ `数据模型目录`
>
>/config/ `配置文件目录`
>
>>/config/cfg.inc.php `网站配置文件`
>>
>>/config/di.inc.php `依赖注入配置文件`
>>
>>/config/ds.inc.php `数据源配置文件，可以自动检查数据源是否可用`
>>
>>/config/zh-cn.language.mo `中文语言包`
>
>/cache/ `cache目录，包含smarty编译文件，session文件等等`
>
>/script/ `后台脚本`
>
>/logs/ `网站日志目录`
### 开发
#### 创建目录结构

 - `composer create-project warper myapp`

#### 开启开发模式
 - `composer run dev`
 - `> server start at 0.0.0.0:8080`

#### 创建user/login
 - 访问http://127.0.0.1:8080/user/login
 - 点击创建页面
 - 自动生成相关页面

### 调试
 - socketlog

### 测试
 - 测试文件和主文件在同一目录，同名且以.test.php结尾
 - `composer run testcase` 运行所有测试用例

### 部署
 - `composer package` 自动打包项目
 - 自动生成数据库结构文件
 - 去除测试用例以及相关源文件