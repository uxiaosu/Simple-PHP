<!-- API文档页面 -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <!-- API 导航 -->
            <div class="card border-0 shadow-sm sticky-lg-top mb-4" style="top: 2rem; z-index: 100;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 fw-bold">API 文档</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="#introduction" class="list-group-item list-group-item-action">介绍</a>
                        <a href="#authentication" class="list-group-item list-group-item-action">身份验证</a>
                        <a href="#rate-limiting" class="list-group-item list-group-item-action">请求限制</a>
                        <a href="#error-handling" class="list-group-item list-group-item-action">错误处理</a>
                        <a href="#endpoints" class="list-group-item list-group-item-action">API 端点</a>
                        <a href="#users" class="list-group-item list-group-item-action ps-4">- 用户</a>
                        <a href="#posts" class="list-group-item list-group-item-action ps-4">- 文章</a>
                        <a href="#comments" class="list-group-item list-group-item-action ps-4">- 评论</a>
                        <a href="#files" class="list-group-item list-group-item-action ps-4">- 文件</a>
                        <a href="#examples" class="list-group-item list-group-item-action">代码示例</a>
                        <a href="#sdks" class="list-group-item list-group-item-action">SDKs & 客户端</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <!-- API 文档内容 -->
            <h1 class="display-5 fw-bold mb-4">SimplePHP API 文档</h1>
            <p class="lead">SimplePHP 提供强大的 RESTful API，使您能够以编程方式与应用程序的数据和功能进行交互。</p>
            
            <!-- 介绍部分 -->
            <section id="introduction" class="mt-5 mb-5">
                <h2 class="fw-bold border-bottom pb-2 mb-4">介绍</h2>
                <p>SimplePHP API 采用 RESTful 设计原则，通过标准 HTTP 方法提供数据访问和操作功能。所有 API 通信均使用 HTTPS 进行安全加密，并采用 JSON 作为数据交换格式。</p>
                
                <div class="card bg-light border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="fw-bold">基本 URL</h5>
                        <p class="mb-0">所有 API 请求的基本 URL 为：</p>
                        <div class="bg-dark text-white p-3 rounded mt-2 mb-2">
                            <code>https://api.simplephp.com/v2</code>
                        </div>
                        <p class="mb-0 small text-muted">注意：确保在请求中使用正确的版本号，当前稳定版本为 v2。</p>
                    </div>
                </div>
            </section>
            
            <!-- 身份验证部分 -->
            <section id="authentication" class="mb-5">
                <h2 class="fw-bold border-bottom pb-2 mb-4">身份验证</h2>
                <p>SimplePHP API 使用 Bearer Token 进行身份验证。所有的 API 请求都需要在 HTTP 头部包含有效的访问令牌。</p>
                
                <h5 class="fw-bold mt-4">获取访问令牌</h5>
                <p>要获取访问令牌，请向 <code>/auth/token</code> 端点发送 POST 请求，并提供您的凭据：</p>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <div class="d-flex">
                            <span class="badge bg-success me-2">POST</span>
                            <code>/auth/token</code>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold">请求体</h6>
<pre class="bg-light p-3 rounded"><code>{
  "client_id": "your_client_id",
  "client_secret": "your_client_secret",
  "grant_type": "client_credentials"
}</code></pre>

                        <h6 class="fw-bold mt-3">响应</h6>
<pre class="bg-light p-3 rounded"><code>{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "Bearer",
  "expires_in": 3600
}</code></pre>
                    </div>
                </div>
                
                <h5 class="fw-bold mt-4">使用访问令牌</h5>
                <p>获取访问令牌后，您需要在所有 API 请求的 Authorization 标头中包含它：</p>
                
<pre class="bg-light p-3 rounded"><code>Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...</code></pre>
            </section>
            
            <!-- 请求限制部分 -->
            <section id="rate-limiting" class="mb-5">
                <h2 class="fw-bold border-bottom pb-2 mb-4">请求限制</h2>
                <p>为了确保 API 的稳定性和可用性，SimplePHP 实施了请求限制。默认情况下，每个 API 密钥每小时可以发出 1000 个请求。</p>
                
                <div class="card bg-light border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <h5 class="fw-bold">限制头信息</h5>
                        <p>每个 API 响应都会包含以下标头，让您了解您的当前限制状态：</p>
                        <ul class="mb-0">
                            <li><code>X-RateLimit-Limit</code>: 在时间窗口内允许的最大请求数</li>
                            <li><code>X-RateLimit-Remaining</code>: 在当前时间窗口内剩余的请求数</li>
                            <li><code>X-RateLimit-Reset</code>: 当前时间窗口重置的时间（Unix 时间戳）</li>
                        </ul>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    如果您超过了请求限制，API 将返回 429 Too Many Requests 响应。请等待限制重置或联系我们升级您的计划。
                </div>
            </section>
            
            <!-- 错误处理部分 -->
            <section id="error-handling" class="mb-5">
                <h2 class="fw-bold border-bottom pb-2 mb-4">错误处理</h2>
                <p>当 API 请求失败时，SimplePHP 将返回适当的 HTTP 状态码和 JSON 格式的错误响应，其中包含详细的错误信息。</p>
                
                <div class="table-responsive">
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>状态码</th>
                                <th>描述</th>
                                <th>可能原因</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>400</code></td>
                                <td>错误请求</td>
                                <td>请求格式不正确或缺少必要参数</td>
                            </tr>
                            <tr>
                                <td><code>401</code></td>
                                <td>未授权</td>
                                <td>身份验证失败或令牌无效</td>
                            </tr>
                            <tr>
                                <td><code>403</code></td>
                                <td>禁止访问</td>
                                <td>您没有权限访问请求的资源</td>
                            </tr>
                            <tr>
                                <td><code>404</code></td>
                                <td>未找到</td>
                                <td>请求的资源不存在</td>
                            </tr>
                            <tr>
                                <td><code>422</code></td>
                                <td>无法处理</td>
                                <td>请求参数验证失败</td>
                            </tr>
                            <tr>
                                <td><code>429</code></td>
                                <td>请求过多</td>
                                <td>您已超过 API 请求限制</td>
                            </tr>
                            <tr>
                                <td><code>500</code></td>
                                <td>服务器错误</td>
                                <td>服务器内部错误</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <h5 class="fw-bold mt-4">错误响应示例</h5>
<pre class="bg-light p-3 rounded"><code>{
  "status": "error",
  "code": 401,
  "message": "身份验证失败",
  "errors": [
    "无效的访问令牌"
  ]
}</code></pre>
            </section>
            
            <!-- API 端点部分 -->
            <section id="endpoints" class="mb-5">
                <h2 class="fw-bold border-bottom pb-2 mb-4">API 端点</h2>
                <p>SimplePHP API 提供以下主要资源端点，每个端点都支持标准的 RESTful 操作。</p>
                
                <!-- 用户端点 -->
                <section id="users" class="mb-4">
                    <h3 class="fw-bold mt-4">用户 API</h3>
                    <p>用户 API 提供对用户资源的访问和管理功能。</p>
                    
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-dark text-white">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>/users</code>
                                <span class="ms-auto badge bg-secondary">获取用户列表</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">获取用户列表，支持分页和筛选。</p>
                            <h6 class="fw-bold">查询参数</h6>
                            <ul class="mb-3">
                                <li><code>page</code>: 页码 (默认: 1)</li>
                                <li><code>limit</code>: 每页记录数 (默认: 20, 最大: 100)</li>
                                <li><code>sort</code>: 排序字段 (默认: id)</li>
                                <li><code>order</code>: 排序方向 (asc 或 desc, 默认: asc)</li>
                                <li><code>search</code>: 搜索关键词</li>
                            </ul>
                            <h6 class="fw-bold">响应示例</h6>
<pre class="bg-light p-3 rounded"><code>{
  "status": "success",
  "data": {
    "users": [
      {
        "id": 1,
        "name": "张三",
        "email": "zhangsan@example.com",
        "created_at": "2023-01-15T08:30:00Z",
        "updated_at": "2023-06-20T14:25:30Z"
      },
      // ... 更多用户
    ],
    "pagination": {
      "total": 150,
      "count": 20,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 8,
      "links": {
        "next": "/api/v2/users?page=2"
      }
    }
  }
}</code></pre>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-dark text-white">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">POST</span>
                                <code>/users</code>
                                <span class="ms-auto badge bg-secondary">创建用户</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">创建新用户。</p>
                            <h6 class="fw-bold">请求体</h6>
<pre class="bg-light p-3 rounded"><code>{
  "name": "李四",
  "email": "lisi@example.com",
  "password": "secure_password",
  "role": "user"
}</code></pre>
                            <h6 class="fw-bold mt-3">响应示例</h6>
<pre class="bg-light p-3 rounded"><code>{
  "status": "success",
  "message": "用户创建成功",
  "data": {
    "user": {
      "id": 151,
      "name": "李四",
      "email": "lisi@example.com",
      "role": "user",
      "created_at": "2023-07-01T09:45:12Z",
      "updated_at": "2023-07-01T09:45:12Z"
    }
  }
}</code></pre>
                        </div>
                    </div>
                    
                    <!-- 其他用户端点可以类似添加 -->
                </section>
                
                <!-- 文章端点 -->
                <section id="posts" class="mb-4">
                    <h3 class="fw-bold mt-4">文章 API</h3>
                    <p>文章 API 提供对文章资源的访问和管理功能。</p>
                    
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-dark text-white">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>/posts</code>
                                <span class="ms-auto badge bg-secondary">获取文章列表</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">获取文章列表，支持分页、筛选和排序。</p>
                            <h6 class="fw-bold">查询参数</h6>
                            <ul class="mb-3">
                                <li><code>page</code>: 页码 (默认: 1)</li>
                                <li><code>limit</code>: 每页记录数 (默认: 10, 最大: 50)</li>
                                <li><code>category_id</code>: 按分类筛选</li>
                                <li><code>user_id</code>: 按用户筛选</li>
                                <li><code>status</code>: 按状态筛选 (published, draft)</li>
                                <li><code>sort</code>: 排序字段 (id, title, created_at)</li>
                                <li><code>order</code>: 排序方向 (asc, desc)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- 其他文章端点可以类似添加 -->
                </section>
                
                <!-- 评论端点 -->
                <section id="comments" class="mb-4">
                    <h3 class="fw-bold mt-4">评论 API</h3>
                    <p>评论 API 提供对文章评论的访问和管理功能。</p>
                    
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-dark text-white">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">GET</span>
                                <code>/posts/{post_id}/comments</code>
                                <span class="ms-auto badge bg-secondary">获取文章评论</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">获取指定文章的评论列表。</p>
                            <h6 class="fw-bold">路径参数</h6>
                            <ul class="mb-3">
                                <li><code>post_id</code>: 文章ID</li>
                            </ul>
                            <h6 class="fw-bold">查询参数</h6>
                            <ul class="mb-3">
                                <li><code>page</code>: 页码 (默认: 1)</li>
                                <li><code>limit</code>: 每页记录数 (默认: 20)</li>
                                <li><code>sort</code>: 排序方式 (newest, oldest, popular)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- 其他评论端点可以类似添加 -->
                </section>
                
                <!-- 文件端点 -->
                <section id="files" class="mb-4">
                    <h3 class="fw-bold mt-4">文件 API</h3>
                    <p>文件 API 提供文件上传和管理功能。</p>
                    
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-dark text-white">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">POST</span>
                                <code>/files/upload</code>
                                <span class="ms-auto badge bg-secondary">上传文件</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">上传文件到服务器。支持图片、文档和其他媒体文件。</p>
                            <h6 class="fw-bold">请求内容类型</h6>
                            <p><code>multipart/form-data</code></p>
                            <h6 class="fw-bold">表单参数</h6>
                            <ul class="mb-3">
                                <li><code>file</code>: 要上传的文件</li>
                                <li><code>type</code>: 文件类型 (image, document, video)</li>
                                <li><code>description</code>: 文件描述 (可选)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- 其他文件端点可以类似添加 -->
                </section>
            </section>
            
            <!-- 代码示例部分 -->
            <section id="examples" class="mb-5">
                <h2 class="fw-bold border-bottom pb-2 mb-4">代码示例</h2>
                <p>以下是使用不同编程语言调用 SimplePHP API 的示例。</p>
                
                <!-- JavaScript示例 -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">JavaScript (fetch)</h5>
                    </div>
                    <div class="card-body">
<pre class="bg-light p-3 rounded"><code>// 获取用户列表
async function getUsers() {
  const response = await fetch('https://api.simplephp.com/v2/users', {
    method: 'GET',
    headers: {
      'Authorization': 'Bearer YOUR_ACCESS_TOKEN',
      'Content-Type': 'application/json'
    }
  });
  
  const data = await response.json();
  return data;
}

// 创建新用户
async function createUser(userData) {
  const response = await fetch('https://api.simplephp.com/v2/users', {
    method: 'POST',
    headers: {
      'Authorization': 'Bearer YOUR_ACCESS_TOKEN',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(userData)
  });
  
  const data = await response.json();
  return data;
}</code></pre>
                    </div>
                </div>
                
                <!-- PHP示例 -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">PHP (cURL)</h5>
                    </div>
                    <div class="card-body">
<pre class="bg-light p-3 rounded"><code>// 获取用户列表
function getUsers() {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://api.simplephp.com/v2/users");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer YOUR_ACCESS_TOKEN',
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// 创建新用户
function createUser($userData) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://api.simplephp.com/v2/users");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer YOUR_ACCESS_TOKEN',
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}</code></pre>
                    </div>
                </div>
            </section>
            
            <!-- SDKs和客户端部分 -->
            <section id="sdks" class="mb-5">
                <h2 class="fw-bold border-bottom pb-2 mb-4">SDKs & 客户端</h2>
                <p>为了简化API集成，我们提供了多种编程语言的官方SDK。</p>
                
                <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fab fa-php fa-2x text-primary me-3"></i>
                                    <h5 class="mb-0 fw-bold">PHP SDK</h5>
                                </div>
                                <p class="card-text">官方PHP SDK，支持Composer安装和全部API功能。</p>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download me-2"></i> 下载
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-book me-2"></i> 文档
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fab fa-js fa-2x text-primary me-3"></i>
                                    <h5 class="mb-0 fw-bold">JavaScript SDK</h5>
                                </div>
                                <p class="card-text">适用于浏览器和Node.js的JavaScript SDK，支持Promise和异步/等待。</p>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download me-2"></i> 下载
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-book me-2"></i> 文档
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fab fa-python fa-2x text-primary me-3"></i>
                                    <h5 class="mb-0 fw-bold">Python SDK</h5>
                                </div>
                                <p class="card-text">Python SDK，支持同步和异步调用，适用于服务器端集成。</p>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download me-2"></i> 下载
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-book me-2"></i> 文档
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- API支持部分 -->
            <section class="bg-light p-4 rounded shadow-sm text-center">
                <h3 class="fw-bold mb-3">需要帮助？</h3>
                <p class="mb-4">如果您在使用API过程中遇到任何问题，或者有改进建议，请随时联系我们的开发团队。</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="mailto:api-support@simplephp.com" class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i> 联系API支持
                    </a>
                    <a href="https://github.com/simplephp/api/issues" class="btn btn-outline-primary">
                        <i class="fab fa-github me-2"></i> 报告问题
                    </a>
                </div>
            </section>
        </div>
    </div>
</div> 