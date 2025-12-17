<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Điều Khiển Nhà Hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Press+Start+2P&display=swap');

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            background: #100a05; /* Nền tối hơi nâu nhẹ */
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }

        /* --- ARCADE CABINET --- */
        .arcade-cabinet {
            position: relative;
            width: 90vw;
            height: 90vh;
            max-width: 1200px;
            background: #2a1510; /* Màu gỗ tối/bếp */
            border: 15px solid #1a0a05;
            border-radius: 30px;
            box-shadow: 0 0 60px rgba(255, 140, 0, 0.15), inset 0 0 100px #000;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-sizing: border-box;
        }

        .arcade-header {
            text-align: center;
            color: #ff4500; /* Màu cam đỏ lửa */
            font-family: 'Press Start 2P', cursive;
            text-shadow: 0 0 10px #ff4500, 4px 4px 0 #8b0000;
            margin-bottom: 15px;
            font-size: 24px;
            border: 2px solid #ff4500;
            padding: 12px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(255, 69, 0, 0.4);
            animation: neonFlicker 3s infinite;
            text-transform: uppercase;
            background: rgba(0,0,0,0.5);
        }

        .screen-bezel {
            flex-grow: 1;
            position: relative;
            background: #0d0d0d;
            border-radius: 20px;
            border: 8px solid #333;
            box-shadow: inset 0 0 60px #000;
            overflow: hidden;
        }

        .screen-glare {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, transparent 40%);
            pointer-events: none;
            z-index: 10;
        }

        .controls-panel {
            height: 90px;
            margin-top: 15px;
            background: #1a1a1a;
            border-top: 4px solid #333;
            border-radius: 0 0 15px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 50px;
        }

        .d-pad {
            width: 100px;
            height: 100px;
            position: relative;
        }

        .d-pad::before,
        .d-pad::after {
            content: '';
            position: absolute;
            background: #444;
            border-radius: 4px;
            box-shadow: 0 2px 0 #000;
        }

        .d-pad::before {
            top: 35px;
            left: 0;
            width: 100px;
            height: 30px;
        }

        .d-pad::after {
            top: 0;
            left: 35px;
            width: 30px;
            height: 100px;
        }

        .action-btns {
            display: flex;
            gap: 25px;
        }

        .btn-arcade {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 3px solid rgba(0, 0, 0, 0.2);
            position: relative;
            cursor: pointer;
            transition: transform 0.1s;
        }

        .btn-arcade:active {
            transform: scale(0.95);
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .btn-a {
            background: #ff4500; /* Đỏ cam */
            box-shadow: 0 6px 0 #8b0000;
        }

        .btn-b {
            background: #FFD700; /* Vàng */
            box-shadow: 0 6px 0 #b8860b;
        }

        @keyframes neonFlicker {
            0%, 18%, 22%, 25%, 53%, 57%, 100% {
                text-shadow: 0 0 10px #ff4500, 0 0 20px #ff4500;
                opacity: 1;
            }
            20%, 24%, 55% {
                text-shadow: none;
                opacity: 0.5;
            }
        }

        /* --- DASHBOARD --- */
        .dashboard-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 5;
            pointer-events: none;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Cố định 3 cột cho đẹp */
            gap: 20px;
            width: 85%;
            max-width: 900px;
            justify-items: center;
            pointer-events: auto;
        }

        .dashboard-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
            padding: 15px 10px;
            width: 100%;
            max-width: 130px;
            text-align: center;
            color: #fff;
            text-decoration: none;
            background: rgba(20, 10, 5, 0.75); /* Nền tối ấm */
            border: 1px solid rgba(255, 140, 0, 0.3);
            backdrop-filter: blur(4px);
            transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }

        .dashboard-card:hover {
            transform: translateY(-8px) scale(1.05);
            background: linear-gradient(135deg, #ff4500, #ff8c00);
            box-shadow: 0 0 25px rgba(255, 69, 0, 0.6);
            border-color: #fff;
            color: #fff;
        }

        .dashboard-card i {
            margin-bottom: 8px;
            font-size: 2.5rem;
        }

        .dashboard-card span {
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Màu riêng cho từng thẻ để dễ phân biệt */
        .card-admin i { color: #ff3333; }
        .card-client i { color: #33cc33; }
        .card-nv i { color: #33ccff; }
        .card-lt i { color: #d942f5; }
        .card-bep i { color: #ffa500; }
        .card-qr i { color: #fff; }

        .dashboard-card:hover i { color: #fff; }

        canvas {
            width: 100%;
            height: 100%;
            display: block;
        }

        .tutorial {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            color: #888;
            font-size: 10px;
            font-family: 'Press Start 2P';
            z-index: 4;
            text-align: center;
            opacity: 0.7;
            pointer-events: none;
            background: rgba(0,0,0,0.5);
            padding: 5px 10px;
            border-radius: 4px;
        }

        /* --- SWITCH GAME BUTTON --- */
        .switch-game-btn {
            position: absolute;
            bottom: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, #ff8c00, #b22222);
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 0 20px #ff4500;
            cursor: pointer;
            z-index: 20;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.2s;
        }

        .switch-game-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 40px #ff4500;
        }

        .switch-game-btn i {
            color: #fff;
            font-size: 24px;
        }

        @media (max-width: 768px) {
            .arcade-cabinet {
                width: 100%;
                height: 100%;
                border: none;
                border-radius: 0;
            }
            .controls-panel { display: none; }
            .dashboard-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
            .arcade-header { font-size: 16px; margin-top: 10px; }
        }
    </style>
</head>

<body>

    <div class="arcade-cabinet">
        <div class="arcade-header" id="gameTitle">RESTAURANT OS</div>

        <div class="screen-bezel">
            <div class="screen-glare"></div>
            <canvas id="gameCanvas"></canvas>

            <div class="dashboard-wrapper">
                <div class="dashboard-grid">
                    <a href="{{ route('admin.dashboard') }}" target="_blank" class="dashboard-card card-admin">
                        <i class="fa fa-cogs"></i><span>Quản trị</span>
                    </a>
                    <a href="{{ route('home') }}" target="_blank" class="dashboard-card card-client">
                        <i class="fa fa-home"></i><span>Trang chủ</span>
                    </a>
                    <a href="{{ route('nhanVien.ban-an.index') }}" target="_blank" class="dashboard-card card-nv">
                        <i class="fa fa-concierge-bell"></i><span>Lễ tân</span>
                    </a>
                    <a href="{{ route('nhanVien.order.index') }}" target="_blank" class="dashboard-card card-lt">
                        <i class="fa fa-user-tie"></i><span>Nhân viên</span>
                    </a>
                    <a href="{{ route('bep.dashboard') }}" target="_blank" class="dashboard-card card-bep">
                        <i class="fa fa-utensils"></i><span>Bếp</span>
                    </a>
                    <a href="{{ route('oderqr.list') }}" target="_blank" class="dashboard-card card-qr">
                        <i class="fa fa-qrcode"></i><span>QR Menu</span>
                    </a>
                </div>
            </div>

            <div class="tutorial" id="gameTutorial">USE [W,A,S,D] TO COOK</div>
        </div>

        <div class="controls-panel">
            <div class="d-pad"></div>
            <div class="action-btns">
                <div class="btn-arcade btn-a" onclick="if(currentGame==='shooter') fireBullet()"></div>
                <div class="btn-arcade btn-b"></div>
            </div>
        </div>

        <div class="switch-game-btn" onclick="switchGame()" title="Đổi Game">
            <i class="fa-solid fa-gamepad"></i>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');

        // --- CHUNG ---
        let currentGame = 'snake';
        let canvasWidth, canvasHeight;

        function resizeCanvas() {
            const container = document.querySelector('.screen-bezel');
            canvas.width = container.clientWidth;
            canvasHeight = container.clientHeight;
            canvas.height = canvasHeight;
            canvasWidth = container.clientWidth;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // --- HÀM CHUYỂN GAME ---
        function switchGame() {
            fireworks = []; // Reset pháo hoa
            if (currentGame === 'snake') {
                currentGame = 'shooter';
                document.getElementById('gameTitle').innerText = 'KITCHEN DEFENSE';
                document.getElementById('gameTutorial').innerText = '[A,D] DI CHUYỂN | [SPACE] BẮN | [R] CHƠI LẠI';
                initShooter();
            } else {
                currentGame = 'snake';
                document.getElementById('gameTitle').innerText = 'FOOD SNAKE';
                document.getElementById('gameTutorial').innerText = 'DÙNG [W,A,S,D] ĐỂ SĂN MỒI';
                initSnake();
            }
        }

        // UTILS
        let fireworks = [];
        class Particle {
            constructor(x, y, color, speedScale = 1) {
                this.x = x;
                this.y = y;
                this.color = color;
                const a = Math.random() * Math.PI * 2;
                const s = (Math.random() * 3 + 1) * speedScale;
                this.vx = Math.cos(a) * s;
                this.vy = Math.sin(a) * s;
                this.alpha = 1;
                this.decay = Math.random() * 0.03 + 0.015;
                this.g = 0.08;
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;
                this.vy += this.g;
                this.alpha -= this.decay;
            }
            draw() {
                ctx.save();
                ctx.globalAlpha = this.alpha;
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, 3, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            }
        }

        function createExplosion(x, y, count, color, scale = 1) {
            const safe = Math.min(count, 50);
            for (let i = 0; i < safe; i++) fireworks.push(new Particle(x, y, color || '#FFD700', scale));
        }

        function spawnVictoryFireworks() {
            if (Math.random() < 0.05) {
                const c = ['#FF4500', '#FFD700', '#FFFFFF'];
                createExplosion(Math.random() * canvasWidth, Math.random() * canvasHeight / 2, 40, c[Math.floor(Math.random() * c.length)], 1.2);
            }
        }

        // GAME 1: SNAKE (Theme Bếp)
        const snakeGrid = 25;
        const snakeSpeed = 12;
        let snake = [],
            snakeDx = 1,
            snakeDy = 0,
            nextDx = 1,
            nextDy = 0;
        let food = {},
            snakeScore = 0;
        let snakeAccumulator = 0;

        function initSnake() {
            snake = [{x: 10, y: 10}, {x: 9, y: 10}, {x: 8, y: 10}];
            snakeDx = 1; snakeDy = 0;
            nextDx = 1; nextDy = 0;
            snakeScore = 0;
            spawnFood();
            document.getElementById('gameTitle').innerText = 'FOOD SNAKE';
        }

        function spawnFood() {
            const cols = Math.floor(canvasWidth / snakeGrid);
            const rows = Math.floor(canvasHeight / snakeGrid);
            food.x = Math.floor(Math.random() * (cols - 4)) + 2;
            food.y = Math.floor(Math.random() * (rows - 4)) + 2;
            food.color = ['#ff4500', '#32cd32', '#ffff00'][Math.floor(Math.random() * 3)]; // Đỏ, Xanh lá, Vàng
        }

        function updateSnakeGame() {
            snakeDx = nextDx;
            snakeDy = nextDy;
            const head = { x: snake[0].x + snakeDx, y: snake[0].y + snakeDy };
            const cols = Math.floor(canvasWidth / snakeGrid);
            const rows = Math.floor(canvasHeight / snakeGrid);

            if (head.x < 0) head.x = cols - 1;
            if (head.x >= cols) head.x = 0;
            if (head.y < 0) head.y = rows - 1;
            if (head.y >= rows) head.y = 0;

            snake.unshift(head);

            if (Math.abs(head.x - food.x) < 1 && Math.abs(head.y - food.y) < 1) {
                snakeScore++;
                spawnFood();
                createExplosion(head.x * snakeGrid + snakeGrid / 2, head.y * snakeGrid + snakeGrid / 2, 8, food.color, 0.8);
            } else {
                snake.pop();
            }
        }

        function drawSnakeGame() {
            // Mồi (Nguyên liệu)
            ctx.shadowBlur = 15;
            ctx.shadowColor = food.color;
            ctx.fillStyle = food.color;
            ctx.beginPath();
            ctx.arc(food.x * snakeGrid + snakeGrid / 2, food.y * snakeGrid + snakeGrid / 2, snakeGrid / 2 - 3, 0, Math.PI * 2);
            ctx.fill();
            ctx.shadowBlur = 0;

            // Rắn
            snake.forEach((s, i) => {
                const cx = s.x * snakeGrid + snakeGrid / 2;
                const cy = s.y * snakeGrid + snakeGrid / 2;
                if (i === 0) {
                    ctx.fillStyle = '#FFD700'; // Đầu vàng
                    ctx.shadowBlur = 15;
                    ctx.shadowColor = '#FFA500';
                } else {
                    ctx.fillStyle = '#cd853f'; // Thân nâu
                    ctx.shadowBlur = 0;
                }
                ctx.beginPath();
                ctx.arc(cx, cy, snakeGrid / 2 - (i == 0 ? 0 : 1), 0, Math.PI * 2);
                ctx.fill();

                if (i === 0) { // Mắt
                    ctx.shadowBlur = 0;
                    ctx.fillStyle = '#000';
                    ctx.beginPath();
                    ctx.arc(cx + (snakeDy !== 0 ? -3 : 4), cy + (snakeDx !== 0 ? -3 : 4), 2.5, 0, Math.PI * 2);
                    ctx.arc(cx + (snakeDy !== 0 ? 3 : 4), cy + (snakeDx !== 0 ? 3 : 4), 2.5, 0, Math.PI * 2);
                    ctx.fill();
                }
            });

            ctx.shadowBlur = 0;
            ctx.fillStyle = '#fff';
            ctx.font = '16px "Press Start 2P"';
            ctx.textAlign = 'left';
            ctx.fillText(`SCORE: ${snakeScore}`, 30, 30);
        }

        // GAME 2: KITCHEN DEFENSE (Shooter)
        let ship = { x: 0, y: 0, w: 40, h: 40, speed: 8 };
        let bullets = [];
        let eggs = [];
        let chickens = [];
        let chickenDir = 1;
        let chickenSpeed = 2;
        let shooterState = 'playing';

        function initShooter() {
            ship.x = canvasWidth / 2;
            ship.y = canvasHeight - 60;
            bullets = [];
            eggs = [];
            chickens = [];
            shooterState = 'playing';
            createChickenGrid();
        }

        function createChickenGrid() {
            const rows = 3;
            const cols = 8;
            const startX = (canvasWidth - (cols * 60)) / 2;
            const startY = 50;
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    chickens.push({
                        x: startX + c * 60,
                        y: startY + r * 50,
                        w: 40,
                        h: 30,
                        color: r === 0 ? '#FF4444' : (r === 1 ? '#FFAA00' : '#FFFF00'),
                    });
                }
            }
        }

        function fireBullet() {
            if (shooterState !== 'playing') return;
            if (bullets.length < 5) bullets.push({
                x: ship.x,
                y: ship.y - 20,
                r: 4,
                speed: 10,
                color: '#fff' // Đạn trắng
            });
        }

        function updateShooter() {
            if (shooterState === 'win') {
                spawnVictoryFireworks();
                return;
            }
            if (shooterState === 'gameover') return;

            // Bullets
            for (let i = bullets.length - 1; i >= 0; i--) {
                bullets[i].y -= bullets[i].speed;
                if (bullets[i].y < 0) bullets.splice(i, 1);
            }

            // Eggs (Giờ là thức ăn rơi)
            for (let i = eggs.length - 1; i >= 0; i--) {
                eggs[i].y += eggs[i].speed;
                if (Math.abs(eggs[i].x - ship.x) < 20 && Math.abs(eggs[i].y - ship.y) < 20) {
                    shooterState = 'gameover';
                    createExplosion(ship.x, ship.y, 40, '#ff4500', 2);
                }
                if (eggs[i].y > canvasHeight) eggs.splice(i, 1);
            }

            // Chickens
            let hitWall = false;
            chickens.forEach(c => {
                c.x += chickenSpeed * chickenDir;
                if (c.x < 20 || c.x > canvasWidth - 20) hitWall = true;
            });
            if (hitWall) {
                chickenDir *= -1;
                chickens.forEach(c => c.y += 20);
            }

            // Collision
            for (let i = chickens.length - 1; i >= 0; i--) {
                if (Math.abs(chickens[i].x - ship.x) < 30 && Math.abs(chickens[i].y - ship.y) < 30) shooterState = 'gameover';

                for (let j = bullets.length - 1; j >= 0; j--) {
                    if (Math.abs(bullets[j].x - chickens[i].x) < 25 && Math.abs(bullets[j].y - chickens[i].y) < 20) {
                        createExplosion(chickens[i].x, chickens[i].y, 10, chickens[i].color, 1);
                        chickens.splice(i, 1);
                        bullets.splice(j, 1);
                        break;
                    }
                }
            }

            if (Math.random() < 0.015 && chickens.length > 0) {
                const c = chickens[Math.floor(Math.random() * chickens.length)];
                eggs.push({ x: c.x, y: c.y + 15, speed: 3 });
            }
            if (chickens.length === 0) shooterState = 'win';
        }

        function drawShooterGame() {
            if (shooterState !== 'gameover') {
                // Vẽ tàu (Mũ bếp)
                ctx.fillStyle = '#fff';
                ctx.shadowBlur = 10;
                ctx.shadowColor = '#fff';
                ctx.beginPath();
                ctx.moveTo(ship.x, ship.y - 25);
                ctx.lineTo(ship.x - 20, ship.y + 15);
                ctx.lineTo(ship.x + 20, ship.y + 15);
                ctx.fill();
                ctx.shadowBlur = 0;
            }

            ctx.fillStyle = '#fff';
            bullets.forEach(b => {
                ctx.beginPath();
                ctx.arc(b.x, b.y, b.r, 0, Math.PI * 2);
                ctx.fill();
            });

            chickens.forEach(c => {
                ctx.fillStyle = c.color;
                ctx.beginPath();
                ctx.arc(c.x, c.y, 15, 0, Math.PI * 2); // Body
                ctx.fill();
            });

            ctx.fillStyle = '#ff4500';
            eggs.forEach(e => {
                ctx.beginPath();
                ctx.arc(e.x, e.y, 5, 0, Math.PI * 2);
                ctx.fill();
            });

            if (shooterState === 'gameover') {
                ctx.fillStyle = 'red';
                ctx.font = '40px "Press Start 2P"';
                ctx.textAlign = 'center';
                ctx.fillText("CHÁY BẾP!", canvasWidth / 2, canvasHeight * 0.25);
                ctx.font = '15px "Press Start 2P"';
                ctx.fillStyle = '#FFF';
                ctx.fillText("BẤM 'R' ĐỂ NẤU LẠI", canvasWidth / 2, canvasHeight * 0.85);
            } else if (shooterState === 'win') {
                ctx.fillStyle = '#32cd32';
                ctx.font = '40px "Press Start 2P"';
                ctx.textAlign = 'center';
                ctx.fillText("HOÀN THÀNH!", canvasWidth / 2, canvasHeight * 0.25);
                ctx.font = '15px "Press Start 2P"';
                ctx.fillStyle = '#FFF';
                ctx.fillText("BẤM 'R' ĐỂ TIẾP TỤC", canvasWidth / 2, canvasHeight * 0.85);
            }
        }

        // --- CONTROLS ---
        let keys = {};
        document.addEventListener('keydown', e => {
            keys[e.key.toLowerCase()] = true;
            if (e.code === 'Space' && currentGame === 'shooter') fireBullet();
            if (e.key.toLowerCase() === 'r' && currentGame === 'shooter') initShooter();

            if (currentGame === 'snake') {
                if (e.key === 'a' && snakeDx === 0) { nextDx = -1; nextDy = 0; }
                if (e.key === 'w' && snakeDy === 0) { nextDx = 0; nextDy = -1; }
                if (e.key === 'd' && snakeDx === 0) { nextDx = 1; nextDy = 0; }
                if (e.key === 's' && snakeDy === 0) { nextDx = 0; nextDy = 1; }
            }
        });
        document.addEventListener('keyup', e => delete keys[e.key.toLowerCase()]);

        function handleShooterInput() {
            if (shooterState !== 'playing') return;
            if (keys['a'] && ship.x > 20) ship.x -= ship.speed;
            if (keys['d'] && ship.x < canvasWidth - 20) ship.x += ship.speed;
        }

        // --- MAIN LOOP ---
        let lastTime = 0;
        function loop(timestamp) {
            requestAnimationFrame(loop);
            const dt = timestamp - lastTime;
            lastTime = timestamp;

            ctx.globalCompositeOperation = 'destination-out';
            ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
            ctx.fillRect(0, 0, canvasWidth, canvasHeight);
            ctx.globalCompositeOperation = 'source-over';

            if (currentGame === 'snake') {
                snakeAccumulator += dt;
                if (snakeAccumulator > 1000 / snakeSpeed) {
                    updateSnakeGame();
                    snakeAccumulator = 0;
                }
                drawSnakeGame();
            } else {
                handleShooterInput();
                updateShooter();
                drawShooterGame();
            }

            ctx.globalCompositeOperation = 'lighter';
            for (let i = fireworks.length - 1; i >= 0; i--) {
                fireworks[i].update();
                fireworks[i].draw();
                if (fireworks[i].alpha <= 0) fireworks.splice(i, 1);
            }
            ctx.globalCompositeOperation = 'source-over';
            drawMouseLights();
        }

        // --- MOUSE LIGHT EFFECT (Lửa bếp) ---
        const mouseLights = [];
        document.addEventListener('mousemove', e => {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            mouseLights.push({
                x: x, y: y, radius: 8 + Math.random() * 4,
                alpha: 0.15 + Math.random() * 0.1,
                color: '#FF8C00' // Cam đậm
            });
        });

        function drawMouseLights() {
            for (let i = mouseLights.length - 1; i >= 0; i--) {
                const light = mouseLights[i];
                ctx.save();
                ctx.globalAlpha = light.alpha;
                ctx.fillStyle = light.color;
                ctx.shadowBlur = 10;
                ctx.shadowColor = light.color;
                ctx.beginPath();
                ctx.arc(light.x, light.y, light.radius, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
                light.alpha -= 0.01;
                light.radius *= 0.95;
                if (light.alpha <= 0) mouseLights.splice(i, 1);
            }
        }

        // Start
        initSnake();
        requestAnimationFrame(loop);
    </script>

</body>
</html>