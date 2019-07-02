欢迎使用RestPHP,当前版本为2.0。

# RestPHP能帮您做什么？
使用RestPHP能够协助您快速进行php语言的RESTFul接口编码，满足您对RESTFul的所有期望。

# RestPHP的特点
支持路径参数，如：/users/$userId 或 /users/$userId/orders/$orderId

支持各种HTTP Method，支持Form表单、json、Xml的报文请求

支持多语言设置

# Hello world
1、PHP环镜运行要求：PHP5.3+

2、配置URL重新规则，将所有请求地址重写到indxt.php。如，Nginx重写配置：

    location / {
	index  index.php;
        if (!-e $request_filename) {            
            rewrite ^/(.*)$ /index.php?$1 last;                
        }            
    }

3、新建控制器类：php\controller\HelloWorldController.php

    <?php
    namespace php\controller;
    use restphp\http\RestHttpResponse;
    class HelloWorldController {
        #RequestMapping(value="/hello", method="GET")
        public function hello() {
            RestHttpResponse::html("Hello world!");
        }
    }

4、在浏览器中访问build.php，编译路径映射，如：http://localhost/build.php

5、访问：http://localhost/hello 果看结果：Hello world!

# 路径映身编写规则
1、使用方法 #RequestMapping 标注，可以写在类名（class）和方法名（function）上。完整写法为：
 #RequestMapping(value="/hello", method="GET", before="php\service\AuthService::sign", afeter="php\service\response::format")

2、#RequestMapping方法参数value为必填参数，表示请求地址路径。

如果类和方法名都有指定，则方法访问路径等于类指定路径+方法指定路径。

路径值中可以设置路径参数，命名规则与php变量命名一致，以$符号开始，如：/users/$userId/avatar，方法中使用RestHttpRequest::getPathValue方法获取，如：$userId = = RestHttpRequest::getPathValue("userId");

3、#RequestMapping方法参数method，可选参数。表示方法支持的HTTP Method访问值

4、#RequestMapping方法参数before，可选参数。前置处理方法。

4、#RequestMapping方法参数after，可选参数。后置处理方法。

# 其他规则

1、框架采用根据类名和命称空间自动加载文件，因此，一个PHP文件只能编写一个类，类名要和文件名一致，大小写也要一致，并且类文件也应该要指定正确的名称空间。

2、语言包通过$GOLBAL['_LANG'] 指定，格式为key=>value数组，key命名规则为:[key]，写法参考 config\lang.config.php

3、MySQL数据库通过$GOLBAL['_DB_MYSQL '] 指定，配置写法参考：config\env.config.php

4、提供前端RESTFul基本请求封装，详细参考：view\index.html
