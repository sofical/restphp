欢迎使用RestPHP,当前版本为2.0。

# RestPHP能帮您做什么？
使用RestPHP能够协助您快速进行php语言的RESTFul接口编码，满足您对RESTFul的所有期望。

# RestPHP的特点
支持路径参数，如：/users/$userId 或 /users/$userId/orders/$orderId

支持各种HTTP Method，支持Form表单、json、Xml的报文请求

# Hello world
1、PHP环镜运行要求：PHP5.3+

2、配置URL重新规则，将所有请求地址重写到indxt.php。如，Nginx重写配置：

    location / {
	index  index.php;
        if (!-e $request_filename) {            
            rewrite ^/(.*)$ /index.php?$1 last;                
        }            
    }
