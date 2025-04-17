<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 数据库类 - 提供安全的数据库连接和操作
 */

namespace SimplePHP\Core\Database;

use SimplePHP\Core\Config;
use PDO;
use PDOException;

class Database
{
    /**
     * @var PDO 数据库连接实例
     */
    private $connection;
    
    /**
     * @var array 当前连接配置
     */
    private $config;
    
    /**
     * @var PDOStatement 最后一个预编译语句
     */
    private $statement;
    
    /**
     * @var array 执行的查询记录
     */
    private $queries = [];
    
    /**
     * 构造函数 - 初始化数据库连接
     */
    public function __construct()
    {
        $this->config = $this->getConnectionConfig();
        $this->connect();
    }
    
    /**
     * 获取数据库连接配置
     *
     * @return array
     */
    private function getConnectionConfig()
    {
        $defaultConnection = Config::get('database.default', 'mysql');
        return Config::get('database.connections.' . $defaultConnection, []);
    }
    
    /**
     * 连接到数据库
     *
     * @throws PDOException 连接失败时抛出异常
     */
    private function connect()
    {
        // 检查是否需要数据库连接
        if (!Config::get('database.enable', true)) {
            return; // 如果配置中禁用了数据库，直接返回
        }
        
        try {
            $dsn = $this->createDsn();
            
            // 获取PDO选项，如果没有指定则使用默认值
            $defaultOptions = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $configOptions = Config::get('database.options', []);
            $options = array_merge($defaultOptions, $configOptions);
            
            // 获取用户名和密码，如果没有指定则使用空字符串
            $username = $this->config['username'] ?? '';
            $password = $this->config['password'] ?? '';
            
            // 创建PDO连接
            $this->connection = new PDO($dsn, $username, $password, $options);
            
            // 设置字符集（仅适用于MySQL）
            if (($this->config['driver'] ?? 'mysql') === 'mysql' && isset($this->config['charset'])) {
                $charset = $this->config['charset'];
                $this->connection->exec("SET NAMES '{$charset}'");
                
                // 如果指定了校对规则，也一并设置
                if (isset($this->config['collation'])) {
                    $collation = $this->config['collation'];
                    $this->connection->exec("SET collation_connection = '{$collation}'");
                }
            }
        } catch (PDOException $e) {
            // 记录错误
            error_log("数据库连接失败: " . $e->getMessage());
            
            // 如果是开发环境，重新抛出异常；否则返回空
            if (Config::get('app.debug', false)) {
                throw new PDOException("数据库连接失败: " . $e->getMessage());
            }
        }
    }
    
    /**
     * 创建DSN连接字符串
     *
     * @return string
     */
    private function createDsn()
    {
        $driver = $this->config['driver'] ?? 'mysql';
        
        switch ($driver) {
            case 'mysql':
                $host = $this->config['host'] ?? 'localhost';
                $port = $this->config['port'] ?? 3306;
                $database = $this->config['database'] ?? 'simplepphp';
                return "mysql:host={$host};port={$port};dbname={$database}";
                
            case 'sqlite':
                $database = $this->config['database'] ?? ROOT_PATH . '/database/database.sqlite';
                return "sqlite:{$database}";
                
            case 'pgsql':
                $host = $this->config['host'] ?? 'localhost';
                $port = $this->config['port'] ?? 5432;
                $database = $this->config['database'] ?? 'simplepphp';
                return "pgsql:host={$host};port={$port};dbname={$database}";
                
            default:
                throw new \Exception("不支持的数据库驱动: {$driver}");
        }
    }
    
    /**
     * 准备一个SQL语句
     *
     * @param string $sql SQL语句
     * @return Database
     */
    public function prepare($sql)
    {
        // 检查是否存在连接
        if ($this->connection === null) {
            // 如果是开发环境，记录警告日志
            if (Config::get('app.debug', false)) {
                error_log("警告: 尝试准备SQL语句但数据库连接不存在");
            }
            return $this;
        }
        
        $this->statement = $this->connection->prepare($sql);
        
        if (Config::get('database.show_queries', false)) {
            $this->logQuery($sql);
        }
        
        return $this;
    }
    
    /**
     * 绑定参数到预编译语句
     *
     * @param mixed $param 参数标识符或参数位置
     * @param mixed $value 参数值
     * @param int $type 参数类型
     * @return Database
     */
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        
        $this->statement->bindValue($param, $value, $type);
        return $this;
    }
    
    /**
     * 执行预编译语句
     *
     * @param array $params 参数数组
     * @return bool
     */
    public function execute($params = [])
    {
        // 检查是否存在预编译语句
        if ($this->statement === null) {
            // 如果是开发环境，记录警告日志
            if (Config::get('app.debug', false)) {
                error_log("警告: 尝试执行SQL语句但预编译语句不存在");
            }
            return false;
        }
        
        return $this->statement->execute($params);
    }
    
    /**
     * 执行一个简单的SQL查询
     *
     * @param string $sql SQL查询
     * @param array $params 参数数组
     * @return mixed 查询结果
     */
    public function query($sql, $params = [])
    {
        // 检查是否存在连接
        if ($this->connection === null) {
            // 如果是开发环境，记录警告日志
            if (Config::get('app.debug', false)) {
                error_log("警告: 尝试执行SQL查询但数据库连接不存在");
            }
            return false;
        }
        
        $this->prepare($sql);
        
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $this->bind($param, $value);
            }
        }
        
        $this->execute();
        return $this;
    }
    
    /**
     * 返回单行结果
     *
     * @return mixed
     */
    public function single()
    {
        if ($this->statement === null) {
            return null;
        }
        
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * 返回所有结果
     *
     * @return array
     */
    public function resultSet()
    {
        if ($this->statement === null) {
            return [];
        }
        
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 获取最后插入的ID
     *
     * @return string
     */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
    
    /**
     * 开始一个事务
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }
    
    /**
     * 提交一个事务
     *
     * @return bool
     */
    public function commit()
    {
        return $this->connection->commit();
    }
    
    /**
     * 回滚一个事务
     *
     * @return bool
     */
    public function rollBack()
    {
        return $this->connection->rollBack();
    }
    
    /**
     * 返回结果行数
     *
     * @return int
     */
    public function rowCount()
    {
        if ($this->statement === null) {
            return 0;
        }
        
        return $this->statement->rowCount();
    }
    
    /**
     * 记录查询
     *
     * @param string $sql SQL语句
     */
    private function logQuery($sql)
    {
        $this->queries[] = [
            'sql' => $sql,
            'time' => microtime(true)
        ];
    }
    
    /**
     * 获取执行的查询记录
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }
    
    /**
     * 关闭数据库连接
     */
    public function close()
    {
        // 如果存在预编译语句，先关闭它
        if ($this->statement !== null) {
            $this->statement->closeCursor();
            $this->statement = null;
        }
        
        // 关闭数据库连接
        if ($this->connection !== null) {
            $this->connection = null;
        }
    }
    
    /**
     * 安全地引用表名或字段名
     *
     * @param string $name 表名或字段名
     * @return string
     */
    public function quoteIdentifier($name)
    {
        $driver = $this->config['driver'] ?? 'mysql';
        
        switch ($driver) {
            case 'mysql':
                return "`" . str_replace("`", "``", $name) . "`";
                
            case 'pgsql':
                return "\"" . str_replace("\"", "\"\"", $name) . "\"";
                
            default:
                return $name;
        }
    }
    
    /**
     * 创建一个查询构建器实例
     *
     * @param string $table 表名
     * @return QueryBuilder
     */
    public function table($table)
    {
        // 如果没有数据库连接，记录警告并返回null
        if ($this->connection === null) {
            if (Config::get('app.debug', false)) {
                error_log("警告: 尝试创建查询构建器但数据库连接不存在");
            }
            // 注意：这里返回null可能会导致调用方出错，但比返回无效的查询构建器更容易发现问题
            return null;
        }
        
        return new QueryBuilder($this, $table);
    }
} 