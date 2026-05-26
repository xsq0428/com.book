// 游戏配置
const GAME_CONFIG = { initialCoins: 200, initialLevel: 1, maxLevel: 200, totalLandPlots: 16, witherTime: 7200000 };
const LAND_LEVELS = [
    { level: 1, name: '贫瘠土地', color: '#8B4513', upgradeCost: 0, unlockLevel: 1 },
    { level: 2, name: '普通土地', color: '#A0522D', upgradeCost: 500, unlockLevel: 5 },
    { level: 3, name: '肥沃土地', color: '#CD853F', upgradeCost: 1500, unlockLevel: 15 },
    { level: 4, name: '金色土地', color: '#DAA520', upgradeCost: 5000, unlockLevel: 30 },
    { level: 5, name: '翡翠土地', color: '#F0E68C', upgradeCost: 15000, unlockLevel: 60 },
    { level: 6, name: '钻石土地', color: '#98FB98', upgradeCost: 50000, unlockLevel: 100 },
    { level: 7, name: '传奇土地', color: '#00CED1', upgradeCost: 150000, unlockLevel: 150 },
];

const CROPS = {
    radish: { id: 'radish', name: '萝卜', emoji: '🥕', seedEmoji: '🌱', growthTime: 30000, buyPrice: 5, sellPrice: 12, exp: 3, minLevel: 1 },
    cabbage: { id: 'cabbage', name: '白菜', emoji: '🥬', seedEmoji: '🌰', growthTime: 60000, buyPrice: 10, sellPrice: 25, exp: 6, minLevel: 1 },
    tomato: { id: 'tomato', name: '番茄', emoji: '🍅', seedEmoji: '🍅', growthTime: 90000, buyPrice: 15, sellPrice: 38, exp: 10, minLevel: 2 },
    corn: { id: 'corn', name: '玉米', emoji: '🌽', seedEmoji: '🌽', growthTime: 120000, buyPrice: 25, sellPrice: 60, exp: 15, minLevel: 3 },
    eggplant: { id: 'eggplant', name: '茄子', emoji: '🍆', seedEmoji: '🍆', growthTime: 180000, buyPrice: 35, sellPrice: 85, exp: 22, minLevel: 4 },
    strawberry: { id: 'strawberry', name: '草莓', emoji: '🍓', seedEmoji: '🌸', growthTime: 240000, buyPrice: 50, sellPrice: 125, exp: 32, minLevel: 5 },
    peach: { id: 'peach', name: '桃子', emoji: '🍑', seedEmoji: '🌺', growthTime: 300000, buyPrice: 70, sellPrice: 175, exp: 45, minLevel: 7 },
    cherry: { id: 'cherry', name: '樱桃', emoji: '🍒', seedEmoji: '🌷', growthTime: 420000, buyPrice: 100, sellPrice: 260, exp: 65, minLevel: 10 },
    watermelon: { id: 'watermelon', name: '西瓜', emoji: '🍉', seedEmoji: '🍉', growthTime: 600000, buyPrice: 150, sellPrice: 400, exp: 100, minLevel: 15 },
    grape: { id: 'grape', name: '葡萄', emoji: '🍇', seedEmoji: '🍇', growthTime: 900000, buyPrice: 250, sellPrice: 680, exp: 180, minLevel: 20 },
};

const PETS = [
    { id: 'dog', name: '小狗', emoji: '🐶', unlockLevel: 1 },
    { id: 'cat', name: '小猫', emoji: '🐱', unlockLevel: 3 },
    { id: 'rabbit', name: '兔子', emoji: '🐰', unlockLevel: 5 },
    { id: 'fox', name: '狐狸', emoji: '🦊', unlockLevel: 8 },
    { id: 'bear', name: '小熊', emoji: '🐻', unlockLevel: 12 },
    { id: 'panda', name: '熊猫', emoji: '🐼', unlockLevel: 18 },
];

const GROWTH_STAGES = ['播种', '发芽', '生长', '开花', '成熟'];
const ACHIEVEMENTS = {
    firstHarvest: { id: 'firstHarvest', name: '初次收获', description: '首次收获作物', emoji: '🎉', unlocked: false },
    level10: { id: 'level10', name: '农场新手', description: '达到 10 级', emoji: '⭐', unlocked: false },
    level50: { id: 'level50', name: '农场达人', description: '达到 50 级', emoji: '🌟', unlocked: false },
    level100: { id: 'level100', name: '农场大师', description: '达到 100 级', emoji: '👑', unlocked: false },
    level200: { id: 'level200', name: '农场传奇', description: '达到 200 级', emoji: '🏆', unlocked: false },
    richFarmer: { id: 'richFarmer', name: '富豪农场主', description: '拥有 10000 金币', emoji: '💰', unlocked: false },
    harvest100: { id: 'harvest100', name: '丰收大师', description: '累计收获 100 次', emoji: '🌾', unlocked: false },
};

const AVATARS = ['👤', '👨', '👩', '🧑', '👴', '👵', '🧔', '👱', '👮', '👷', '👲', '👳', '🤠', '🤡', '👹', '👺', '👻', '👽', '🤖', '💀'];
const DECORATIONS = [
    { id: 'scarecrow', name: '稻草人', emoji: '🌾', price: 100, effect: 'prevent_weed' },
    { id: 'fountain', name: '喷泉', emoji: '⛲', price: 500, effect: 'speed_growth' },
    { id: 'lamp', name: '路灯', emoji: '🏮', price: 200, effect: 'night_mode' },
];

let gameState = {
    currentUserId: null,
    users: {},
    coins: 0,
    level: 0,
    exp: 0,
    inventory: {},
    landPlots: [],
    harvestLog: [],
    coinLog: [],
    harvestCount: 0,
    achievements: {},
    stats: {},
    pet: {},
    avatar: '👤',
    friends: [],
    decorations: {},
    lastLoginTime: Date.now(),
    offlineEarnings: 0,
};
let selectedSeed = null, buyCropId = null, buyQuantity = 1, sellCropId = null, sellQuantity = 0;

function initGame() {
    loadUsers();
    loadOrCreateUser();
    initAudio();
    checkOfflineEarnings();
    renderGame();
    startGameLoop();
    renderPet();
}

function loadUsers() { const saved = localStorage.getItem('farmGameUsers'); if (saved) try { gameState.users = JSON.parse(saved); } catch (e) { gameState.users = {}; } }
function saveUsers() { localStorage.setItem('farmGameUsers', JSON.stringify(gameState.users)); }

function loadOrCreateUser() {
    const saved = localStorage.getItem('farmGameCurrent');
    if (saved) try {
        const data = JSON.parse(saved);
        if (data.userId && gameState.users[data.userId]) {
            gameState.currentUserId = data.userId;
            loadUserData(); renderUserBar(); return;
        }
    } catch (e) {}
    const defaultId = 'user_' + Date.now();
    gameState.currentUserId = defaultId;
    gameState.users[defaultId] = createDefaultData();
    loadUserData(); saveUsers(); saveCurrent(); renderUserBar();
}

function createDefaultData() {
    return {
        username: '农场主', displayName: '农场主', coins: GAME_CONFIG.initialCoins, level: GAME_CONFIG.initialLevel, exp: 0,
        inventory: { radish: 5, cabbage: 3 },
        landPlots: Array.from({ length: GAME_CONFIG.totalLandPlots }, (_, i) => createEmptyPlot(i)),
        harvestLog: [], coinLog: [], harvestCount: 0, achievements: { ...ACHIEVEMENTS },
        stats: { totalPlant: 0, totalHarvest: 0, totalEarn: 0, totalTimePlayed: 0, startTime: Date.now() },
        pet: { id: 'dog', name: '小狗', heart: 3, lastInteract: Date.now() },
        avatar: '👤',
        friends: [],
        decorations: {},
        lastLoginTime: Date.now(),
        offlineEarnings: 0,
    };
}

function loadUserData() { const userData = gameState.users[gameState.currentUserId]; if (userData) Object.assign(gameState, userData); }
function saveCurrent() { localStorage.setItem('farmGameCurrent', JSON.stringify({ userId: gameState.currentUserId })); }

function saveCurrentUser() {
    const userData = {
        username: gameState.username,
        displayName: gameState.displayName,
        coins: gameState.coins,
        level: gameState.level,
        exp: gameState.exp,
        inventory: gameState.inventory,
        landPlots: gameState.landPlots,
        harvestLog: gameState.harvestLog,
        coinLog: gameState.coinLog,
        harvestCount: gameState.harvestCount,
        achievements: gameState.achievements,
        stats: gameState.stats,
        pet: gameState.pet,
        avatar: gameState.avatar,
        friends: gameState.friends,
        decorations: gameState.decorations,
        lastLoginTime: gameState.lastLoginTime,
        offlineEarnings: gameState.offlineEarnings,
    };
    gameState.users[gameState.currentUserId] = userData;
    saveUsers();
    saveCurrent();
}

function saveGame() { saveCurrentUser(); }

function createEmptyPlot(index) {
    return { index, cropId: null, growthTime: 0, growthStage: 0, isWatered: false, hasWeed: false, hasEvent: null, plantedTime: null, withered: false, landLevel: 1 };
}

function renderGame() {
    renderHeader();
    renderUserBar();
    renderFarmGrid();
    renderInventory();
    renderSeedShop();
    renderStats();
    renderAchievements();
    renderPet();
}

function getExpForLevel(level) { return level * level * 50; }

function renderHeader() {
    document.getElementById('coin-count').textContent = gameState.coins;
    document.getElementById('player-level').textContent = 'Lv.' + gameState.level;
    document.getElementById('player-name').textContent = gameState.displayName || '农场主';
    document.getElementById('player-avatar').textContent = gameState.avatar || '👤';
    const expNeeded = getExpForLevel(gameState.level);
    const expPercent = Math.min((gameState.exp / expNeeded) * 100, 100);
    document.getElementById('exp-bar-fill').style.width = expPercent + '%';
    document.getElementById('exp-text').textContent = gameState.exp + '/' + expNeeded;
}

function renderUserBar() {
    const bar = document.getElementById('user-bar');
    if (bar) {
        document.getElementById('user-id').textContent = gameState.currentUserId.slice(-6).toUpperCase();
    }
}

function copyUserId() { navigator.clipboard.writeText(gameState.currentUserId); showFloatingMessage('ID 已复制', 'success'); }

function renameUser() {
    const newName = prompt('输入新用户名 (3-12 字符):', gameState.username || '');
    if (!newName || newName.length < 3 || newName.length > 12) { showFloatingMessage('用户名 3-12 个字符', 'info'); return; }
    gameState.username = newName; gameState.displayName = newName;
    saveCurrentUser(); renderUserBar();
    showFloatingMessage('改名成功', 'success');
}

function createUser() {
    const input = document.getElementById('new-username');
    const username = input.value.trim().slice(0, 12);
    if (!username || username.length < 3) { showFloatingMessage('用户名 3-12 个字符', 'info'); return; }
    const userId = 'user_' + Date.now();
    const userData = createDefaultData();
    userData.username = username; userData.displayName = username;
    userData.coins += 500; userData.coinLog = [{ time: Date.now(), desc: '新用户奖励', coins: 500, type: 'positive' }];
    gameState.users[userId] = userData;
    input.value = ''; switchToUser(userId);
    showFloatingMessage('创建成功', 'success');
}

function switchToUser(userId) {
    saveCurrentUser();
    gameState.currentUserId = userId;
    loadUserData();
    renderUserBar(); renderGame(); renderPet();
    saveCurrent();
}

function renderUserList() {
    // User list panel removed in new UI
}

function renderFarmGrid() {
    const grid = document.querySelector('.farm-grid'); if (!grid) return;
    grid.innerHTML = '';
    gameState.landPlots.forEach((plot, index) => {
        const plotEl = createLandPlotElement(plot, index);
        grid.appendChild(plotEl);
    });
}

function createLandPlotElement(plot, index) {
    const plotEl = document.createElement('div');
    const landLevel = Math.min(plot.landLevel || 1, 7);
    plotEl.className = `land-plot level-${landLevel}`;
    const handler = () => handleLandClick(index);
    plotEl.addEventListener('click', handler);
    plotEl.addEventListener('touchstart', (e) => { e.preventDefault(); handler(); });
    
    if (plot.withered) {
        plotEl.classList.add('withered');
        plotEl.innerHTML = `<span class="crop-emoji">🥀</span><span class="land-level">L${landLevel}</span><div class="growth-stage">枯萎</div><button class="quick-action clear" data-index="${index}">清理</button>`;
    } else if (plot.cropId) {
        const crop = CROPS[plot.cropId];
        plotEl.classList.add('planted');
        if (plot.growthStage >= GROWTH_STAGES.length - 1) plotEl.classList.add('ready');
        plotEl.innerHTML = `<span class="water-indicator">${plot.isWatered?'💧':''}</span><span class="land-level">L${landLevel}</span><span class="crop-emoji">${getGrowthEmoji(crop, plot.growthStage)}</span><span class="growth-stage">${GROWTH_STAGES[plot.growthStage]}</span><div class="progress-bar"><div class="progress-fill" style="width:${Math.min(plot.growthTime/crop.growthTime*100,100)}%"></div></div>${plot.hasWeed?'<span class="weed-icon">🌿</span>':''}${plot.hasEvent?`<span class="event-icon">${plot.hasEvent}</span>`:''}${plot.growthStage>=GROWTH_STAGES.length-1?`<button class="quick-action harvest" data-index="${index}">收获</button>`:''}`;
    } else {
        plotEl.classList.add('empty');
        const nextLevel = landLevel + 1;
        const canUpgrade = nextLevel <= 7 && gameState.level >= LAND_LEVELS[nextLevel-1].unlockLevel;
        const upgradeCost = nextLevel <= 7 ? LAND_LEVELS[nextLevel-1].upgradeCost : 0;
        plotEl.innerHTML = `<span class="land-level">L${landLevel}</span><span class="crop-emoji">🟫</span><span class="plot-status">${LAND_LEVELS[landLevel-1].name}</span>${canUpgrade && gameState.coins >= upgradeCost?`<button class="quick-action upgrade" data-index="${index}" data-level="${nextLevel}">升级 L${nextLevel}</button>`:''}`;
    }
    
    plotEl.querySelectorAll('.quick-action').forEach(btn => {
        const action = () => {
            const idx = parseInt(btn.dataset.index);
            if (btn.classList.contains('harvest')) harvestCrop(idx);
            if (btn.classList.contains('clear')) clearWitheredPlot(idx);
            if (btn.classList.contains('plant') && selectedSeed) plantCrop(idx, selectedSeed);
            if (btn.classList.contains('upgrade')) upgradeLand(idx, parseInt(btn.dataset.level));
        };
        btn.addEventListener('click', (e) => { e.stopPropagation(); action(); });
        btn.addEventListener('touchstart', (e) => { e.stopPropagation(); e.preventDefault(); action(); });
    });
    return plotEl;
}

function upgradeLand(index, targetLevel) {
    const plot = gameState.landPlots[index];
    const landLevel = plot.landLevel || 1;
    if (targetLevel > 7 || targetLevel !== landLevel + 1) return;
    const cost = LAND_LEVELS[targetLevel - 1].upgradeCost;
    if (gameState.coins < cost) { showFloatingMessage('金币不足', 'info'); return; }
    if (gameState.level < LAND_LEVELS[targetLevel - 1].unlockLevel) { showFloatingMessage('等级不足', 'info'); return; }
    gameState.coins -= cost;
    plot.landLevel = targetLevel;
    addCoinLog(`升级土地到 L${targetLevel}`, -cost);
    showFloatingMessage(`土地升级到 L${targetLevel}!`, 'success');
    saveGame(); renderGame();
}

function getGrowthEmoji(crop, stage) { return [crop.seedEmoji, '🌱', '🌿', '🌸', crop.emoji][stage] || crop.emoji; }

function handleLandClick(index) {
    const plot = gameState.landPlots[index];
    if (!plot.cropId) {
        if (selectedSeed && gameState.inventory[selectedSeed] > 0) plantCrop(index, selectedSeed);
        else updateActionTip('请先从背包中选择种子');
    } else if (plot.withered) clearWitheredPlot(index);
    else if (plot.growthStage >= GROWTH_STAGES.length - 1) harvestCrop(index);
    else showPlotActions(index);
}

function selectSeed(cropId) {
    if (gameState.level < CROPS[cropId].minLevel) {
        showFloatingMessage(`需要 Lv.${CROPS[cropId].minLevel}`, 'info');
        return;
    }
    selectedSeed = selectedSeed === cropId ? null : cropId;
    renderSeedInventory();
    updateActionTip(selectedSeed ? `已选择 ${CROPS[cropId].name} 种子，点击土地种植` : '点击土地进行操作');
}

function renderSeedInventory() {
    const el = document.getElementById('seed-inventory');
    if (!el) return;
    el.innerHTML = '';
    const seeds = Object.entries(gameState.inventory).filter(([cropId, count]) => count > 0 && CROPS[cropId]);
    if (!seeds.length) {
        el.innerHTML = '<p class="empty-msg">暂无种子</p>';
        return;
    }
    seeds.forEach(([cropId, count]) => {
        const crop = CROPS[cropId];
        const itemEl = document.createElement('div');
        itemEl.className = 'inventory-item seed-item' + (selectedSeed === cropId ? ' selected' : '');
        itemEl.style.opacity = gameState.level >= crop.minLevel ? '1' : '0.5';
        itemEl.innerHTML = `
            <span class="emoji">${crop.seedEmoji}</span>
            <span class="count">${crop.name}</span>
            <span class="quantity">x${count}</span>
        `;
        const handler = () => selectSeed(cropId);
        itemEl.addEventListener('click', handler);
        itemEl.addEventListener('touchstart', (e) => { e.preventDefault(); handler(); });
        el.appendChild(itemEl);
    });
}

function renderCropInventory() {
    const el = document.getElementById('crop-inventory');
    if (!el) return;
    el.innerHTML = '';
    const crops = Object.entries(gameState.inventory).filter(([cropId, count]) => count > 0 && CROPS[cropId]);
    if (!crops.length) {
        el.innerHTML = '<p class="empty-msg">暂无果实</p>';
        return;
    }
    crops.forEach(([cropId, count]) => {
        const crop = CROPS[cropId];
        const itemEl = document.createElement('div');
        itemEl.className = 'inventory-item crop-item';
        itemEl.style.position = 'relative';
        itemEl.innerHTML = `
            <span class="emoji">${crop.emoji}</span>
            <span class="count" style="color:#32CD32;">${crop.name}</span>
            <span class="quantity">x${count}</span>
            <button class="sell-btn" onclick="showSellModal('${cropId}')" style="position:absolute;bottom:-5px;right:-5px;width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,#FFD700 0%,#FFA500 100%);border:1px solid #FFF;font-size:0.6em;cursor:pointer;" title="出售">💰</button>
        `;
        el.appendChild(itemEl);
    });
}

function renderInventory() {
    renderSeedInventory();
    renderCropInventory();
}

function renderSeedShop() {
    const el = document.getElementById('shop-area'); if (!el) return;
    el.innerHTML = '';
    Object.values(CROPS).forEach(crop => {
        const canBuy = gameState.level >= crop.minLevel;
        const seedEl = document.createElement('div');
        seedEl.className = 'seed-item';
        seedEl.style.opacity = canBuy ? '1' : '0.5';
        seedEl.innerHTML = `<span class="seed-emoji">${crop.seedEmoji}</span><span class="seed-name">${crop.name}</span><span class="seed-price">💰${crop.buyPrice}</span><span class="seed-profit">+${(((crop.sellPrice-crop.buyPrice)/crop.buyPrice)*100).toFixed(0)}%</span>`;
        if (!canBuy) seedEl.innerHTML += `<span class="level-lock">Lv.${crop.minLevel}</span>`;
        const handler = () => { if (canBuy) openBuyModal(crop.id); };
        seedEl.addEventListener('click', handler);
        seedEl.addEventListener('touchstart', (e) => { e.preventDefault(); handler(); });
        el.appendChild(seedEl);
    });
}

function openBuyModal(cropId) {
    buyCropId = cropId; buyQuantity = 1;
    const crop = CROPS[cropId];
    document.getElementById('buy-modal-title').textContent = '确认购买';
    document.getElementById('buy-modal-body').innerHTML = `<div class="buy-modal-info"><span class="seed-emoji">${crop.seedEmoji}</span><span class="seed-name">${crop.name} 种子</span><div class="buy-price-info"><span>单价：${crop.buyPrice} 金币</span><span>卖出：${crop.sellPrice} 金币</span></div></div>`;
    updateBuyTotal(); document.getElementById('buy-qty').textContent = buyQuantity;
    document.getElementById('buy-modal').classList.add('show');
}

function closeBuyModal() { document.getElementById('buy-modal').classList.remove('show'); buyCropId = null; }
function adjustQty(delta) { buyQuantity = Math.max(1, Math.min(99, buyQuantity + delta)); document.getElementById('buy-qty').textContent = buyQuantity; updateBuyTotal(); }
function updateBuyTotal() { document.getElementById('buy-total').textContent = CROPS[buyCropId].buyPrice * buyQuantity; }

function confirmBuy() {
    const crop = CROPS[buyCropId];
    const total = crop.buyPrice * buyQuantity;
    if (gameState.coins >= total) {
        gameState.coins -= total;
        gameState.inventory[buyCropId] = (gameState.inventory[buyCropId] || 0) + buyQuantity;
        addCoinLog(`购买${crop.name}x${buyQuantity}`, -total);
        closeBuyModal(); saveGame(); renderGame();
        showFloatingMessage(`购买 ${crop.name} x${buyQuantity}`, 'success');
        playSound('coin');
    } else showFloatingMessage('金币不足', 'info');
}

function renderHarvestLog() {
    const el = document.getElementById('harvest-log');
    if (!el) return;
    if (!gameState.harvestLog.length) {
        el.innerHTML = '<p class="empty-msg">暂无收获</p>';
        return;
    }
    el.innerHTML = '';
    gameState.harvestLog.slice(-15).reverse().forEach(log => {
        const entryEl = document.createElement('div');
        entryEl.className = 'harvest-entry';
        const date = new Date(log.time);
        const timeStr = date.toLocaleString('zh-CN', { month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
        entryEl.innerHTML = `
            <div><span class="crop">${log.cropName}</span> x${log.count}</div>
            <div class="entry-meta">
                <span class="time">🕐 ${timeStr}</span>
                <span class="coins">+${log.coins}</span>
                <span class="exp">+${log.exp}</span>
            </div>
        `;
        el.appendChild(entryEl);
    });
}

function renderStats() {
    const el = document.getElementById('stats-panel'); if (!el) return;
    el.innerHTML = `<div class="stat-item"><span class="stat-label">种植</span><span class="stat-value">${gameState.stats.totalPlant}</span></div><div class="stat-item"><span class="stat-label">收获</span><span class="stat-value">${gameState.stats.totalHarvest}</span></div><div class="stat-item"><span class="stat-label">收益</span><span class="stat-value">${gameState.stats.totalEarn}</span></div><div class="stat-item"><span class="stat-label">时长</span><span class="stat-value">${formatTime(gameState.stats.totalTimePlayed)}</span></div>`;
}

function renderAchievements() {
    const el = document.getElementById('achievements-panel'); if (!el) return;
    el.innerHTML = '';
    Object.values(gameState.achievements).forEach(ach => {
        const achEl = document.createElement('div');
        achEl.className = 'achievement-item' + (ach.unlocked ? 'unlocked' : 'locked');
        achEl.innerHTML = `<span class="achievement-emoji">${ach.emoji}</span><div class="achievement-info"><div class="achievement-name">${ach.name}</div><div class="achievement-desc">${ach.description}</div></div>`;
        el.appendChild(achEl);
    });
}

function showPlotActions(index) {
    const plot = gameState.landPlots[index];
    const crop = CROPS[plot.cropId];
    showModal(`${crop.name}`, `<div class="crop-info-modal"><div class="crop-emoji-large">${getGrowthEmoji(crop, plot.growthStage)}</div><div class="crop-status"><p>阶段：<strong>${GROWTH_STAGES[plot.growthStage]}</strong></p><p>进度：<strong>${Math.floor(plot.growthTime/crop.growthTime*100)}%</strong></p><p>剩余：<strong>${formatTime(Math.max(0, crop.growthTime-plot.growthTime)/1000)}</strong></p>${plot.isWatered?'<p style="color:#32CD32">💧 水分充足</p>':''}${plot.hasWeed?'<p style="color:#FF6347">🌿 需要除草</p>':''}</div><div class="modal-actions">${!plot.isWatered?`<button class="modal-action-btn water" onclick="closeModal();waterCrop(${index})">💧 浇水</button>`:''}${plot.hasWeed?`<button class="modal-action-btn clear-weed" onclick="closeModal();clearWeed(${index})">🌿 除草</button>`:''}</div></div><button class="close-btn" onclick="closeModal()">关闭</button>`);
}

function plantCrop(index, cropId) {
    if (!cropId) return;
    const seedCount = gameState.inventory[cropId] || 0;
    if (seedCount <= 0) { showFloatingMessage('没有种子了，去商店购买吧', 'info'); return; }
    const crop = CROPS[cropId];
    gameState.inventory[cropId]--;
    gameState.landPlots[index] = { ...createEmptyPlot(index), cropId, plantTime: Date.now(), readyTime: Date.now() + crop.growthTime, isWatered: false, hasWeed: false, growthStage: 1, waterCount: 0 };
    const landLevel = gameState.landPlots[index].landLevel || 1;
    gameState.stats.timesPlanted++;
    addCoinLog(`种植${crop.name}`, 0);
    playSound('plant');
    showFloatingMessage(`🌱 种植了 ${crop.name}`, 'success');
    saveGame(); renderGame();
}

function waterCrop(index) {
    if (index === undefined || index === null) return;
    const plot = gameState.landPlots[index];
    if (!plot.cropId) return;
    plot.isWatered = true;
    plot.waterCount++;
    showFloatingMessage('💧 已浇水', 'success');
    playSound('plant');
    renderGame();
}

function clearWeed(index) {
    if (index === undefined || index === null) return;
    const plot = gameState.landPlots[index];
    if (!plot.cropId) return;
    plot.hasWeed = false;
    const reward = 5;
    gameState.coins += reward;
    showFloatingMessage(`🌿 清除杂草，+${reward} 金币`, 'success');
    playSound('coin');
    renderGame();
    saveGame();
}

function harvestCrop(index) {
    const plot = gameState.landPlots[index];
    const crop = CROPS[plot.cropId];
    const harvestTime = new Date();
    let coins = crop.sellPrice, exp = crop.exp;
    const landLevel = plot.landLevel || 1;
    coins = Math.floor(coins * (1 + (landLevel - 1) * 0.1));
    exp = Math.floor(exp * (1 + (landLevel - 1) * 0.1));
    if (!plot.hasWeed && plot.isWatered) { coins = Math.floor(coins * 1.2); exp = Math.floor(exp * 1.2); showFloatingMessage('✨ 完美收获!', 'bonus'); playSound('levelup'); }
    else { playSound('harvest'); }
    gameState.coins += coins;
    addExp(exp);
    gameState.harvestCount++;
    gameState.stats.totalHarvest++;
    gameState.stats.totalEarn += coins;
    gameState.harvestLog.push({ time: Date.now(), cropId: plot.cropId, cropName: crop.name, count: 1, coins, exp, harvestTime: harvestTime.toLocaleString('zh-CN') });
    addCoinLog(`收获${crop.name}`, coins);
    if (Math.random() < 0.15) { gameState.inventory[plot.cropId] = (gameState.inventory[plot.cropId] || 0) + 1; showFloatingMessage(`🎁 +1 种子`, 'bonus'); playSound('coin'); }
    gameState.landPlots[index] = createEmptyPlot(index);
    updateActionTip(`🎉 收获 ${crop.name}, +${coins} 金币`);
    showFloatingMessage(`+${coins}`, 'coins');
    checkAchievements();
    saveGame(); renderGame();
}

function clearWitheredPlot(index) { gameState.landPlots[index] = createEmptyPlot(index); updateActionTip('已清理枯萎作物'); saveGame(); renderGame(); }

function addExp(amount) {
    gameState.exp += amount;
    let levelUp = false;
    while (gameState.level < GAME_CONFIG.maxLevel && gameState.exp >= getExpForLevel(gameState.level)) {
        gameState.exp -= getExpForLevel(gameState.level);
        gameState.level++;
        gameState.coins += 50;
        levelUp = true;
    }
    if (gameState.level >= GAME_CONFIG.maxLevel) gameState.exp = getExpForLevel(GAME_CONFIG.maxLevel);
    if (levelUp) {
        addCoinLog('升级奖励', 50);
        showModal('🎉 升级!', `<p>达到 ${gameState.level} 级!</p><p>奖励 +50 金币</p><button class="close-btn" onclick="closeModal()">好的</button>`);
        showFloatingMessage('🎉 Lv.UP!', 'levelup');
    }
    renderHeader();
}

function addCoinLog(desc, coins) {
    gameState.coinLog.push({ time: Date.now(), desc, coins, type: coins >= 0 ? 'positive' : 'negative' });
    if (gameState.coinLog.length > 50) gameState.coinLog.shift();
}

function checkAchievements() {
    let newly = [];
    if (gameState.harvestCount >= 1 && !gameState.achievements.firstHarvest.unlocked) { gameState.achievements.firstHarvest.unlocked = true; newly.push(gameState.achievements.firstHarvest); }
    if (gameState.level >= 10 && !gameState.achievements.level10.unlocked) { gameState.achievements.level10.unlocked = true; newly.push(gameState.achievements.level10); }
    if (gameState.level >= 50 && !gameState.achievements.level50.unlocked) { gameState.achievements.level50.unlocked = true; newly.push(gameState.achievements.level50); }
    if (gameState.level >= 100 && !gameState.achievements.level100.unlocked) { gameState.achievements.level100.unlocked = true; newly.push(gameState.achievements.level100); }
    if (gameState.level >= 200 && !gameState.achievements.level200.unlocked) { gameState.achievements.level200.unlocked = true; newly.push(gameState.achievements.level200); }
    if (gameState.coins >= 10000 && !gameState.achievements.richFarmer.unlocked) { gameState.achievements.richFarmer.unlocked = true; newly.push(gameState.achievements.richFarmer); }
    if (gameState.harvestCount >= 100 && !gameState.achievements.harvest100.unlocked) { gameState.achievements.harvest100.unlocked = true; newly.push(gameState.achievements.harvest100); }
    if (newly.length) showModal('🏆 成就解锁', `<div class="achievement-unlock">${newly.map(a=>`${a.emoji} ${a.name}`).join('<br>')}</div><button class="close-btn" onclick="closeModal()">好的</button>`);
}

function checkGrowthStage(index) {
    const plot = gameState.landPlots[index];
    if (!plot.cropId || plot.withered) return;
    const crop = CROPS[plot.cropId];
    const newStage = Math.floor((plot.growthTime / crop.growthTime) * (GROWTH_STAGES.length - 1));
    plot.growthStage = Math.min(Math.max(newStage, 0), GROWTH_STAGES.length - 1);
    if (plot.growthStage >= GROWTH_STAGES.length - 1) plot.growthTime = crop.growthTime;
}

function checkWither() {
    const now = Date.now();
    let withered = false;
    gameState.landPlots.forEach(plot => {
        if (plot.cropId && !plot.withered && plot.plantedTime && now - plot.plantedTime > GAME_CONFIG.witherTime) {
            plot.withered = true; plot.growthStage = 0; plot.isWatered = false; plot.hasWeed = false; withered = true;
        }
    });
    if (withered) updateActionTip('⚠️ 有作物枯萎了');
}

function startGameLoop() {
    setInterval(() => {
        gameState.landPlots.forEach((plot, index) => {
            if (plot.cropId && !plot.withered) {
                let rate = 1;
                if (plot.isWatered) rate *= 1.5;
                if (plot.hasWeed) rate *= 0.8;
                plot.growthTime += 1000 * rate;
                checkGrowthStage(index);
            }
        });
        checkWither(); saveGame(); renderFarmGrid(); renderHeader();
    }, 1000);
}

function showFloatingMessage(text, type) {
    const msgEl = document.createElement('div');
    msgEl.className = 'floating-message ' + (type || '');
    msgEl.textContent = text;
    msgEl.style.left = (50 + Math.random() * 20 - 10) + '%';
    msgEl.style.top = (40 + Math.random() * 20 - 10) + '%';
    document.body.appendChild(msgEl);
    setTimeout(() => { msgEl.classList.add('fade-out'); setTimeout(() => msgEl.remove(), 500); }, 1500);
}

function updateActionTip(msg) { const el = document.getElementById('action-tip'); if (el) { el.textContent = msg; setTimeout(() => { el.textContent = '💡 点击土地进行操作'; }, 3000); } }

function showModal(title, content) {
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');
    if (modal && modalTitle && modalBody) { modalTitle.textContent = title; modalBody.innerHTML = content; modal.classList.add('show'); }
}

function closeModal() { const modal = document.getElementById('modal'); if (modal) modal.classList.remove('show'); }

function resetGame() { if (confirm('确定删除进度？')) { gameState.users = {}; saveUsers(); localStorage.removeItem('farmGameCurrent'); location.reload(); } }

function harvestAll() {
    let count = 0;
    gameState.landPlots.forEach((plot, index) => { if (plot.cropId && !plot.withered && plot.growthStage >= GROWTH_STAGES.length - 1) { harvestCrop(index); count++; } });
    if (!count) updateActionTip('没有可收获的作物');
    else { updateActionTip(`收获 ${count} 个作物`); showFloatingMessage(`x${count}`, 'success'); }
}

function scrollToSection(id) {
    let el;
    if (id === 'farm-area') el = document.querySelector('.farm-area');
    else if (id === 'seed-inventory') el = document.getElementById('seed-inventory')?.closest('.panel-section');
    else if (id === 'crop-inventory') el = document.getElementById('crop-inventory')?.closest('.panel-section');
    else if (id === 'seed-shop') el = document.getElementById('shop-area')?.closest('.panel-section');
    else if (id === 'achievements-panel') el = document.getElementById('achievements-panel')?.closest('.panel-section');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    document.querySelectorAll('.nav-item').forEach((item, idx) => {
        const ids = ['farm-area', 'seed-inventory', null, null, null];
        item.classList.toggle('active', ids[idx] === id);
    });
}

function formatTime(seconds) { if (!seconds || seconds < 60) return Math.floor(seconds || 0) + '秒'; if (seconds < 3600) return Math.floor(seconds / 60) + '分钟'; return Math.floor(seconds / 3600) + '小时' + Math.floor((seconds % 3600) / 60) + '分钟'; }

function togglePanel(panelId) {
    const panel = document.getElementById(panelId);
    if (panel) {
        const isHidden = panel.style.display === 'none' || panel.style.display === '';
        document.querySelectorAll('.sub-panel').forEach(p => p.style.display = 'none');
        panel.style.display = isHidden ? 'block' : 'none';
    }
}

function toggleUserPanel() {
    showFloatingMessage('用户管理请在后台进行', 'info');
}

function renderPet() {
    const pet = gameState.pet;
    document.getElementById('modal-pet-emoji').textContent = PETS.find(p => p.id === pet.id)?.emoji || '🐶';
    document.getElementById('modal-pet-name').textContent = pet.name;
    document.getElementById('modal-pet-heart').textContent = '❤️'.repeat(pet.heart);
}

function renderPetSelector() {
    const el = document.getElementById('pet-selector');
    if (!el) return;
    el.innerHTML = '';
    PETS.forEach(pet => {
        const canUnlock = gameState.level >= pet.unlockLevel;
        const isSelected = gameState.pet.id === pet.id;
        const petEl = document.createElement('div');
        petEl.className = 'pet-option' + (isSelected ? ' selected' : '');
        petEl.style.opacity = canUnlock ? '1' : '0.5';
        petEl.innerHTML = `<span class="pet-emoji">${pet.emoji}</span><span class="pet-name">${pet.name}${pet.unlockLevel > 1 ? ' (Lv.' + pet.unlockLevel + ')' : ''}</span>`;
        petEl.addEventListener('click', () => {
            if (canUnlock) {
                gameState.pet.id = pet.id;
                gameState.pet.name = pet.name;
                renderPet();
                renderPetSelector();
                showFloatingMessage(`选择 ${pet.name}`, 'success');
                saveGame();
            } else {
                showFloatingMessage(`需要 Lv.${pet.unlockLevel}`, 'info');
            }
        });
        el.appendChild(petEl);
    });
}

function interactWithPet() {
    const now = Date.now();
    if (now - gameState.pet.lastInteract < 3600000) {
        showFloatingMessage(`${Math.ceil((3600000 - (now - gameState.pet.lastInteract)) / 60000)}分钟后再互动`, 'info');
        return;
    }
    gameState.pet.heart = Math.min(5, gameState.pet.heart + 1);
    gameState.pet.lastInteract = now;
    gameState.coins += 10;
    addCoinLog('宠物互动奖励', 10);
    showFloatingMessage('❤️ 心情 +1, +10 金币', 'success');
    renderPet();
    saveGame();
    renderHeader();
}

function showPetPanel() {
    renderPet();
    renderPetSelector();
    showModal('pet-modal');
}

function showAvatarPanel() {
    const el = document.getElementById('avatar-selector');
    if (!el) return;
    el.innerHTML = '';
    AVATARS.forEach(avatar => {
        const avatarEl = document.createElement('div');
        avatarEl.className = 'avatar-option' + ((gameState.avatar || '👤') === avatar ? ' selected' : '');
        avatarEl.textContent = avatar;
        avatarEl.addEventListener('click', () => {
            gameState.avatar = avatar;
            document.getElementById('current-avatar-preview').textContent = avatar;
            document.getElementById('player-avatar').textContent = avatar;
            el.querySelectorAll('.avatar-option').forEach(e => e.classList.remove('selected'));
            avatarEl.classList.add('selected');
            saveGame();
            showFloatingMessage('头像已更新', 'success');
        });
        el.appendChild(avatarEl);
    });
    document.getElementById('current-avatar-preview').textContent = gameState.avatar || '👤';
    showModal('avatar-modal');
}

function showFriendsPanel() {
    renderFriendsList();
    showModal('friends-modal');
}

function renderFriendsList() {
    const el = document.getElementById('friends-list');
    if (!el) return;
    const friends = gameState.users[gameState.currentUserId]?.friends || [];
    if (friends.length === 0) {
        el.innerHTML = '<div style="text-align:center;color:#888;padding:20px;">暂无好友<br><span style="font-size:0.8em">输入好友 ID 添加好友</span></div>';
        return;
    }
    el.innerHTML = '';
    friends.forEach(friendId => {
        const friend = gameState.users[friendId];
        if (!friend) return;
        const itemEl = document.createElement('div');
        itemEl.className = 'friend-item';
        itemEl.innerHTML = `
            <div class="friend-info">
                <div class="friend-avatar">${friend.avatar || '👤'}</div>
                <div class="friend-details">
                    <div class="friend-name">${friend.displayName || '农场主'}</div>
                    <div class="friend-id">ID: ${friendId.slice(-6).toUpperCase()}</div>
                </div>
                <span class="friend-level">Lv.${friend.level}</span>
            </div>
            <button class="visit-btn" onclick="visitFriendFarm('${friendId}')">访问</button>
        `;
        el.appendChild(itemEl);
    });
}

function searchFriend() {
    const input = document.getElementById('friend-search-input');
    const searchId = input.value.trim().toUpperCase();
    if (!searchId) {
        showFloatingMessage('请输入好友 ID', 'info');
        return;
    }
    const foundId = Object.keys(gameState.users).find(id => id.slice(-6).toUpperCase() === searchId);
    if (!foundId) {
        showFloatingMessage('未找到该用户', 'info');
        return;
    }
    if (foundId === gameState.currentUserId) {
        showFloatingMessage('不能添加自己为好友', 'info');
        return;
    }
    const userData = gameState.users[gameState.currentUserId];
    if (!userData.friends) userData.friends = [];
    if (userData.friends.includes(foundId)) {
        showFloatingMessage('已是好友', 'info');
        return;
    }
    userData.friends.push(foundId);
    saveCurrentUser();
    renderFriendsList();
    showFloatingMessage('添加好友成功', 'success');
    input.value = '';
}

function visitFriendFarm(friendId) {
    const friend = gameState.users[friendId];
    if (!friend) {
        showFloatingMessage('好友不存在', 'info');
        return;
    }
    const tempCurrent = gameState.currentUserId;
    gameState.currentUserId = friendId;
    loadUserData();
    renderGame();
    gameState.currentUserId = tempCurrent;
    loadUserData();
    closeModal('friends-modal');
    showFloatingMessage(`访问 ${friend.displayName} 的农场`, 'success');
}

function showSellModal(cropId) {
    sellCropId = cropId;
    sellQuantity = gameState.inventory[cropId] || 0;
    if (sellQuantity <= 0) {
        showFloatingMessage('没有可出售的果实', 'info');
        return;
    }
    const crop = CROPS[cropId];
    document.getElementById('sell-modal-body').innerHTML = `
        <div style="font-size:3em;margin-bottom:10px;">${crop.emoji}</div>
        <div style="color:#FFD700;font-size:1.1em;font-weight:bold;">${crop.name}</div>
        <div style="color:#888;font-size:0.8em;">售价：💰${crop.sellPrice}/个</div>
        <div style="color:#888;font-size:0.8em;">拥有：x${sellQuantity}</div>
    `;
    document.getElementById('sell-qty').textContent = sellQuantity;
    updateSellTotal();
    showModal('sell-modal');
}

function adjustSellQty(delta) {
    if (!sellCropId) return;
    const max = gameState.inventory[sellCropId] || 0;
    sellQuantity = Math.max(0, Math.min(max, sellQuantity + delta));
    document.getElementById('sell-qty').textContent = sellQuantity;
    updateSellTotal();
}

function updateSellTotal() {
    if (!sellCropId) return;
    const crop = CROPS[sellCropId];
    const total = sellQuantity * crop.sellPrice;
    document.querySelector('#sell-modal .total-coins').textContent = total;
}

function confirmSell() {
    if (!sellCropId || sellQuantity <= 0) return;
    const crop = CROPS[sellCropId];
    const total = sellQuantity * crop.sellPrice;
    gameState.inventory[sellCropId] -= sellQuantity;
    if (gameState.inventory[sellCropId] <= 0) delete gameState.inventory[sellCropId];
    gameState.coins += total;
    addCoinLog(`出售${crop.name} x${sellQuantity}`, total);
    showFloatingMessage(`出售 ${crop.name} x${sellQuantity}, +${total} 金币`, 'success');
    playSound('coin');
    closeSellModal();
    saveGame();
    renderGame();
}

function closeSellModal() {
    sellCropId = null;
    sellQuantity = 0;
    closeModal('sell-modal');
}

function handleAvatarUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (file.size > 500000) {
        showFloatingMessage('图片大小不能超过 500KB', 'info');
        return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
        gameState.avatar = e.target.result;
        document.getElementById('current-avatar-preview').innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">`;
        document.getElementById('player-avatar').innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">`;
        saveGame();
        showFloatingMessage('头像已更新', 'success');
    };
    reader.readAsDataURL(file);
}

function showAdminLogin() {
    document.getElementById('admin-username').value = '';
    document.getElementById('admin-password').value = '';
    showModal('admin-login-modal');
}

function adminLogin() {
    const username = document.getElementById('admin-username').value;
    const password = document.getElementById('admin-password').value;
    if (username === '123456' && password === '123456') {
        closeModal('admin-login-modal');
        window.location.href = 'admin.html';
    } else {
        showFloatingMessage('账号或密码错误', 'info');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('show');
}

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('show');
}

function checkOfflineEarnings() {
    const now = Date.now();
    const lastLogin = gameState.lastLoginTime || now;
    const offlineTime = now - lastLogin;
    const offlineHours = Math.floor(offlineTime / 3600000);
    
    if (offlineHours > 0) {
        // 计算离线收益：每小时 20 金币
        const earnings = offlineHours * 20;
        gameState.coins += earnings;
        gameState.offlineEarnings = earnings;
        addCoinLog(`离线奖励 (${offlineHours}小时)`, earnings);
        showFloatingMessage(`🎁 离线奖励：+${earnings} 金币`, 'bonus');
        
        // 检查作物是否枯萎
        gameState.landPlots.forEach((plot, index) => {
            if (plot.cropId && !plot.withered) {
                const crop = CROPS[plot.cropId];
                const timeSincePlanted = now - (plot.plantedTime || now);
                if (timeSincePlanted > crop.growthTime + GAME_CONFIG.witherTime) {
                    plot.withered = true;
                    plot.cropId = null;
                }
            }
        });
        
        gameState.lastLoginTime = now;
        saveGame();
        renderHeader();
    } else {
        gameState.lastLoginTime = now;
    }
}

function showDecorationPanel() {
    const el = document.getElementById('decoration-panel');
    if (!el) return;
    el.innerHTML = '';
    DECORATIONS.forEach(dec => {
        const owned = gameState.decorations[dec.id] || 0;
        const decEl = document.createElement('div');
        decEl.className = 'decoration-item';
        decEl.style.background = '#2d2d44';
        decEl.style.border = '2px solid #444';
        decEl.style.borderRadius = '8px';
        decEl.style.padding = '10px';
        decEl.style.textAlign = 'center';
        decEl.innerHTML = `
            <div style="font-size:2em;margin-bottom:5px;">${dec.emoji}</div>
            <div style="color:#FFD700;font-size:0.9em;font-weight:bold;">${dec.name}</div>
            <div style="color:#888;font-size:0.7em;margin:5px 0;">💰 ${dec.price}</div>
            <div style="color:#888;font-size:0.65em;">拥有：x${owned}</div>
            <button onclick="buyDecoration('${dec.id}')" style="margin-top:8px;padding:6px 15px;background:linear-gradient(135deg,#4CAF50 0%,#45a049 100%);color:#FFF;border:none;border-radius:6px;cursor:pointer;font-size:0.75em;">购买</button>
        `;
        el.appendChild(decEl);
    });
    showModal('decoration-modal');
}

function buyDecoration(decId) {
    const dec = DECORATIONS.find(d => d.id === decId);
    if (!dec) return;
    if (gameState.coins < dec.price) {
        showFloatingMessage('金币不足', 'info');
        return;
    }
    gameState.coins -= dec.price;
    gameState.decorations[decId] = (gameState.decorations[decId] || 0) + 1;
    addCoinLog(`购买装饰 ${dec.name}`, -dec.price);
    showFloatingMessage(`购买 ${dec.name} 成功`, 'success');
    saveGame();
    renderHeader();
    showDecorationPanel();
}

// 音效系统
let audioEnabled = true;
let bgmEnabled = true;
let volume = 0.5;

function initAudio() {
    const savedSettings = localStorage.getItem('farmGameAudioSettings');
    if (savedSettings) {
        const settings = JSON.parse(savedSettings);
        bgmEnabled = settings.bgmEnabled !== false;
        audioEnabled = settings.sfxEnabled !== false;
        volume = settings.volume || 0.5;
    }
    document.getElementById('bgm-toggle').checked = bgmEnabled;
    document.getElementById('sfx-toggle').checked = audioEnabled;
    document.getElementById('volume-slider').value = volume * 100;
    document.getElementById('volume-value').textContent = Math.round(volume * 100) + '%';
}

function playSound(type) {
    if (!audioEnabled) return;
    // 简单的 Web Audio API 音效
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    const sounds = {
        harvest: { freq: 523.25, duration: 0.1 },
        plant: { freq: 392, duration: 0.15 },
        coin: { freq: 1046.5, duration: 0.2 },
        levelup: { freq: 659.25, duration: 0.3 },
        click: { freq: 880, duration: 0.05 },
    };
    
    const sound = sounds[type] || sounds.click;
    oscillator.frequency.value = sound.freq;
    oscillator.type = 'sine';
    gainNode.gain.setValueAtTime(volume, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + sound.duration);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + sound.duration);
}

function toggleBGM() {
    bgmEnabled = document.getElementById('bgm-toggle').checked;
    saveAudioSettings();
    playSound('click');
}

function toggleSFX() {
    audioEnabled = document.getElementById('sfx-toggle').checked;
    saveAudioSettings();
    playSound('click');
}

function setVolume(value) {
    volume = value / 100;
    document.getElementById('volume-value').textContent = value + '%';
    saveAudioSettings();
    playSound('click');
}

function saveAudioSettings() {
    localStorage.setItem('farmGameAudioSettings', JSON.stringify({
        bgmEnabled,
        sfxEnabled: audioEnabled,
        volume
    }));
}

// 设置面板
function showSettingsPanel() {
    initAudio();
    document.getElementById('settings-avatar').textContent = gameState.avatar || '👤';
    document.getElementById('settings-username').textContent = gameState.displayName || '农场主';
    document.getElementById('settings-userid').textContent = gameState.currentUserId.slice(-6).toUpperCase();
    document.getElementById('settings-level').textContent = gameState.level;
    document.getElementById('settings-coins').textContent = gameState.coins;
    showModal('settings-modal');
}

function showHelpModal() {
    const modal = document.getElementById('modal');
    document.getElementById('modal-title').textContent = '🎮 游戏帮助';
    document.getElementById('modal-body').innerHTML = `
        <div style="line-height:1.8;color:#888;">
            <p>🌱 <strong>种植：</strong>在商店购买种子，点击背包选择种子，再点击空地种植</p>
            <p>💧 <strong>照料：</strong>及时浇水可提高收成，注意清除杂草</p>
            <p>🎉 <strong>收获：</strong>作物成熟后及时收获，可获得金币和经验</p>
            <p>⬆️ <strong>升级土地：</strong>点击土地升级按钮可提高收益加成</p>
            <p>🐾 <strong>宠物：</strong>与宠物互动可获得金币奖励</p>
        </div>
    `;
    document.getElementById('modal-footer').innerHTML = '<button class="modal-action-btn secondary" onclick="closeModal(\'modal\')">知道了</button>';
    modal.classList.add('show');
}

function showLandPanel() {
    const statsEl = document.querySelector('.land-stats');
    const listEl = document.getElementById('land-list');
    if (!statsEl || !listEl) return;
    
    // 统计土地信息
    const landStats = { 1:0, 2:0, 3:0, 4:0, 5:0, 6:0, 7:0 };
    let totalValue = 0;
    gameState.landPlots.forEach(plot => {
        const level = plot.landLevel || 1;
        landStats[level]++;
        totalValue += LAND_LEVELS[level-1].upgradeCost;
    });
    
    statsEl.innerHTML = `
        <div style="background:#1a1a2e;padding:10px;border-radius:8px;border:1px solid #FFD700;">
            <div style="color:#888;font-size:0.7em;">土地总数</div>
            <div style="color:#FFD700;font-size:1.3em;font-weight:bold;">${gameState.landPlots.length} 块</div>
        </div>
        <div style="background:#1a1a2e;padding:10px;border-radius:8px;border:1px solid #32CD32;">
            <div style="color:#888;font-size:0.7em;">总价值</div>
            <div style="color:#32CD32;font-size:1.3em;font-weight:bold;">💰 ${totalValue}</div>
        </div>
    `;
    
    // 显示每块土地详情
    listEl.innerHTML = '';
    gameState.landPlots.forEach((plot, index) => {
        const level = plot.landLevel || 1;
        const nextLevel = level + 1;
        const canUpgrade = nextLevel <= 7 && gameState.level >= LAND_LEVELS[nextLevel-1].unlockLevel;
        const upgradeCost = nextLevel <= 7 ? LAND_LEVELS[nextLevel-1].upgradeCost : 0;
        const bonus = (level - 1) * 10;
        
        const landEl = document.createElement('div');
        landEl.style.cssText = 'background:#2d2d44;border:2px solid #444;border-radius:8px;padding:10px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center;';
        landEl.innerHTML = `
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:40px;height:40px;background:${LAND_LEVELS[level-1].color};border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:1.2em;font-weight:bold;color:#FFF;">L${level}</div>
                <div>
                    <div style="color:#FFD700;font-size:0.9em;font-weight:bold;">${LAND_LEVELS[level-1].name}</div>
                    <div style="color:#888;font-size:0.7em;">收益加成：+${bonus}%</div>
                    <div style="color:#888;font-size:0.65em;">${plot.cropId ? `🌱 ${CROPS[plot.cropId].name}` : '🟫 空闲'}</div>
                </div>
            </div>
            ${canUpgrade && gameState.coins >= upgradeCost ? `
                <button onclick="upgradeLand(${index},${nextLevel})" style="padding:6px 12px;background:linear-gradient(135deg,#FFD700 0%,#FFA500 100%);color:#1a1a2e;border:none;border-radius:6px;cursor:pointer;font-size:0.75em;font-weight:bold;">
                    升级 L${nextLevel}<br>💰${upgradeCost}
                </button>
            ` : canUpgrade ? `
                <button disabled style="padding:6px 12px;background:#444;color:#888;border:none;border-radius:6px;cursor:not-allowed;font-size:0.75em;">
                    金币不足<br>💰${upgradeCost}
                </button>
            ` : `
                <button disabled style="padding:6px 12px;background:#444;color:#888;border:none;border-radius:6px;cursor:not-allowed;font-size:0.75em;">
                    需要 Lv.${LAND_LEVELS[nextLevel-1]?.unlockLevel || '-'}
                </button>
            `}
        `;
        listEl.appendChild(landEl);
    });
    
    showModal('land-modal');
}

function logout() { if (confirm('确定退出后台管理？')) { window.open('admin.html', '_blank'); } }

initGame();
