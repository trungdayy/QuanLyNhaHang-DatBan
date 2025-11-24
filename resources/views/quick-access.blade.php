<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcade Multi-Game Fixed</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Press+Start+2P&display=swap');

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            background: #050505;
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
            background: #222;
            border: 15px solid #111;
            border-radius: 30px;
            box-shadow: 0 0 60px rgba(255, 215, 0, 0.1), inset 0 0 100px #000;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-sizing: border-box;
        }

        .arcade-header {
            text-align: center;
            color: #FFD700;
            font-family: 'Press Start 2P', cursive;
            text-shadow: 0 0 10px #FFD700, 4px 4px 0 #b8860b;
            margin-bottom: 15px;
            font-size: 24px;
            border: 2px solid #FFD700;
            padding: 12px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.4);
            animation: neonFlicker 3s infinite;
            text-transform: uppercase;
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
            background: #333;
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
            background: #ff0055;
            box-shadow: 0 6px 0 #990033;
        }

        .btn-b {
            background: #FFD700;
            box-shadow: 0 6px 0 #b8860b;
        }

        @keyframes neonFlicker {

            0%,
            18%,
            22%,
            25%,
            53%,
            57%,
            100% {
                text-shadow: 0 0 10px #FFD700, 0 0 20px #FFD700;
                opacity: 1;
            }

            20%,
            24%,
            55% {
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
            /* Quan trọng: Để click xuyên qua vùng trống */
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 25px;
            width: 80%;
            max-width: 900px;
            justify-items: center;
            pointer-events: auto;
            /* Bật lại click cho các nút */
        }

        .dashboard-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 16px;
            padding: 20px 10px;
            width: 100%;
            max-width: 140px;
            text-align: center;
            color: #fff;
            text-decoration: none;
            background: rgba(16, 16, 16, 0.7);
            border: 1px solid rgba(255, 215, 0, 0.2);
            backdrop-filter: blur(4px);
            transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .dashboard-card:hover {
            transform: translateY(-10px) scale(1.1);
            background: linear-gradient(135deg, #FFD700, #ff8c00);
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.6);
            border-color: #fff;
            color: #000;
        }

        .dashboard-card i {
            margin-bottom: 10px;
        }

        .dashboard-card span {
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        canvas {
            width: 100%;
            height: 100%;
            display: block;
        }

        .tutorial {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #666;
            font-size: 12px;
            font-family: 'Press Start 2P';
            z-index: 4;
            text-align: center;
            opacity: 0.7;
            pointer-events: none;
        }

        /* --- SWITCH GAME BUTTON --- */
        .switch-game-btn {
            position: absolute;
            bottom: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, #00ffcc, #008866);
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 0 20px #00ffcc;
            cursor: pointer;
            z-index: 20;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.2s;
        }

        .switch-game-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 40px #00ffcc;
        }

        .switch-game-btn i {
            color: #000;
            font-size: 24px;
        }

        @media (max-width: 768px) {
            .arcade-cabinet {
                width: 100%;
                height: 100%;
                border: none;
                border-radius: 0;
            }

            .controls-panel {
                display: none;
            }

            .dashboard-grid {
                gap: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="arcade-cabinet">
        <div class="arcade-header" id="gameTitle">CYBER SNAKE</div>

        <div class="screen-bezel">
            <div class="screen-glare"></div>
            <canvas id="gameCanvas"></canvas>

            <div class="dashboard-wrapper">
                <div class="dashboard-grid">
                    <a href="{{ route('admin.dashboard') }}" target="_blank" class="dashboard-card card-admin">
                        <i class="fa fa-cogs fa-3x"></i><span>Admin</span>
                    </a>
                    <a href="{{ route('home') }}" target="_blank" class="dashboard-card card-client">
                        <i class="fa fa-home fa-3x"></i><span>Client</span>
                    </a>
                    <a href="{{ route('nhanVien.ban-an.index') }}" target="_blank" class="dashboard-card card-nv">
                        <i class="fa fa-user fa-3x"></i><span>Nhân viên</span>
                    </a>
                    <a href="{{ route('bep.dashboard') }}" target="_blank" class="dashboard-card card-bep">
                        <i class="fa fa-utensils fa-3x"></i><span>Bếp</span>
                    </a>
                    <a href="{{ route('oderqr.list') }}" target="_blank" class="dashboard-card card-qr">
                        <i class="fa fa-qrcode fa-3x"></i><span>QR List</span>
                    </a>
                </div>
            </div>

            <div class="tutorial" id="gameTutorial">USE [W,A,S,D] TO MOVE</div>
        </div>

        <div class="controls-panel">
            <div class="d-pad"></div>
            <div class="action-btns">
                <div class="btn-arcade btn-a" onclick="if(currentGame==='shooter') fireBullet()"></div>
                <div class="btn-arcade btn-b"></div>
            </div>
        </div>

        <div class="switch-game-btn" onclick="switchGame()" title="Switch Game">
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
                document.getElementById('gameTitle').innerText = 'SPACE CHICKEN';
                document.getElementById('gameTutorial').innerText = '[A,D] MOVE | [SPACE] SHOOT | [R] RESTART';
                initShooter();
            } else {
                currentGame = 'snake';
                document.getElementById('gameTitle').innerText = 'CYBER SNAKE';
                document.getElementById('gameTutorial').innerText = 'USE [W,A,S,D] TO MOVE';
                initSnake();
            }
        }

        // ==========================================
        // UTILS
        // ==========================================
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
                const c = ['#F00', '#0F0', '#00F', '#FF0', '#0FF', '#F0F'];
                createExplosion(Math.random() * canvasWidth, Math.random() * canvasHeight / 2, 40, c[Math.floor(Math
                .random() * c.length)], 1.2);
            }
        }

        // ==========================================
        // GAME 1: SNAKE
        // ==========================================
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
            snake = [{
                x: 10,
                y: 10
            }, {
                x: 9,
                y: 10
            }, {
                x: 8,
                y: 10
            }];
            snakeDx = 1;
            snakeDy = 0;
            nextDx = 1;
            nextDy = 0;
            snakeScore = 0;
            spawnFood();
        }

        function spawnFood() {
            // Trừ biên 2 ô để không bị khuất
            const cols = Math.floor(canvasWidth / snakeGrid);
            const rows = Math.floor(canvasHeight / snakeGrid);
            food.x = Math.floor(Math.random() * (cols - 4)) + 2;
            food.y = Math.floor(Math.random() * (rows - 4)) + 2;
            food.color = ['#ff0055', '#00ffff', '#ffcc00'][Math.floor(Math.random() * 3)];
        }

        function updateSnakeGame() {
            snakeDx = nextDx;
            snakeDy = nextDy;
            const head = {
                x: snake[0].x + snakeDx,
                y: snake[0].y + snakeDy
            };
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
            // Mồi
            ctx.shadowBlur = 15;
            ctx.shadowColor = food.color;
            ctx.fillStyle = food.color;
            ctx.beginPath();
            ctx.arc(food.x * snakeGrid + snakeGrid / 2, food.y * snakeGrid + snakeGrid / 2, snakeGrid / 2 - 3, 0, Math.PI *
                2);
            ctx.fill();
            ctx.shadowBlur = 0;

            // Rắn
            snake.forEach((s, i) => {
                const cx = s.x * snakeGrid + snakeGrid / 2;
                const cy = s.y * snakeGrid + snakeGrid / 2;
                if (i === 0) {
                    ctx.fillStyle = '#FFD700';
                    ctx.shadowBlur = 15;
                    ctx.shadowColor = '#FFA500';
                } else {
                    ctx.fillStyle = '#b8860b';
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

        // ==========================================
        // GAME 2: SPACE CHICKEN
        // ==========================================
        let ship = {
            x: 0,
            y: 0,
            w: 40,
            h: 40,
            speed: 8
        };
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
                color: '#00FFFF'
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

            // Eggs
            for (let i = eggs.length - 1; i >= 0; i--) {
                eggs[i].y += eggs[i].speed;
                if (Math.abs(eggs[i].x - ship.x) < 20 && Math.abs(eggs[i].y - ship.y) < 20) {
                    shooterState = 'gameover';
                    createExplosion(ship.x, ship.y, 40, '#00FFFF', 2);
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
                if (Math.abs(chickens[i].x - ship.x) < 30 && Math.abs(chickens[i].y - ship.y) < 30) shooterState =
                    'gameover';

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
                eggs.push({
                    x: c.x,
                    y: c.y + 15,
                    speed: 3
                });
            }
            if (chickens.length === 0) shooterState = 'win';
        }

        function drawShooterGame() {
            if (shooterState !== 'gameover') {
                ctx.fillStyle = '#00FFFF';
                ctx.shadowBlur = 10;
                ctx.shadowColor = '#00FFFF';
                ctx.beginPath();
                ctx.moveTo(ship.x, ship.y - 20);
                ctx.lineTo(ship.x - 15, ship.y + 15);
                ctx.lineTo(ship.x + 15, ship.y + 15);
                ctx.fill();
                ctx.shadowBlur = 0;
            }

            ctx.fillStyle = '#00FFFF';
            bullets.forEach(b => {
                ctx.beginPath();
                ctx.arc(b.x, b.y, b.r, 0, Math.PI * 2);
                ctx.fill();
            });

            chickens.forEach(c => {
                ctx.fillStyle = c.color;
                ctx.beginPath();
                ctx.arc(c.x, c.y, 15, 0, Math.PI * 2);
                ctx.fill();
                ctx.beginPath();
                ctx.arc(c.x - 18, c.y - 5, 8, 0, Math.PI * 2);
                ctx.fill();
                ctx.beginPath();
                ctx.arc(c.x + 18, c.y - 5, 8, 0, Math.PI * 2);
                ctx.fill();
                ctx.fillStyle = '#000';
                ctx.beginPath();
                ctx.arc(c.x - 5, c.y - 2, 2, 0, Math.PI * 2);
                ctx.arc(c.x + 5, c.y - 2, 2, 0, Math.PI * 2);
                ctx.fill();
            });

            ctx.fillStyle = '#FFF';
            eggs.forEach(e => {
                ctx.beginPath();
                ctx.arc(e.x, e.y, 5, 0, Math.PI * 2);
                ctx.fill();
            });

            if (shooterState === 'gameover') {
                // --- ĐẨY CHỮ LÊN TRÊN (Top 25%) ---
                ctx.fillStyle = 'red';
                ctx.font = '40px "Press Start 2P"';
                ctx.textAlign = 'center';
                ctx.fillText("GAME OVER", canvasWidth / 2, canvasHeight * 0.25);

                // --- ĐẨY CHỮ XUỐNG DƯỚI (Bottom 85%) ---
                ctx.font = '15px "Press Start 2P"';
                ctx.fillStyle = '#FFF';
                ctx.fillText("PRESS 'R' TO RESTART", canvasWidth / 2, canvasHeight * 0.85);
            } else if (shooterState === 'win') {
                // --- ĐẨY CHỮ LÊN TRÊN ---
                ctx.fillStyle = 'green';
                ctx.font = '40px "Press Start 2P"';
                ctx.textAlign = 'center';
                ctx.fillText("YOU WIN!", canvasWidth / 2, canvasHeight * 0.25);

                // --- ĐẨY CHỮ XUỐNG DƯỚI ---
                ctx.font = '15px "Press Start 2P"';
                ctx.fillStyle = '#FFF';
                ctx.fillText("PRESS 'R' TO RESTART", canvasWidth / 2, canvasHeight * 0.85);
            }
        }

        // --- CONTROLS ---
        let keys = {};
        document.addEventListener('keydown', e => {
            keys[e.key.toLowerCase()] = true;
            if (e.code === 'Space' && currentGame === 'shooter') fireBullet();
            if (e.key.toLowerCase() === 'r' && currentGame === 'shooter') initShooter();

            if (currentGame === 'snake') {
                if (e.key === 'a' && snakeDx === 0) {
                    nextDx = -1;
                    nextDy = 0;
                }
                if (e.key === 'w' && snakeDy === 0) {
                    nextDx = 0;
                    nextDy = -1;
                }
                if (e.key === 'd' && snakeDx === 0) {
                    nextDx = 1;
                    nextDy = 0;
                }
                if (e.key === 's' && snakeDy === 0) {
                    nextDx = 0;
                    nextDy = 1;
                }
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
        }

        // Start
        initSnake();
        requestAnimationFrame(loop);
    </script>

</body>

</html>
