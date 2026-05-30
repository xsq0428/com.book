<?php
require_once 'config/database.php';

$pdo = getDbConnection();

$stmt = $pdo->query("SELECT * FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$stmt = $pdo->query("SELECT * FROM urls WHERE is_active = 1 ORDER BY sort_order");
$urls = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM ads WHERE is_active = 1 ORDER BY sort_order");
$ads = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM announcements WHERE is_active = 1 ORDER BY sort_order");
$announcements = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($settings['site_title'] ?? '二次元地址发布页') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans SC', sans-serif;
            background: linear-gradient(135deg, #ffeef8 0%, #f0e6ff 50%, #e6f3ff 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        .anime-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255, 182, 193, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(221, 160, 221, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 192, 203, 0.2) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
            animation: bgShift 20s ease-in-out infinite;
        }

        @keyframes bgShift {
            0%, 100% { transform: translateX(0) translateY(0) rotate(0deg); }
            25% { transform: translateX(-20px) translateY(-10px) rotate(1deg); }
            50% { transform: translateX(10px) translateY(-20px) rotate(-1deg); }
            75% { transform: translateX(-10px) translateY(10px) rotate(0.5deg); }
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            min-height: 100vh;
            position: relative;
            padding: 20px 0;
        }

        .anime-decoration {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .floating-element {
            position: absolute;
            font-size: 28px;
            animation: float 8s ease-in-out infinite;
            opacity: 0.6;
            filter: drop-shadow(0 0 10px rgba(255, 182, 193, 0.5));
        }

        .element-1 { top: 15%; left: 10%; animation-delay: 0s; }
        .element-2 { top: 25%; right: 15%; animation-delay: 1.5s; }
        .element-3 { top: 65%; left: 8%; animation-delay: 3s; }
        .element-4 { top: 75%; right: 12%; animation-delay: 4.5s; }
        .element-5 { top: 45%; left: 50%; animation-delay: 2s; }
        .element-6 { top: 35%; right: 30%; animation-delay: 5s; }
        .element-7 { top: 20%; left: 20%; animation-delay: 6s; }
        .element-8 { top: 80%; right: 25%; animation-delay: 7s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            25% { transform: translateY(-15px) rotate(90deg) scale(1.1); }
            50% { transform: translateY(-25px) rotate(180deg) scale(0.9); }
            75% { transform: translateY(-10px) rotate(270deg) scale(1.05); }
        }

        .header {
            text-align: center;
            padding: 20px 16px;
            position: relative;
            z-index: 2;
        }

        .logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 16px;
        }

        .logo {
            width: 90px;
            height: 90px;
            background: linear-gradient(45deg, #ffb6c1, #ffc0cb, #dda0dd, #e6e6fa);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            color: white;
            box-shadow: 0 8px 32px rgba(255, 182, 193, 0.4);
            animation: pulse 3s ease-in-out infinite;
            border: 3px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
        }

        .logo-inner {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .logo-glow {
            position: absolute;
            top: -15px;
            left: -15px;
            right: -15px;
            bottom: -15px;
            background: linear-gradient(45deg, #ffb6c1, #ffc0cb, #dda0dd);
            border-radius: 50%;
            opacity: 0.2;
            animation: glow 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes glow {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.1); opacity: 0.6; }
        }

        .main-title {
            font-size: 22px;
            font-weight: 700;
            color: #d63384;
            margin-bottom: 8px;
            text-shadow: 0 2px 8px rgba(214, 51, 132, 0.3);
        }

        .subtitle {
            font-size: 14px;
            color: #e91e63;
            font-weight: 400;
            opacity: 0.8;
        }

        .main-content {
            background: rgba(255, 255, 255, 0.85);
            margin: 0 16px;
            border-radius: 25px;
            padding: 16px;
            box-shadow: 0 20px 40px rgba(255, 182, 193, 0.2);
            backdrop-filter: blur(15px);
            position: relative;
            z-index: 2;
            border: 1px solid rgba(255, 182, 193, 0.3);
        }

        .announcement-box {
            background: linear-gradient(45deg, #ff9a9e, #fecfef);
            padding: 8px 12px;
            border-radius: 12px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #333;
            box-shadow: 0 4px 12px rgba(255, 154, 158, 0.3);
        }

        .button-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .interactive-btn {
            background: linear-gradient(45deg, #ffb6c1, #ffc0cb);
            border: none;
            border-radius: 18px;
            padding: 10px 8px;
            color: #d63384;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            box-shadow: 0 4px 12px rgba(255, 182, 193, 0.4);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .interactive-btn:nth-child(1) { background: linear-gradient(45deg, #ffb6c1, #ffc0cb); }
        .interactive-btn:nth-child(2) { background: linear-gradient(45deg, #dda0dd, #e6e6fa); }
        .interactive-btn:nth-child(3) { background: linear-gradient(45deg, #ffc0cb, #ffb6c1); }
        .interactive-btn:nth-child(4) { background: linear-gradient(45deg, #f0e6ff, #e6f3ff); }
        .interactive-btn:nth-child(5) { background: linear-gradient(45deg, #ffeef8, #f0e6ff); }
        .interactive-btn:nth-child(6) { background: linear-gradient(45deg, #e6e6fa, #dda0dd); }

        .interactive-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-icon {
            font-size: 24px;
        }

        .btn-text {
            font-size: 13px;
            text-align: center;
        }

        .ad-section {
            background: linear-gradient(135deg, #ffeef8, #f0e6ff);
            border-radius: 15px;
            padding: 12px;
            margin: 12px 0;
            border: 2px solid rgba(255, 182, 193, 0.5);
            box-shadow: 0 4px 12px rgba(255, 182, 193, 0.3);
        }

        .ad-title {
            font-size: 14px;
            font-weight: 700;
            color: #ff1493;
            margin-bottom: 10px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(255, 20, 147, 0.3);
        }

        .ad-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .ad-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
            background: rgba(255, 255, 255, 0.8);
            padding: 8px 12px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(255, 182, 193, 0.2);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 182, 193, 0.3);
        }

        .ad-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 182, 193, 0.3);
        }

        .ad-label {
            font-weight: 600;
            color: #ff69b4;
            font-size: 13px;
        }

        .ad-link {
            color: #ff1493;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            transition: color 0.3s ease;
        }

        .ad-link:hover {
            color: #ff69b4;
            text-decoration: underline;
        }

        .ad-copy-btn {
            background: linear-gradient(135deg, #ff69b4, #ff1493);
            color: white;
            border: none;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(255, 105, 180, 0.3);
        }

        .ad-copy-btn:hover {
            background: linear-gradient(135deg, #ff1493, #dc143c);
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(255, 105, 180, 0.4);
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        .group-link-container {
            margin-bottom: 18px;
        }

        .group-link-btn {
            width: 100%;
            background: linear-gradient(45deg, #ff69b4, #ff1493);
            border: none;
            border-radius: 20px;
            padding: 14px 20px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 6px 20px rgba(255, 105, 180, 0.4);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .group-link-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 105, 180, 0.5);
        }

        .group-icon {
            font-size: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .btn-shine {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .address-section {
            background: linear-gradient(45deg, #ffb6c1, #dda0dd);
            border-radius: 20px;
            padding: 16px;
            color: #d63384;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .address-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
            font-size: 13px;
            font-weight: 500;
        }

        .address-url {
            margin-bottom: 12px;
        }

        .url-text {
            background: rgba(255, 255, 255, 0.6);
            padding: 8px 16px;
            border-radius: 12px;
            font-family: monospace;
            font-size: 14px;
            color: #d63384;
            display: inline-block;
            margin-right: 8px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .footer {
            margin: 20px 16px 0;
            position: relative;
            z-index: 2;
        }

        .footer-banner {
            background: linear-gradient(45deg, #ffecd2, #fcb69f);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(252, 182, 159, 0.3);
        }

        .banner-content h3 {
            font-size: 16px;
            color: #333;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .banner-content p {
            font-size: 12px;
            color: #666;
        }

        @media (max-width: 768px) {
            .container { max-width: 100%; padding: 20px 15px; }
            .button-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        }
        
        @media (max-width: 480px) {
            .main-content { margin: 0 8px; padding: 20px; }
            .main-title { font-size: 18px; }
            .button-grid { grid-template-columns: 1fr; gap: 10px; }
        }

        .container { animation: fadeIn 0.8s ease-out; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="anime-bg"></div>
        
        <div class="anime-decoration">
            <div class="floating-element element-1">✨</div>
            <div class="floating-element element-2">🌸</div>
            <div class="floating-element element-3">💫</div>
            <div class="floating-element element-4">⭐</div>
            <div class="floating-element element-5">🎀</div>
            <div class="floating-element element-6">💖</div>
            <div class="floating-element element-7">🦄</div>
            <div class="floating-element element-8">🌈</div>
        </div>

        <header class="header">
            <div class="logo-container">
                <div class="logo">
                    <img src="<?= htmlspecialchars($settings['site_logo'] ?? '') ?>" alt="头像" class="logo-inner">
                </div>
                <div class="logo-glow"></div>
            </div>
            <h1 class="main-title"><?= htmlspecialchars($settings['site_title'] ?? '') ?></h1>
            <p class="subtitle"><?= htmlspecialchars($settings['site_subtitle'] ?? '') ?></p>
        </header>

        <main class="main-content">
            <?php foreach ($announcements as $ann): ?>
            <div class="announcement-box">
                <span><?= htmlspecialchars($ann['icon']) ?></span>
                <span><?= htmlspecialchars($ann['content']) ?></span>
            </div>
            <?php endforeach; ?>

            <div class="button-grid">
                <?php foreach ($urls as $url): ?>
                <button class="interactive-btn" onclick="trackClick(<?= $url['id'] ?>, '<?= htmlspecialchars($url['url']) ?>')">
                    <span class="btn-icon"><?= htmlspecialchars($url['icon']) ?></span>
                    <span class="btn-text"><?= htmlspecialchars($url['name']) ?></span>
                </button>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($ads)): ?>
            <div class="ad-section">
                <div class="ad-title">💻 推荐服务</div>
                <div class="ad-content">
                    <?php foreach ($ads as $ad): ?>
                    <div class="ad-item">
                        <span class="ad-label"><?= htmlspecialchars($ad['title']) ?></span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <?php if ($ad['link_url']): ?>
                            <a href="<?= htmlspecialchars($ad['link_url']) ?>" target="_blank" class="ad-link"><?= htmlspecialchars($ad['content']) ?></a>
                            <?php else: ?>
                            <span class="ad-link"><?= htmlspecialchars($ad['content']) ?></span>
                            <?php endif; ?>
                            <?php if ($ad['copy_text']): ?>
                            <button class="ad-copy-btn" onclick="copyToClipboard('<?= htmlspecialchars($ad['copy_text']) ?>')">复制</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="group-link-container">
                <button class="group-link-btn" onclick="trackClick(0, '<?= htmlspecialchars($settings['group_link_url'] ?? '') ?>')">
                    <span class="group-icon">👆</span>
                    <span class="group-text"><?= htmlspecialchars($settings['group_link_text'] ?? '点击此处加内部群永不失联') ?></span>
                    <div class="btn-shine"></div>
                </button>
            </div>

            <div class="address-section">
                <div class="address-header">
                    <span class="address-icon">🔗</span>
                    <span>收藏本站永久地址:</span>
                </div>
                <div class="address-url">
                    <span class="url-text"><?= htmlspecialchars($settings['permanent_url'] ?? '') ?></span>
                    <span class="url-suffix">，防止失联!</span>
                </div>
                <div class="address-tips">
                    <p>网站域名经常更新，防止网站打不开</p>
                    <p>请务必截图收藏此网页，永久有效!</p>
                </div>
            </div>
        </main>

        <footer class="footer">
            <div class="footer-banner">
                <div class="banner-content">
                    <h3>二次元分享地址发布页</h3>
                    <p><?= htmlspecialchars($settings['footer_text'] ?? '© 2024 . All Rights Reserved') ?></p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showCopySuccess();
                }).catch(function() {
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
        }

        function fallbackCopy(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.top = "0";
            textArea.style.left = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy') ? showCopySuccess() : showCopyError();
            } catch (err) {
                showCopyError();
            }
            document.body.removeChild(textArea);
        }

        function showCopySuccess() {
            showToast('✅ 复制成功！', '#28a745');
        }

        function showCopyError() {
            showToast('❌ 复制失败，请手动复制', '#dc3545');
        }

        function showToast(message, bgColor) {
            var toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;top:20px;right:20px;background:' + bgColor + ';color:white;padding:12px 20px;border-radius:8px;font-size:14px;font-weight:600;z-index:10000;box-shadow:0 4px 12px rgba(0,0,0,0.3);animation:slideInRight 0.3s ease-out;';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(function() {
                toast.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(function() { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 300);
            }, 3000);
        }

        function trackClick(urlId, url) {
            fetch('api/click.php?id=' + urlId, { method: 'POST' });
            window.open(url, '_blank');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const floatingElements = document.querySelectorAll('.floating-element');
            
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                floatingElements.forEach(element => {
                    element.style.transform = 'translateY(' + (-scrolled * 0.5) + 'px)';
                });
            });

            document.addEventListener('mousemove', function(e) {
                const x = e.clientX / window.innerWidth;
                const y = e.clientY / window.innerHeight;
                floatingElements.forEach((element, index) => {
                    const speed = (index + 1) * 0.02;
                    element.style.transform = 'translate(' + ((x - 0.5) * speed * 100) + 'px,' + ((y - 0.5) * speed * 100) + 'px)';
                });
            });
        });
    </script>
</body>
</html>
