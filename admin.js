// 全局数据
let adminData = { users: {}, gameSettings: {}, giftCodes: [], mails: [], achievements: {}, logs: [] };

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.querySelector('.sidebar-overlay').classList.toggle('open');
}

// 初始化
window.initAdmin = function() {
    loadAdminData();
    initNavigation();
    renderDashboard();
    addLog('system', '登录后台管理系统');
};

// 加载数据
function loadAdminData() {
    const users = localStorage.getItem('farmGameUsers');
    const save = localStorage.getItem('farmGameSave');
    const settings = localStorage.getItem('farmGameSettings');
    const giftCodes = localStorage.getItem('farmGameGiftCodes');
    const mails = localStorage.getItem('farmGameMails');
    const logs = localStorage.getItem('farmGameLogs');

    if (users) adminData.users = JSON.parse(users);
    if (save) {
        const s = JSON.parse(save);
        if (s.currentUserId && adminData.users[s.currentUserId]) {
            // 有用户数据
        }
    }
    if (settings) adminData.gameSettings = JSON.parse(settings);
    if (giftCodes) adminData.giftCodes = JSON.parse(giftCodes);
    if (mails) adminData.mails = JSON.parse(mails);
    if (logs) adminData.logs = JSON.parse(logs);
}

// 保存数据
function saveAdminData() {
    localStorage.setItem('farmGameSettings', JSON.stringify(adminData.gameSettings));
    localStorage.setItem('farmGameGiftCodes', JSON.stringify(adminData.giftCodes));
    localStorage.setItem('farmGameMails', JSON.stringify(adminData.mails));
    localStorage.setItem('farmGameLogs', JSON.stringify(adminData.logs));
    localStorage.setItem('farmGameAchievements', JSON.stringify(adminData.achievements));
}

// 添加日志
function addLog(type, action, detail = '') {
    adminData.logs.unshift({
        time: Date.now(),
        type,
        action,
        detail,
        admin: 'admin',
    });
    if (adminData.logs.length > 1000) adminData.logs = adminData.logs.slice(0, 1000);
    localStorage.setItem('farmGameLogs', JSON.stringify(adminData.logs));
}

// 导航
function initNavigation() {
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const pageId = item.dataset.page;
            
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
            document.getElementById('page-' + pageId).classList.add('active');
            
            document.getElementById('breadcrumb').textContent = item.querySelector('span:last-child').textContent;
            
            // 页面渲染
            if (pageId === 'dashboard') renderDashboard();
            if (pageId === 'users') renderUsers();
            if (pageId === 'game-settings') renderGameSettings();
            if (pageId === 'items') renderItems();
            if (pageId === 'gift-codes') renderGiftCodes();
            if (pageId === 'mail') renderMail();
            if (pageId === 'achievements') renderAchievements();
            if (pageId === 'logs') renderLogs();
        });
    });
}

// 数据概览
function renderDashboard() {
    const users = adminData.users || {};
    const totalUsers = Object.keys(users).length;
    const totalCoins = Object.values(users).reduce((sum, u) => sum + (u.coins || 0), 0);
    const totalPlants = Object.values(users).reduce((sum, u) => sum + (u.stats?.totalPlant || 0), 0);
    const totalHarvests = Object.values(users).reduce((sum, u) => sum + (u.stats?.totalHarvest || 0), 0);

    document.getElementById('total-users').textContent = totalUsers;
    document.getElementById('total-coins').toLocaleString(totalCoins);
    document.getElementById('total-plants').textContent = totalPlants;
    document.getElementById('total-harvests').textContent = totalHarvests;

    // 等级分布
    renderLevelDist(users);
    
    // 成就统计
    renderAchievementStats(users);
    
    // 最近活动
    renderRecentActivity();
}

function renderLevelDist(users) {
    const dist = {};
    Object.values(users).forEach(u => {
        const level = Math.floor((u.level || 1) / 5) * 5;
        const range = `${level + 1}-${level + 5}级`;
        dist[range] = (dist[range] || 0) + 1;
    });

    const container = document.getElementById('level-dist');
    container.innerHTML = '';
    
    const ranges = Object.keys(dist).sort();
    ranges.forEach(range => {
        const count = dist[range];
        const max = Math.max(...Object.values(dist));
        const width = (count / max) * 100;
        
        const bar = document.createElement('div');
        bar.className = 'level-bar';
        bar.innerHTML = `
            <div class="level-bar-fill" style="width: ${width}%"></div>
            <span class="level-bar-label">${range}<br>${count}人</span>
        `;
        container.appendChild(bar);
    });
}

function renderAchievementStats(users) {
    const stats = {};
    Object.values(users).forEach(u => {
        if (u.achievements) {
            Object.values(u.achievements).forEach(a => {
                if (a.unlocked) {
                    stats[a.id] = { count: (stats[a.id] || 0) + 1, emoji: a.emoji, name: a.name };
                }
            });
        }
    });

    const container = document.getElementById('achievement-stats');
    container.innerHTML = '';
    
    Object.entries(stats).forEach(([id, data]) => {
        const item = document.createElement('div');
        item.className = 'ach-stat-item';
        item.innerHTML = `
            <span class="ach-stat-emoji">${data.emoji}</span>
            <span class="ach-stat-count">${data.count}</span>
            <span class="ach-stat-name">${data.name}</span>
        `;
        container.appendChild(item);
    });
}

function renderRecentActivity() {
    const container = document.getElementById('recent-activity');
    container.innerHTML = '';
    
    const recentLogs = adminData.logs.slice(0, 20);
    recentLogs.forEach(log => {
        const time = new Date(log.time).toLocaleString();
        const item = document.createElement('div');
        item.className = 'activity-item';
        item.innerHTML = `
            <span>${log.action}</span>
            <span class="activity-time">${time}</span>
        `;
        container.appendChild(item);
    });
}

// 用户管理
function renderUsers() {
    const container = document.getElementById('user-list');
    container.innerHTML = '';
    
    const users = adminData.users || {};
    Object.entries(users).forEach(([userId, user]) => {
        const item = document.createElement('div');
        item.className = 'user-item';
        const displayName = user.displayName || user.username || '农场主';
        const shortId = userId.slice(-6).toUpperCase();
        item.innerHTML = `
            <div class="user-info">
                <span class="user-avatar">👤</span>
                <div class="user-details">
                    <span class="user-id">${displayName} <span style="color:#888;font-weight:normal;font-size:0.85em">(${shortId})</span></span>
                    <span class="user-stats">Lv.${user.level || 1} | 💰${user.coins || 0} | 收获：${user.harvestCount || 0}</span>
                </div>
            </div>
            <div class="user-actions">
                <button class="btn-sm btn-view" onclick="viewUser('${userId}')">查看</button>
                <button class="btn-sm btn-gift" onclick="giveGift('${userId}')">发奖</button>
                <button class="btn-sm btn-award" onclick="awardCoins('${userId}')">发金币</button>
            </div>
        `;
        container.appendChild(item);
    });
}

function searchUser() {
    const query = document.getElementById('user-search').value.toLowerCase();
    const items = document.querySelectorAll('.user-item');
    
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(query) ? 'flex' : 'none';
    });
}

function viewUser(userId) {
    const user = adminData.users[userId];
    if (!user) return;
    
    const displayName = user.displayName || user.username || '农场主';
    const shortId = userId.slice(-6).toUpperCase();
    
    const body = document.getElementById('user-detail-body');
    body.innerHTML = `
        <div class="user-detail-info">
            <p><strong>用户名：</strong> ${displayName}</p>
            <p><strong>用户 ID：</strong> ${shortId} <button class="btn-sm btn-view" onclick="navigator.clipboard.writeText('${userId}');alert('ID 已复制')">复制</button></p>
            <p><strong>等级：</strong> ${user.level || 1}</p>
            <p><strong>经验：</strong> ${user.exp || 0}</p>
            <p><strong>金币：</strong> ${user.coins || 0}</p>
            <p><strong>收获次数：</strong> ${user.harvestCount || 0}</p>
            <p><strong>种植次数：</strong> ${user.stats?.totalPlant || 0}</p>
            <p><strong>总收益：</strong> ${user.stats?.totalEarn || 0}</p>
            <p><strong>宠物：</strong> ${user.pet?.name || '无'}</p>
            <p><strong>成就解锁：</strong> ${Object.values(user.achievements || {}).filter(a => a.unlocked).length} / ${Object.keys(user.achievements || {}).length}</p>
        </div>
        <h3 style="margin: 20px 0 10px;">背包物品</h3>
        <div class="items-grid">
            ${Object.entries(user.inventory || {}).map(([id, count]) => `
                <div class="item-card">
                    <span class="item-emoji">🌱</span>
                    <span class="item-name">${id}</span>
                    <span class="item-price">x${count}</span>
                </div>
            `).join('')}
        </div>
    `;
    
    document.getElementById('user-detail-modal').classList.add('show');
    addLog('user', `查看用户详情 "${displayName}"`);
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

function giveGift(userId) {
    alert('发放礼包功能开发中...');
}

function awardCoins(userId) {
    const amount = prompt('输入金币数量:');
    if (!amount || isNaN(amount)) return;
    
    const user = adminData.users[userId];
    if (!user) return;
    
    user.coins = (user.coins || 0) + parseInt(amount);
    user.coinLog = user.coinLog || [];
    user.coinLog.push({ time: Date.now(), desc: '管理员奖励', coins: parseInt(amount), type: 'positive' });
    
    localStorage.setItem('farmGameUsers', JSON.stringify(adminData.users));
    addLog('user', `给用户 "${userId}" 发放 ${amount} 金币`);
    alert(`已发放 ${amount} 金币`);
    renderUsers();
}

function deleteUser(userId) {
    if (!confirm(`确定删除用户 "${userId}"？此操作不可恢复！`)) return;
    
    delete adminData.users[userId];
    localStorage.setItem('farmGameUsers', JSON.stringify(adminData.users));
    addLog('system', `删除用户 "${userId}"`);
    alert('已删除');
    renderUsers();
}

// 游戏设置
function renderGameSettings() {
    const settings = adminData.gameSettings;
    document.getElementById('growth-speed').value = settings.growthSpeed;
    document.getElementById('growth-speed-value').textContent = settings.growthSpeed + 'x';
    document.getElementById('wither-time').value = settings.witherTime;
    document.getElementById('coin-rate').value = settings.coinRate;
    document.getElementById('coin-rate-value').textContent = settings.coinRate + 'x';
    document.getElementById('exp-rate').value = settings.expRate;
    document.getElementById('exp-rate-value').textContent = settings.expRate + 'x';
    document.getElementById('double-coin-event').checked = settings.doubleCoinEvent;
    document.getElementById('double-exp-event').checked = settings.doubleExpEvent;
    if (settings.eventEndTime) {
        document.getElementById('event-end-time').value = new Date(settings.eventEndTime).toISOString().slice(0, 16);
    }
}

document.getElementById('growth-speed').addEventListener('input', (e) => {
    document.getElementById('growth-speed-value').textContent = e.target.value + 'x';
});

document.getElementById('coin-rate').addEventListener('input', (e) => {
    document.getElementById('coin-rate-value').textContent = e.target.value + 'x';
});

document.getElementById('exp-rate').addEventListener('input', (e) => {
    document.getElementById('exp-rate-value').textContent = e.target.value + 'x';
});

function saveGameSettings() {
    adminData.gameSettings.growthSpeed = parseFloat(document.getElementById('growth-speed').value);
    adminData.gameSettings.witherTime = parseInt(document.getElementById('wither-time').value);
    adminData.gameSettings.coinRate = parseFloat(document.getElementById('coin-rate').value);
    adminData.gameSettings.expRate = parseFloat(document.getElementById('exp-rate').value);
    
    saveAdminData();
    addLog('system', '保存游戏设置');
    alert('设置已保存');
}

function saveEventSettings() {
    adminData.gameSettings.doubleCoinEvent = document.getElementById('double-coin-event').checked;
    adminData.gameSettings.doubleExpEvent = document.getElementById('double-exp-event').checked;
    const endTime = document.getElementById('event-end-time').value;
    if (endTime) {
        adminData.gameSettings.eventEndTime = new Date(endTime).getTime();
    }
    
    saveAdminData();
    addLog('system', '保存活动设置');
    alert('活动设置已保存');
}

// 道具管理
function renderItems() {
    const seedList = document.getElementById('seed-list');
    const select = document.getElementById('give-item-id');
    seedList.innerHTML = '';
    select.innerHTML = '';
    
    const CROPS = {
        radish: { name: '萝卜', emoji: '🌱', buyPrice: 5, sellPrice: 12 },
        cabbage: { name: '白菜', emoji: '🌰', buyPrice: 10, sellPrice: 25 },
        tomato: { name: '番茄', emoji: '🍅', buyPrice: 15, sellPrice: 38 },
        corn: { name: '玉米', emoji: '🌽', buyPrice: 25, sellPrice: 60 },
        eggplant: { name: '茄子', emoji: '🍆', buyPrice: 35, sellPrice: 85 },
        strawberry: { name: '草莓', emoji: '🌸', buyPrice: 50, sellPrice: 125 },
        peach: { name: '桃子', emoji: '🌺', buyPrice: 70, sellPrice: 175 },
        cherry: { name: '樱桃', emoji: '🌷', buyPrice: 100, sellPrice: 260 },
        watermelon: { name: '西瓜', emoji: '🍉', buyPrice: 150, sellPrice: 400 },
        grape: { name: '葡萄', emoji: '🍇', buyPrice: 250, sellPrice: 680 },
    };
    
    Object.entries(CROPS).forEach(([id, crop]) => {
        const card = document.createElement('div');
        card.className = 'item-card';
        card.innerHTML = `
            <span class="item-emoji">${crop.emoji}</span>
            <span class="item-name">${crop.name}</span>
            <span class="item-price">💰${crop.buyPrice}</span>
            <span class="item-profit">卖出：${crop.sellPrice}</span>
        `;
        seedList.appendChild(card);
        
        const opt = document.createElement('option');
        opt.value = id;
        opt.textContent = crop.name;
        select.appendChild(opt);
    });
}

function giveItem() {
    const targetType = document.getElementById('give-item-type').value;
    const targetUser = document.getElementById('give-item-user').value;
    const itemId = document.getElementById('give-item-id').value;
    const qty = parseInt(document.getElementById('give-item-qty').value);
    
    if (targetType === 'user' && targetUser) {
        const user = adminData.users[targetUser];
        if (user) {
            user.inventory = user.inventory || {};
            user.inventory[itemId] = (user.inventory[itemId] || 0) + qty;
            localStorage.setItem('farmGameUsers', JSON.stringify(adminData.users));
            addLog('item', `给用户 "${targetUser}" 发放 ${itemId} x${qty}`);
            alert(`已发放 ${itemId} x${qty} 给用户 ${targetUser}`);
        } else {
            alert('用户不存在');
        }
    } else if (targetType === 'all') {
        Object.values(adminData.users).forEach(user => {
            user.inventory = user.inventory || {};
            user.inventory[itemId] = (user.inventory[itemId] || 0) + qty;
        });
        localStorage.setItem('farmGameUsers', JSON.stringify(adminData.users));
        addLog('item', `全服发放 ${itemId} x${qty}`);
        alert(`已全服发放 ${itemId} x${qty}`);
    } else {
        alert('请输入用户 ID');
    }
}

// 礼包码
function renderGiftCodes() {
    const container = document.getElementById('gift-list');
    container.innerHTML = '';
    
    adminData.giftCodes.forEach((code, index) => {
        const item = document.createElement('div');
        item.className = 'gift-item';
        item.innerHTML = `
            <span class="gift-code">${code.code}</span>
            <div class="gift-info">
                <div class="gift-reward">${code.rewardType}: ${code.rewardQty}</div>
                <div class="gift-limit">兑换：${code.used}/${code.limit}</div>
            </div>
            <div class="gift-actions">
                <button class="btn-sm btn-gift" onclick="copyGiftCode('${code.code}')">复制</button>
                <button class="btn-sm btn-ban" onclick="deleteGiftCode(${index})">删除</button>
            </div>
        `;
        container.appendChild(item);
    });
}

function createGiftCode() {
    const code = document.getElementById('gift-code').value || generateCode();
    const rewardType = document.getElementById('gift-reward-type').value;
    const rewardId = document.getElementById('gift-reward-id').value;
    const rewardQty = parseInt(document.getElementById('gift-reward-qty').value);
    const limit = parseInt(document.getElementById('gift-limit').value);
    
    adminData.giftCodes.push({
        code,
        rewardType,
        rewardId,
        rewardQty,
        limit,
        used: 0,
        created: Date.now(),
    });
    
    saveAdminData();
    addLog('system', `创建礼包码 ${code}`);
    alert(`礼包码创建成功：${code}`);
    renderGiftCodes();
}

function generateCode() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let code = '';
    for (let i = 0; i < 8; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return code;
}

function copyGiftCode(code) {
    navigator.clipboard.writeText(code);
    alert('已复制到剪贴板');
}

function deleteGiftCode(index) {
    adminData.giftCodes.splice(index, 1);
    saveAdminData();
    addLog('system', `删除礼包码`);
    renderGiftCodes();
}

// 邮件系统
function renderMail() {
    const container = document.getElementById('mail-history');
    container.innerHTML = '';
    
    adminData.mails.reverse().forEach(mail => {
        const item = document.createElement('div');
        item.className = 'mail-item';
        item.innerHTML = `
            <div class="mail-title">📨 ${mail.title} - ${mail.targetType === 'all' ? '全服' : '指定用户'}</div>
            <div class="mail-meta">${new Date(mail.time).toLocaleString()} | 奖励：${mail.rewardType === 'none' ? '无' : mail.rewardQty}</div>
        `;
        container.appendChild(item);
    });
}

function sendMail() {
    const targetType = document.getElementById('mail-target-type').value;
    const targetUser = document.getElementById('mail-target-user').value;
    const title = document.getElementById('mail-title').value;
    const content = document.getElementById('mail-content').value;
    const rewardType = document.getElementById('mail-reward-type').value;
    const rewardId = document.getElementById('mail-reward-id').value;
    const rewardQty = parseInt(document.getElementById('mail-reward-qty').value);
    
    if (!title || !content) {
        alert('请填写标题和内容');
        return;
    }
    
    adminData.mails.push({
        targetType,
        targetUser: targetType === 'user' ? targetUser : null,
        title,
        content,
        rewardType,
        rewardId,
        rewardQty,
        time: Date.now(),
    });
    
    // 发送给全服时，添加到所有用户的收件箱
    if (targetType === 'all') {
        Object.values(adminData.users).forEach(user => {
            user.mailbox = user.mailbox || [];
            user.mailbox.push({
                title,
                content,
                rewardType,
                rewardId,
                rewardQty,
                time: Date.now(),
                read: false,
            });
        });
        localStorage.setItem('farmGameUsers', JSON.stringify(adminData.users));
    }
    
    saveAdminData();
    addLog('system', `发送邮件 "${title}"`);
    alert('邮件已发送');
    renderMail();
}

// 成就管理
function renderAchievements() {
    const container = document.getElementById('achievements-list');
    container.innerHTML = '';
    
    // 预设成就
    const presetAchievements = {
        firstHarvest: { emoji: '🎉', name: '初次收获', desc: '首次收获作物' },
        level5: { emoji: '⭐', name: '农场新手', desc: '达到 5 级' },
        level10: { emoji: '🌟', name: '农场达人', desc: '达到 10 级' },
        richFarmer: { emoji: '💰', name: '富豪农场主', desc: '拥有 1000 金币' },
        harvest100: { emoji: '🏆', name: '丰收大师', desc: '累计收获 100 次' },
    };
    
    Object.entries(presetAchievements).forEach(([id, ach]) => {
        const card = document.createElement('div');
        card.className = 'ach-card';
        card.innerHTML = `
            <span class="ach-emoji">${ach.emoji}</span>
            <span class="ach-name">${ach.name}</span>
            <span class="ach-desc">${ach.desc}</span>
            <span class="ach-condition">${id}</span>
        `;
        container.appendChild(card);
    });
}

function createAchievement() {
    const id = document.getElementById('ach-id').value;
    const name = document.getElementById('ach-name').value;
    const desc = document.getElementById('ach-desc').value;
    const emoji = document.getElementById('ach-emoji').value;
    const condition = document.getElementById('ach-condition').value;
    
    if (!id || !name) {
        alert('请填写 ID 和名称');
        return;
    }
    
    adminData.achievements[id] = { id, name, desc, emoji, condition };
    saveAdminData();
    addLog('system', `创建成就 "${name}"`);
    alert('成就已创建');
    renderAchievements();
}

// 操作日志
function renderLogs() {
    const container = document.getElementById('logs-list');
    container.innerHTML = '';
    
    const typeFilter = document.getElementById('log-type-filter').value;
    const search = document.getElementById('log-search').value.toLowerCase();
    
    adminData.logs.forEach(log => {
        if (typeFilter !== 'all' && log.type !== typeFilter) return;
        if (search && !log.action.toLowerCase().includes(search)) return;
        
        const item = document.createElement('div');
        item.className = 'log-item';
        item.innerHTML = `
            <span class="log-time">${new Date(log.time).toLocaleString()}</span>
            <span class="log-admin">${log.admin}</span>
            <span class="log-action">${log.action}</span>
            <span class="log-type ${log.type}">${log.type}</span>
        `;
        container.appendChild(item);
    });
}

function clearLogs() {
    if (!confirm('确定清除所有日志？')) return;
    adminData.logs = [];
    localStorage.setItem('farmGameLogs', JSON.stringify(adminData.logs));
    addLog('system', '清除日志');
    renderLogs();
}

// 数据管理
function exportUserData() {
    const data = JSON.stringify(adminData.users, null, 2);
    downloadFile('farm-users.json', data);
    addLog('system', '导出用户数据');
}

function exportGameData() {
    const data = JSON.stringify(adminData.gameSettings, null, 2);
    downloadFile('farm-settings.json', data);
    addLog('system', '导出游戏配置');
}

function exportLogs() {
    const data = JSON.stringify(adminData.logs, null, 2);
    downloadFile('farm-logs.json', data);
    addLog('system', '导出日志');
}

function downloadFile(filename, content) {
    const blob = new Blob([content], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
}

function importData() {
    const file = document.getElementById('import-file').files[0];
    const type = document.getElementById('import-type').value;
    
    if (!file) {
        alert('请选择文件');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
        try {
            const data = JSON.parse(e.target.result);
            if (type === 'users') {
                adminData.users = data;
                localStorage.setItem('farmGameUsers', JSON.stringify(data));
            } else if (type === 'game') {
                adminData.gameSettings = data;
                saveAdminData();
            }
            addLog('system', `导入${type === 'users' ? '用户' : '游戏'}数据`);
            alert('导入成功');
        } catch (err) {
            alert('文件格式错误');
        }
    };
    reader.readAsText(file);
}

function cleanupInactiveUsers() {
    const thirtyDaysAgo = Date.now() - 30 * 24 * 60 * 60 * 1000;
    let count = 0;
    
    Object.keys(adminData.users).forEach(userId => {
        if (userId === 'default') return;
        if (adminData.users[userId].lastSaveTime < thirtyDaysAgo) {
            delete adminData.users[userId];
            count++;
        }
    });
    
    localStorage.setItem('farmGameUsers', JSON.stringify(adminData.users));
    addLog('system', `清理${count}个不活跃用户`);
    alert(`已清理${count}个 30 天未登录用户`);
}

function resetAllUsers() {
    if (!confirm('确定重置所有用户进度？此操作不可恢复！')) return;
    
    Object.keys(adminData.users).forEach(userId => {
        if (userId !== 'default') {
            delete adminData.users[userId];
        }
    });
    
    localStorage.setItem('farmGameUsers', JSON.stringify(adminData.users));
    addLog('system', '重置所有用户进度');
    alert('已重置');
}

function wipeAllData() {
    if (!confirm('⚠️ 警告：这将删除所有数据！不可恢复！\n\n确认要继续吗？')) return;
    if (!confirm('真的确定吗？')) return;
    
    localStorage.removeItem('farmGameUsers');
    localStorage.removeItem('farmGameSave');
    localStorage.removeItem('farmGameSettings');
    localStorage.removeItem('farmGameGiftCodes');
    localStorage.removeItem('farmGameMails');
    localStorage.removeItem('farmGameLogs');
    
    adminData = { users: {}, gameSettings: {}, giftCodes: [], mails: [], achievements: {}, logs: [] };
    addLog('system', '清空所有数据');
    alert('数据已清空，页面将刷新');
    location.reload();
}

function logout() {
    if (confirm('确定退出后台管理？')) {
        window.open('../', '_blank');
    }
}
