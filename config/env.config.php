<?php
return array(
    'dev' => array(
        'DATABASE_MYSQL' => array(
            'DB_PREFIX' => '', //表前缀
            'Def_Select'	=> array(	//查询库，一般是从库或只读库
                'dbhost'	=> '127.0.0.1',
                'dbport'	=> 3306,
                'dbuser'	=> 'root',
                'dbpass'	=> '123456',
                'dbname'	=> 'restphp',
                'charset'	=> 'UTF8',
                'dbtype'	=> 1,
                'linktype'	=> 1,
                'fetchtype'	=> 1
            ),
            'Def_Update'	=> array(	//有数据修改权限的库，一般指主库
                'dbhost'	=> '127.0.0.1',
                'dbport'	=> 3306,
                'dbuser'	=> 'root',
                'dbpass'	=> '123456',
                'dbname'	=> 'restphp',
                'charset'	=> 'UTF8',
                'dbtype'	=> 1,
                'linktype'	=> 1,
                'fetchtype'	=> 1
            )
        )
    ),
    'pro' => array(
        'DATABASE_MYSQL' => array(
            'DB_PREFIX' => '', //表前缀
            'Def_Select'	=> array(	//查询库，一般是从库或只读库
                'dbhost'	=> '127.0.0.1',
                'dbport'	=> 3306,
                'dbuser'	=> 'root',
                'dbpass'	=> '123456',
                'dbname'	=> 'restphp',
                'charset'	=> 'UTF8',
                'dbtype'	=> 1,
                'linktype'	=> 1,
                'fetchtype'	=> 1
            ),
            'Def_Update'	=> array(	//有数据修改权限的库，一般指主库
                'dbhost'	=> '127.0.0.1',
                'dbport'	=> 3306,
                'dbuser'	=> 'root',
                'dbpass'	=> '123456',
                'dbname'	=> 'restphp',
                'charset'	=> 'UTF8',
                'dbtype'	=> 1,
                'linktype'	=> 1,
                'fetchtype'	=> 1
            )
        )
    )
);