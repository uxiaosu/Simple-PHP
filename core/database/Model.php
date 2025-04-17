<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 基础模型类 - 所有模型的父类
 */

namespace SimplePHP\Core\Database;

use SimplePHP\Core\Config;

abstract class Model
{
    /**
     * @var Database 数据库实例
     */
    protected $db;
    
    /**
     * @var string 表名
     */
    protected $table;
    
    /**
     * @var string 主键
     */
    protected $primaryKey = 'id';
    
    /**
     * @var array 可填充字段
     */
    protected $fillable = [];
    
    /**
     * @var array 隐藏字段（不返回给客户端）
     */
    protected $hidden = [];
    
    /**
     * 构造函数 - 初始化模型
     */
    public function __construct()
    {
        $this->db = new Database();
        
        // 如果没有指定表名，则根据类名推断
        if ($this->table === null) {
            $className = basename(str_replace('\\', '/', get_called_class()));
            $className = preg_replace('/Model$/', '', $className);
            $this->table = strtolower($className);
        }
    }
    
    /**
     * 查询构建器
     *
     * @return QueryBuilder
     */
    public function query()
    {
        return $this->db->table($this->table);
    }
    
    /**
     * 查找单条记录
     *
     * @param mixed $id 主键值
     * @return array|null
     */
    public function find($id)
    {
        return $this->query()->where($this->primaryKey, $id)->first();
    }
    
    /**
     * 获取所有记录
     *
     * @return array
     */
    public function all()
    {
        return $this->query()->get();
    }
    
    /**
     * 创建新记录
     *
     * @param array $data 数据
     * @return bool|int 成功返回ID，失败返回false
     */
    public function create(array $data)
    {
        // 仅保留可填充字段
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        // 添加创建时间
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        
        // 添加更新时间
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->query()->insert($data);
    }
    
    /**
     * 更新记录
     *
     * @param mixed $id 主键值
     * @param array $data 数据
     * @return bool|int 成功返回影响的行数，失败返回false
     */
    public function update($id, array $data)
    {
        // 仅保留可填充字段
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        // 添加更新时间
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->query()->where($this->primaryKey, $id)->update($data);
    }
    
    /**
     * 删除记录
     *
     * @param mixed $id 主键值
     * @return bool|int 成功返回影响的行数，失败返回false
     */
    public function delete($id)
    {
        return $this->query()->where($this->primaryKey, $id)->delete();
    }
    
    /**
     * 批量更新
     *
     * @param array $data 数据
     * @return bool|int 成功返回影响的行数，失败返回false
     */
    public function batchUpdate(array $data)
    {
        // 仅保留可填充字段
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        // 添加更新时间
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->query()->update($data);
    }
    
    /**
     * 批量删除
     *
     * @return bool|int 成功返回影响的行数，失败返回false
     */
    public function batchDelete()
    {
        return $this->query()->delete();
    }
    
    /**
     * 统计记录数
     *
     * @param string $column 要统计的列
     * @return int
     */
    public function count($column = '*')
    {
        return $this->query()->count($column);
    }
    
    /**
     * 分页获取数据
     *
     * @param int $page 页码
     * @param int $perPage 每页条数
     * @return array
     */
    public function paginate($page = 1, $perPage = 20)
    {
        $total = $this->count();
        $data = $this->query()->paginate($page, $perPage)->get();
        
        return [
            'data' => $this->processData($data),
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'from' => ($page - 1) * $perPage + 1,
                'to' => min($page * $perPage, $total)
            ]
        ];
    }
    
    /**
     * 处理数据（移除隐藏字段）
     *
     * @param array $data 数据
     * @return array
     */
    protected function processData(array $data)
    {
        if (empty($this->hidden)) {
            return $data;
        }
        
        $result = [];
        
        foreach ($data as $item) {
            foreach ($this->hidden as $field) {
                unset($item[$field]);
            }
            $result[] = $item;
        }
        
        return $result;
    }
} 