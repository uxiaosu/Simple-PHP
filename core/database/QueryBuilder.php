<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 查询构建器类 - 提供流畅的SQL查询构建接口
 */

namespace SimplePHP\Core\Database;

class QueryBuilder
{
    /**
     * @var Database 数据库实例
     */
    private $db;
    
    /**
     * @var string 表名
     */
    private $table;
    
    /**
     * @var array 查询部分
     */
    private $query = [
        'select' => ['*'],
        'where' => [],
        'join' => [],
        'orderBy' => [],
        'groupBy' => [],
        'having' => [],
        'limit' => null,
        'offset' => null
    ];
    
    /**
     * @var array 绑定参数
     */
    private $bindings = [];
    
    /**
     * 构造函数
     *
     * @param Database $db 数据库实例
     * @param string $table 表名
     */
    public function __construct(Database $db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }
    
    /**
     * 设置SELECT字段
     *
     * @param string|array $columns 要选择的列
     * @return QueryBuilder
     */
    public function select($columns = ['*'])
    {
        $this->query['select'] = is_array($columns) ? $columns : func_get_args();
        return $this;
    }
    
    /**
     * 添加WHERE条件
     *
     * @param string $column 列名
     * @param string $operator 操作符
     * @param mixed $value 值
     * @param string $boolean 条件连接词（AND/OR）
     * @return QueryBuilder
     */
    public function where($column, $operator = null, $value = null, $boolean = 'AND')
    {
        // 如果只提供两个参数，假设操作符是'='
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->query['where'][] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ];
        
        return $this;
    }
    
    /**
     * 添加OR WHERE条件
     *
     * @param string $column 列名
     * @param string $operator 操作符
     * @param mixed $value 值
     * @return QueryBuilder
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'OR');
    }
    
    /**
     * 添加WHERE IN条件
     *
     * @param string $column 列名
     * @param array $values 值数组
     * @param string $boolean 条件连接词（AND/OR）
     * @return QueryBuilder
     */
    public function whereIn($column, array $values, $boolean = 'AND')
    {
        $this->query['where'][] = [
            'type' => 'in',
            'column' => $column,
            'values' => $values,
            'boolean' => $boolean
        ];
        
        return $this;
    }
    
    /**
     * 添加OR WHERE IN条件
     *
     * @param string $column 列名
     * @param array $values 值数组
     * @return QueryBuilder
     */
    public function orWhereIn($column, array $values)
    {
        return $this->whereIn($column, $values, 'OR');
    }
    
    /**
     * 添加JOIN子句
     *
     * @param string $table 要连接的表
     * @param string $first 第一个条件列
     * @param string $operator 操作符
     * @param string $second 第二个条件列
     * @param string $type 连接类型（INNER, LEFT, RIGHT等）
     * @return QueryBuilder
     */
    public function join($table, $first, $operator = null, $second = null, $type = 'INNER')
    {
        // 如果只提供三个参数，假设操作符是'='
        if ($second === null) {
            $second = $operator;
            $operator = '=';
        }
        
        $this->query['join'][] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => $type
        ];
        
        return $this;
    }
    
    /**
     * 添加LEFT JOIN子句
     *
     * @param string $table 要连接的表
     * @param string $first 第一个条件列
     * @param string $operator 操作符
     * @param string $second 第二个条件列
     * @return QueryBuilder
     */
    public function leftJoin($table, $first, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }
    
    /**
     * 添加RIGHT JOIN子句
     *
     * @param string $table 要连接的表
     * @param string $first 第一个条件列
     * @param string $operator 操作符
     * @param string $second 第二个条件列
     * @return QueryBuilder
     */
    public function rightJoin($table, $first, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }
    
    /**
     * 添加ORDER BY子句
     *
     * @param string $column 列名
     * @param string $direction 排序方向（ASC/DESC）
     * @return QueryBuilder
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $direction = strtoupper($direction);
        
        if (!in_array($direction, ['ASC', 'DESC'])) {
            $direction = 'ASC';
        }
        
        $this->query['orderBy'][] = [
            'column' => $column,
            'direction' => $direction
        ];
        
        return $this;
    }
    
    /**
     * 添加GROUP BY子句
     *
     * @param string|array $columns 列名
     * @return QueryBuilder
     */
    public function groupBy($columns)
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->query['groupBy'] = array_merge($this->query['groupBy'], $columns);
        
        return $this;
    }
    
    /**
     * 添加HAVING子句
     *
     * @param string $column 列名
     * @param string $operator 操作符
     * @param mixed $value 值
     * @param string $boolean 条件连接词（AND/OR）
     * @return QueryBuilder
     */
    public function having($column, $operator = null, $value = null, $boolean = 'AND')
    {
        // 如果只提供两个参数，假设操作符是'='
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->query['having'][] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ];
        
        return $this;
    }
    
    /**
     * 添加OR HAVING子句
     *
     * @param string $column 列名
     * @param string $operator 操作符
     * @param mixed $value 值
     * @return QueryBuilder
     */
    public function orHaving($column, $operator = null, $value = null)
    {
        return $this->having($column, $operator, $value, 'OR');
    }
    
    /**
     * 添加LIMIT子句
     *
     * @param int $limit 限制数量
     * @return QueryBuilder
     */
    public function limit($limit)
    {
        $this->query['limit'] = (int) $limit;
        return $this;
    }
    
    /**
     * 添加OFFSET子句
     *
     * @param int $offset 偏移量
     * @return QueryBuilder
     */
    public function offset($offset)
    {
        $this->query['offset'] = (int) $offset;
        return $this;
    }
    
    /**
     * 设置分页参数
     *
     * @param int $page 页码
     * @param int $perPage 每页条数
     * @return QueryBuilder
     */
    public function paginate($page, $perPage = 20)
    {
        $page = max(1, (int) $page);
        $perPage = (int) $perPage;
        
        return $this->limit($perPage)->offset(($page - 1) * $perPage);
    }
    
    /**
     * 执行查询并获取所有结果
     *
     * @return array
     */
    public function get()
    {
        $sql = $this->buildSelectQuery();
        $this->db->prepare($sql);
        
        // 绑定参数
        foreach ($this->bindings as $key => $value) {
            $this->db->bind($key + 1, $value);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * 执行查询并获取第一条结果
     *
     * @return mixed
     */
    public function first()
    {
        $this->limit(1);
        $results = $this->get();
        
        return !empty($results) ? $results[0] : null;
    }
    
    /**
     * 插入数据
     *
     * @param array $data 要插入的数据
     * @return bool|int 成功返回插入ID，失败返回false
     */
    public function insert(array $data)
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $quotedColumns = array_map(function($column) {
            return $this->db->quoteIdentifier($column);
        }, $columns);
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->db->quoteIdentifier($this->table),
            implode(', ', $quotedColumns),
            implode(', ', $placeholders)
        );
        
        $this->db->prepare($sql);
        
        // 绑定参数
        foreach ($values as $key => $value) {
            $this->db->bind($key + 1, $value);
        }
        
        // 执行插入
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * 更新数据
     *
     * @param array $data 要更新的数据
     * @return bool|int 成功返回影响的行数，失败返回false
     */
    public function update(array $data)
    {
        $setStatements = [];
        $values = [];
        
        foreach ($data as $column => $value) {
            $setStatements[] = $this->db->quoteIdentifier($column) . ' = ?';
            $values[] = $value;
        }
        
        $sql = sprintf(
            "UPDATE %s SET %s",
            $this->db->quoteIdentifier($this->table),
            implode(', ', $setStatements)
        );
        
        // 添加WHERE条件
        if (!empty($this->query['where'])) {
            $sql .= ' ' . $this->buildWhereClause();
            $values = array_merge($values, $this->bindings);
        }
        
        $this->db->prepare($sql);
        
        // 绑定参数
        foreach ($values as $key => $value) {
            $this->db->bind($key + 1, $value);
        }
        
        // 执行更新
        if ($this->db->execute()) {
            return $this->db->rowCount();
        }
        
        return false;
    }
    
    /**
     * 删除数据
     *
     * @return bool|int 成功返回影响的行数，失败返回false
     */
    public function delete()
    {
        $sql = sprintf("DELETE FROM %s", $this->db->quoteIdentifier($this->table));
        
        // 添加WHERE条件
        if (!empty($this->query['where'])) {
            $sql .= ' ' . $this->buildWhereClause();
        }
        
        $this->db->prepare($sql);
        
        // 绑定参数
        foreach ($this->bindings as $key => $value) {
            $this->db->bind($key + 1, $value);
        }
        
        // 执行删除
        if ($this->db->execute()) {
            return $this->db->rowCount();
        }
        
        return false;
    }
    
    /**
     * 统计记录数
     *
     * @param string $column 要统计的列
     * @return int
     */
    public function count($column = '*')
    {
        $this->query['select'] = ["COUNT($column) as count"];
        $result = $this->first();
        
        return isset($result['count']) ? (int) $result['count'] : 0;
    }
    
    /**
     * 构建完整的SELECT查询
     *
     * @return string
     */
    private function buildSelectQuery()
    {
        $sql = [
            'select' => $this->buildSelectClause(),
            'from' => sprintf("FROM %s", $this->db->quoteIdentifier($this->table)),
            'join' => $this->buildJoinClause(),
            'where' => $this->buildWhereClause(),
            'groupBy' => $this->buildGroupByClause(),
            'having' => $this->buildHavingClause(),
            'orderBy' => $this->buildOrderByClause(),
            'limit' => $this->buildLimitClause(),
        ];
        
        return implode(' ', array_filter($sql));
    }
    
    /**
     * 构建SELECT子句
     *
     * @return string
     */
    private function buildSelectClause()
    {
        $columns = array_map(function($column) {
            return $column === '*' ? $column : $this->db->quoteIdentifier($column);
        }, $this->query['select']);
        
        return 'SELECT ' . implode(', ', $columns);
    }
    
    /**
     * 构建JOIN子句
     *
     * @return string
     */
    private function buildJoinClause()
    {
        if (empty($this->query['join'])) {
            return '';
        }
        
        $joins = [];
        
        foreach ($this->query['join'] as $join) {
            $joins[] = sprintf(
                "%s JOIN %s ON %s %s %s",
                $join['type'],
                $this->db->quoteIdentifier($join['table']),
                $this->db->quoteIdentifier($join['first']),
                $join['operator'],
                $this->db->quoteIdentifier($join['second'])
            );
        }
        
        return implode(' ', $joins);
    }
    
    /**
     * 构建WHERE子句
     *
     * @return string
     */
    private function buildWhereClause()
    {
        if (empty($this->query['where'])) {
            return '';
        }
        
        $whereClause = 'WHERE ';
        $conditions = [];
        $paramIndex = 1;
        
        foreach ($this->query['where'] as $index => $condition) {
            $boolean = $index === 0 ? '' : $condition['boolean'] . ' ';
            
            if (isset($condition['type']) && $condition['type'] === 'in') {
                $placeholders = array_fill(0, count($condition['values']), '?');
                $conditions[] = $boolean . $this->db->quoteIdentifier($condition['column']) . ' IN (' . implode(', ', $placeholders) . ')';
                
                // 添加绑定参数
                foreach ($condition['values'] as $value) {
                    $this->bindings[] = $value;
                }
            } else {
                $conditions[] = $boolean . $this->db->quoteIdentifier($condition['column']) . ' ' . $condition['operator'] . ' ?';
                $this->bindings[] = $condition['value'];
            }
        }
        
        return $whereClause . implode(' ', $conditions);
    }
    
    /**
     * 构建GROUP BY子句
     *
     * @return string
     */
    private function buildGroupByClause()
    {
        if (empty($this->query['groupBy'])) {
            return '';
        }
        
        $columns = array_map(function($column) {
            return $this->db->quoteIdentifier($column);
        }, $this->query['groupBy']);
        
        return 'GROUP BY ' . implode(', ', $columns);
    }
    
    /**
     * 构建HAVING子句
     *
     * @return string
     */
    private function buildHavingClause()
    {
        if (empty($this->query['having'])) {
            return '';
        }
        
        $havingClause = 'HAVING ';
        $conditions = [];
        
        foreach ($this->query['having'] as $index => $condition) {
            $boolean = $index === 0 ? '' : $condition['boolean'] . ' ';
            $conditions[] = $boolean . $this->db->quoteIdentifier($condition['column']) . ' ' . $condition['operator'] . ' ?';
            $this->bindings[] = $condition['value'];
        }
        
        return $havingClause . implode(' ', $conditions);
    }
    
    /**
     * 构建ORDER BY子句
     *
     * @return string
     */
    private function buildOrderByClause()
    {
        if (empty($this->query['orderBy'])) {
            return '';
        }
        
        $orders = [];
        
        foreach ($this->query['orderBy'] as $order) {
            $orders[] = $this->db->quoteIdentifier($order['column']) . ' ' . $order['direction'];
        }
        
        return 'ORDER BY ' . implode(', ', $orders);
    }
    
    /**
     * 构建LIMIT和OFFSET子句
     *
     * @return string
     */
    private function buildLimitClause()
    {
        $sql = '';
        
        if ($this->query['limit'] !== null) {
            $sql = 'LIMIT ' . $this->query['limit'];
            
            if ($this->query['offset'] !== null) {
                $sql .= ' OFFSET ' . $this->query['offset'];
            }
        }
        
        return $sql;
    }
} 