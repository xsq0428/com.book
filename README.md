# QQ 农场游戏 🌱

一个基于 HTML/CSS/JavaScript 的网页版农场游戏，包含完整的前台游戏界面和后台管理系统。

## 在线预览

- **游戏前台**: https://xsq0428.github.io/com.book/
- **管理后台**: https://xsq0428.github.io/com.book/admin.html

## 功能特性

### 🎮 游戏系统
- **种植系统**: 10 种作物（萝卜、白菜、番茄、玉米、茄子、草莓、桃子、樱桃、西瓜、葡萄）
- **土地升级**: 7 级土地系统，升级可提高收益
- **等级系统**: 最高 200 级，升级奖励金币
- **宠物系统**: 6 种宠物，等级解锁
- **好友系统**: 支持好友 ID 搜索和访问
- **商城系统**: 购买种子、装饰物品
- **背包系统**: 种子和果实分类显示
- **头像系统**: 20 种预设头像 + 自定义上传
- **音效系统**: Web Audio API 生成游戏音效
- **离线收益**: 每小时 20 金币离线奖励

### 🛠️ 后台管理
- 数据概览（用户数、活跃用户、金币总量等）
- 用户管理（搜索、查看、编辑、删除）
- 游戏设置（作物配置、等级配置）
- 礼包码系统（生成、激活）
- 邮件系统（发送全服邮件）
- 成就管理
- 操作日志
- 数据导入导出

## 本地运行

### 方法 1: 直接打开
直接双击 `index.html` 即可运行游戏。

### 方法 2: 使用本地服务器（推荐）

**使用 Python:**
```bash
# Python 3
python3 -m http.server 8000

# Python 2
python -m SimpleHTTPServer 8000
```

**使用 Node.js:**
```bash
npx serve .
# 或
npx http-server -p 8000
```

**使用 PHP:**
```bash
php -S localhost:8000
```

然后在浏览器访问 `http://localhost:8000/`

### 方法 3: 使用 VS Code Live Server
1. 安装 VS Code
2. 安装 "Live Server" 扩展
3. 右键 `index.html` 选择 "Open with Live Server"

## 项目结构

```
com.book/
├── index.html          # 游戏前台主页面
├── style.css           # 前台样式
├── game.js             # 游戏逻辑
├── admin.html          # 后台管理页面
├── admin.js            # 后台管理逻辑
├── style-admin.css     # 后台样式
└── README.md           # 说明文档
```

## 技术栈

- **纯前端实现**: HTML5 + CSS3 + JavaScript (ES6+)
- **无需框架**: 零依赖，开箱即用
- **本地存储**: 使用 localStorage 保存游戏数据
- **响应式设计**: 移动端优先，适配各种屏幕
- **深色主题**: #1a1a2e 背景 + #FFD700 金色强调色

## 游戏说明

### 种植流程
1. 在商城购买种子
2. 点击底部"种子"按钮打开背包
3. 选择种子后点击空地种植
4. 及时浇水和除草可提高收成
5. 作物成熟后点击收获

### 土地升级
- L2: 500 金币（5 级解锁）
- L3: 1500 金币（15 级解锁）
- L4: 5000 金币（30 级解锁）
- L5: 15000 金币（60 级解锁）
- L6: 50000 金币（100 级解锁）
- L7: 150000 金币（150 级解锁）

### 后台管理
- **账号**: `123456`
- **密码**: `123456`

## 数据存储

游戏数据存储在浏览器的 localStorage 中：
- `farmGameUsers`: 所有用户数据
- `farmGameCurrent`: 当前用户 ID
- `farmGameSettings`: 游戏设置
- `farmGameGiftCodes`: 礼包码
- `farmGameAudioSettings`: 音效设置

### 导出存档
1. 打开后台管理
2. 进入"数据管理"
3. 点击"导出数据"
4. 保存 JSON 文件

### 导入存档
1. 打开后台管理
2. 进入"数据管理"
3. 选择 JSON 文件
4. 点击"导入数据"

## 部署到 GitHub Pages

1. Fork 或克隆此仓库
2. 访问仓库 Settings → Pages
3. Source 选择 `main` 分支，文件夹选择 `/`
4. 保存后等待几分钟
5. 访问 `https://用户名.github.io/com.book/`

## 浏览器兼容性

- Chrome/Edge (推荐)
- Firefox
- Safari
- 移动端浏览器

## 许可证

MIT License

## 更新日志

### v1.0.0
- 初始版本发布
- 完整的农场游戏功能
- 后台管理系统
- 音效系统
- 移动端适配

---

**Enjoy Farming! 🚜**
