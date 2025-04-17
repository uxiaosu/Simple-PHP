<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 用户模型
 */

namespace SimplePHP\App\Models;

use SimplePHP\Core\Database\Model;
use SimplePHP\Core\Security\Security;

class UserModel extends Model
{
    /**
     * @var string 表名
     */
    protected $table = 'users';
    
    /**
     * @var array 可填充字段
     */
    protected $fillable = [
        'username', 
        'email', 
        'password', 
        'full_name',
        'status',
        'role',
        'created_at', 
        'updated_at'
    ];
    
    /**
     * @var array 隐藏字段
     */
    protected $hidden = ['password', 'remember_token'];
    
    /**
     * @var Security 安全组件实例
     */
    private $security;
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->security = new Security();
    }
    
    /**
     * 根据用户名查找用户
     *
     * @param string $username 用户名
     * @return array|null
     */
    public function findByUsername($username)
    {
        return $this->query()
            ->where('username', $username)
            ->first();
    }
    
    /**
     * 根据电子邮件查找用户
     *
     * @param string $email 电子邮件
     * @return array|null
     */
    public function findByEmail($email)
    {
        return $this->query()
            ->where('email', $email)
            ->first();
    }
    
    /**
     * 创建新用户
     *
     * @param array $data 用户数据
     * @return bool|int 成功返回用户ID，失败返回false
     */
    public function createUser(array $data)
    {
        // 检查用户名是否已存在
        if ($this->findByUsername($data['username'])) {
            return false;
        }
        
        // 检查电子邮件是否已存在
        if ($this->findByEmail($data['email'])) {
            return false;
        }
        
        // 验证密码强度
        $passwordValidation = $this->security->validatePassword($data['password']);
        if ($passwordValidation !== true) {
            return false;
        }
        
        // 哈希密码
        $data['password'] = $this->security->hashPassword($data['password']);
        
        // 设置默认角色和状态
        if (!isset($data['role'])) {
            $data['role'] = 'user';
        }
        
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }
        
        // 创建用户
        return $this->create($data);
    }
    
    /**
     * 验证用户登录
     *
     * @param string $username 用户名或电子邮件
     * @param string $password 密码
     * @return array|bool 成功返回用户数据，失败返回false
     */
    public function validateLogin($username, $password)
    {
        // 尝试按用户名查找
        $user = $this->findByUsername($username);
        
        // 如果找不到，尝试按电子邮件查找
        if (!$user) {
            $user = $this->findByEmail($username);
        }
        
        // 如果仍然找不到，或者用户状态不是活动的，返回false
        if (!$user || $user['status'] !== 'active') {
            return false;
        }
        
        // 验证密码
        if (!$this->security->verifyPassword($password, $user['password'])) {
            return false;
        }
        
        // 移除敏感字段
        unset($user['password']);
        unset($user['remember_token']);
        
        return $user;
    }
    
    /**
     * 更新用户密码
     *
     * @param int $userId 用户ID
     * @param string $password 新密码
     * @return bool|int 成功返回影响的行数，失败返回false
     */
    public function updatePassword($userId, $password)
    {
        // 验证密码强度
        $passwordValidation = $this->security->validatePassword($password);
        if ($passwordValidation !== true) {
            return false;
        }
        
        // 哈希密码
        $hashedPassword = $this->security->hashPassword($password);
        
        // 更新密码
        return $this->update($userId, [
            'password' => $hashedPassword,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * 更新用户状态
     *
     * @param int $userId 用户ID
     * @param string $status 新状态
     * @return bool|int 成功返回影响的行数，失败返回false
     */
    public function updateStatus($userId, $status)
    {
        if (!in_array($status, ['active', 'inactive', 'suspended', 'deleted'])) {
            return false;
        }
        
        return $this->update($userId, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * 获取活跃用户数量
     *
     * @return int
     */
    public function countActiveUsers()
    {
        return $this->query()
            ->where('status', 'active')
            ->count();
    }
    
    /**
     * 获取最近注册的用户
     *
     * @param int $limit 限制数量
     * @return array
     */
    public function getRecentUsers($limit = 10)
    {
        return $this->query()
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }
} 