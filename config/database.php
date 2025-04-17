<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 数据库配置文件
 */

return [
    // 是否启用数据库连接
    'enable' => false, // 设置为false以禁用数据库连接，避免不必要的错误通知
    
    // 默认数据库连接
    'default' => 'mysql',
    
    // 数据库连接配置
    'connections' => [
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'port'      => 3306,
            'database'  => 'simplepphp',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ],
        
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => ROOT_PATH . '/database/database.sqlite',
            'prefix'   => '',
        ],
        
        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'port'     => 5432,
            'database' => 'simplepphp',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ],
    ],
    
    // 是否启用数据库查询日志
    'query_log' => false,
    
    // 最大连接数 (PDO连接池大小)
    'max_connections' => 100,
    
    // PDO选项
    'options' => [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
]; 