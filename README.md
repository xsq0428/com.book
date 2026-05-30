# 二次元地址发布系统

基于 PHP + MySQL 的二次元风格网址导航发布页，包含完整的前台展示和后台管理功能。

## 功能特性

### 前台功能
- 二次元风格响应式页面设计
- 动态加载网址链接（主网址 + 备用网址）
- 广告推荐模块（支持一键复制）
- 群组链接跳转
- 永久地址展示
- 访问统计追踪

### 后台管理
- 管理员登录（Session 认证）
- 网址管理（增删改查、排序、启用/禁用）
- 广告管理（标题、内容、链接、复制文本）
- 公告管理（收藏提示、联系信息、自定义公告）
- 系统设置（站点标题、Logo、群组链接等）
- 访问统计（点击量、独立访客、访问日志）

## 技术栈

- **后端**: PHP 7.4+ (兼容 8.x)
- **数据库**: MySQL 5.6+
- **前端**: Bootstrap 5 + 原生 CSS/JS
- **架构**: MVC 模式简化版

## 安装步骤

### 1. 数据库配置

创建 MySQL 数据库并导入表结构：

```bash
mysql -u root -p < database.sql
```

或手动执行 `database.sql` 中的 SQL 语句。

### 2. 修改数据库配置

编辑 `config/database.php` 文件，修改数据库连接信息：

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');  // 修改为你的密码
define('DB_NAME', '二次元地址发布系统');
```

### 3. 配置 Web 服务器

#### Nginx 配置示例

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/project;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### Apache 配置示例

确保启用 `mod_rewrite`，项目根目录已有 `.htaccess` 文件。

### 4. 访问后台管理

访问 `http://your-domain/admin/` 使用默认账号登录：

- 用户名：`admin`
- 密码：`password`

**首次登录后请立即修改密码！**

## 项目结构

```
project/
├── admin/                  # 后台管理目录
│   ├── index.php          # 后台首页（数据统计）
│   ├── login.php          # 管理员登录
│   ├── logout.php         # 退出登录
│   ├── urls.php           # 网址管理
│   ├── ads.php            # 广告管理
│   ├── announcements.php  # 公告管理
│   ├── settings.php       # 系统设置
│   ├── stats.php          # 访问统计
│   └── layout.php         # 后台布局模板
├── api/
│   └── click.php          # 点击统计 API
├── config/
│   └── database.php       # 数据库配置
├── includes/
│   └── functions.php      # 公共函数
├── index.php              # 前台首页
├── database.sql           # 数据库脚本
└── README.md              # 本文件
```

## 数据库表说明

| 表名 | 说明 |
|------|------|
| `admins` | 管理员账号 |
| `urls` | 网址列表（主网址/备用网址） |
| `ads` | 广告内容 |
| `announcements` | 公告/提示/联系信息 |
| `settings` | 系统配置项 |
| `visit_logs` | 访问日志 |

## 安全建议

1. 修改默认管理员密码
2. 设置复杂数据库密码
3. 配置 HTTPS
4. 定期备份数据库
5. 限制 admin 目录访问 IP（可选）

## 自定义

### 修改默认管理员密码

```php
// 生成新密码哈希
echo password_hash('your_new_password', PASSWORD_DEFAULT);

// 在数据库中更新
UPDATE admins SET password = '新生成的哈希值' WHERE username = 'admin';
```

### 添加更多管理员

```sql
INSERT INTO admins (username, password, role) VALUES 
('newadmin', '$2y$10$...', 'admin');
```

## 常见问题

### Q: 数据库连接失败
A: 检查 `config/database.php` 中的配置是否正确，确保 MySQL 服务已启动。

### Q: 后台页面空白
A: 检查 PHP 错误日志，确认开启了必要 PHP 扩展（PDO、MySQL）。

### Q: 点击统计不生效
A: 确保前台页面能访问到 `api/click.php`，检查浏览器控制台是否有 JavaScript 错误。

##  License

MIT License

## 支持

如有问题请提交 Issue 或联系开发者。
