<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trang truy cập nhanh</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Press+Start+2P&display=swap');
    
    html, body {
        margin: 0; padding: 0; height: 100%; overflow: hidden;
        background: #050505;
        display: flex; justify-content: center; align-items: center;
        font-family: 'Poppins', sans-serif;
    }

    /* --- ARCADE CABINET --- */
    .arcade-cabinet {
        position: relative;
        width: 90vw; height: 90vh; max-width: 1200px;
        background: #222;
        border: 15px solid #111;
        border-radius: 30px;
        box-shadow: 0 0 60px rgba(255, 215, 0, 0.1), inset 0 0 100px #000;
        display: flex; flex-direction: column;
        padding: 20px;
        box-sizing: border-box;
    }

    .arcade-header {
        text-align: center; color: #FFD700;
        font-family: 'Press Start 2P', cursive;
        text-shadow: 0 0 10px #FFD700, 4px 4px 0 #b8860b;
        margin-bottom: 15px; font-size: 24px;
        border: 2px solid #FFD700;
        padding: 12px; border-radius: 10px;
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.4);
        animation: neonFlicker 3s infinite;
        text-transform: uppercase;
    }

    .screen-bezel {
        flex-grow: 1; position: relative;
        background: #0d0d0d;
        border-radius: 20px;
        border: 8px solid #333;
        box-shadow: inset 0 0 60px #000;
        overflow: hidden;
    }

    .screen-glare {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, transparent 40%);
        pointer-events: none; z-index: 10;
    }

    .controls-panel {
        height: 90px; margin-top: 15px;
        background: #1a1a1a; border-top: 4px solid #333;
        border-radius: 0 0 15px 15px;
        display: flex; justify-content: space-between; align-items: center;
        padding: 0 50px;
    }
    
    .d-pad {
        width: 100px; height: 100px; position: relative;
    }
    .d-pad::before, .d-pad::after {
        content: ''; position: absolute; background: #333; border-radius: 4px;
        box-shadow: 0 2px 0 #000;
    }
    .d-pad::before { top: 35px; left: 0; width: 100px; height: 30px; }
    .d-pad::after { top: 0; left: 35px; width: 30px; height: 100px; }

    .action-btns { display: flex; gap: 25px; }
    .btn-arcade { 
        width: 45px; height: 45px; border-radius: 50%; 
        border: 3px solid rgba(0,0,0,0.2);
        position: relative; cursor: pointer; transition: transform 0.1s;
    }
    .btn-arcade:active { transform: scale(0.95); box-shadow: inset 0 0 10px rgba(0,0,0,0.5); }
    .btn-a { background: #ff0055; box-shadow: 0 6px 0 #990033; }
    .btn-b { background: #FFD700; box-shadow: 0 6px 0 #b8860b; }

    @keyframes neonFlicker {
        0%, 18%, 22%, 25%, 53%, 57%, 100% { text-shadow: 0 0 10px #FFD700, 0 0 20px #FFD700; opacity: 1; }
        20%, 24%, 55% { text-shadow: none; opacity: 0.5; }
    }

    /* --- DASHBOARD --- */
    .dashboard-wrapper {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        display: flex; justify-content: center; align-items: center;
        z-index: 5; pointer-events: none;
    }
    .dashboard-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 25px; width: 80%; max-width: 900px; justify-items: center;
        pointer-events: auto;
    }
    .dashboard-card {
        display:flex; flex-direction:column; justify-content:center; align-items:center;
        border-radius:16px; padding:20px 10px; width:100%; max-width:140px;
        text-align:center; color:#fff; text-decoration:none;
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
    .dashboard-card i { margin-bottom:10px; }
    .dashboard-card span { font-weight:600; font-size:0.85rem; letter-spacing: 0.5px; }

    #snakeCanvas { width: 100%; height: 100%; display: block; }

    .tutorial {
        position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
        color: #666; font-size: 12px; font-family: 'Press Start 2P';
        z-index: 4; text-align: center; opacity: 0.7;
    }

    @media (max-width: 768px) {
        .arcade-cabinet { width: 100%; height: 100%; border: none; border-radius: 0; }
        .controls-panel { display: none; }
        .dashboard-grid { gap: 15px; }
    }
</style>
</head>
<body>

<div class="arcade-cabinet">
    <div class="arcade-header">CYBER SNAKE</div>
    
    <div class="screen-bezel">
        <div class="screen-glare"></div>
        <canvas id="snakeCanvas"></canvas>

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
        
        <div class="tutorial">USE [W,A,S,D] TO MOVE</div>
    </div>

    <div class="controls-panel">
        <div class="d-pad"></div>
        <div class="action-btns">
            <div class="btn-arcade btn-a"></div>
            <div class="btn-arcade btn-b"></div>
        </div>
    </div>
</div>

<script>
const canvas = document.getElementById('snakeCanvas');
const ctx = canvas.getContext('2d');

function resizeCanvas() {
    const container = document.querySelector('.screen-bezel');
    canvas.width = container.clientWidth;
    canvas.height = container.clientHeight;
}
window.addEventListener('resize', resizeCanvas);
resizeCanvas();

// --- GAME CONFIG ---
const grid = 25; 
const snakeSpeed = 12; 
const maxScore = 100; 

// --- STATE ---
let snake = [{x: 10, y: 10}, {x: 9, y: 10}, {x: 8, y: 10}];
let dx = 1; 
let dy = 0; 
let nextDx = 1; 
let nextDy = 0;

let food = {x: 15, y: 10, color: '#FF0055'};
let score = 0;
let fireworks = [];
let lastTime = 0;
let accumulator = 0; 

// --- CONTROLS ---
document.addEventListener('keydown', (e) => {
    if(["ArrowUp","ArrowDown","ArrowLeft","ArrowRight"].indexOf(e.code) > -1) e.preventDefault();

    switch(e.key.toLowerCase()) {
        case 'a': case 'arrowleft': if (dx === 0) { nextDx = -1; nextDy = 0; } break;
        case 'w': case 'arrowup': if (dy === 0) { nextDx = 0; nextDy = -1; } break;
        case 'd': case 'arrowright': if (dx === 0) { nextDx = 1; nextDy = 0; } break;
        case 's': case 'arrowdown': if (dy === 0) { nextDx = 0; nextDy = 1; } break;
    }
});

// --- GAME LOGIC ---
function spawnFood() {
    const cols = Math.floor(canvas.width / grid);
    const rows = Math.floor(canvas.height / grid);
    food.x = Math.floor(Math.random() * (cols - 4)) + 2;
    food.y = Math.floor(Math.random() * (rows - 4)) + 2;
    
    const colors = ['#ff0055', '#00ffff', '#ffcc00', '#adff2f'];
    food.color = colors[Math.floor(Math.random() * colors.length)];
}

function updateSnake() {
    dx = nextDx; dy = nextDy;
    const head = { x: snake[0].x + dx, y: snake[0].y + dy };

    // Wrap-around
    const cols = Math.floor(canvas.width / grid);
    const rows = Math.floor(canvas.height / grid);
    
    if (head.x < 0) head.x = cols - 1;
    if (head.x >= cols) head.x = 0;
    if (head.y < 0) head.y = rows - 1;
    if (head.y >= rows) head.y = 0;

    snake.unshift(head);

    // Ăn mồi
    if (Math.abs(head.x - food.x) < 1 && Math.abs(head.y - food.y) < 1) {
        score++;
        spawnFood();
        
        // Hiệu ứng nổ nhẹ khi ăn (Tối ưu hóa: ít hạt, không shadow)
        createExplosion(head.x * grid + grid/2, head.y * grid + grid/2, 5, food.color, 0.8);
        
        // Logic pháo hoa
        if (score === 100) {
            spawnGiantFireworks();
        } else if (score > 0 && score % 10 === 0) {
            spawnBigFireworks();
        }

    } else {
        snake.pop();
    }
}

// --- RENDERING ---
function drawSnake() {
    snake.forEach((segment, index) => {
        const x = segment.x * grid;
        const y = segment.y * grid;
        const centerX = x + grid / 2;
        const centerY = y + grid / 2;

        // BỎ Shadow nặng nề ở thân rắn
        const gradient = ctx.createRadialGradient(centerX, centerY, 2, centerX, centerY, grid/2);
        
        if (index === 0) {
            // ĐẦU RẮN (Vẫn giữ đẹp)
            gradient.addColorStop(0, '#fff');
            gradient.addColorStop(1, '#FFD700');
            ctx.shadowBlur = 15; // Chỉ đầu rắn mới có shadow
            ctx.shadowColor = '#FFA500';
        } else {
            // THÂN RẮN (Tối ưu: Không shadow)
            const sizeRatio = 1 - (index / (snake.length + 5)) * 0.4; 
            const radius = (grid / 2) * sizeRatio;
            
            gradient.addColorStop(0, '#FFD700');
            gradient.addColorStop(1, '#b8860b'); 
            ctx.shadowBlur = 0; // Tắt shadow cho thân
            
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
            ctx.fillStyle = gradient;
            ctx.fill();
            return; 
        }

        ctx.beginPath();
        ctx.arc(centerX, centerY, grid / 2, 0, Math.PI * 2);
        ctx.fillStyle = gradient;
        ctx.fill();

        // Mắt
        ctx.shadowBlur = 0;
        ctx.fillStyle = '#000';
        let eyeOffsetX = (dx * 4) || 4, eyeOffsetY = (dy * 4) || 4;
        if (dx === -1) eyeOffsetX = -4;
        if (dy === -1) eyeOffsetY = -4;

        ctx.beginPath();
        ctx.arc(centerX + (dy!==0 ? -3 : eyeOffsetX), centerY + (dx!==0 ? -3 : eyeOffsetY), 2.5, 0, Math.PI*2);
        ctx.arc(centerX + (dy!==0 ? 3 : eyeOffsetX), centerY + (dx!==0 ? 3 : eyeOffsetY), 2.5, 0, Math.PI*2);
        ctx.fill();
    });
    ctx.shadowBlur = 0;
}

function drawFood() {
    const cx = food.x * grid + grid / 2;
    const cy = food.y * grid + grid / 2;
    const pulse = Math.sin(Date.now() / 200) * 2;
    
    ctx.shadowBlur = 15; // Mồi vẫn cần sáng
    ctx.shadowColor = food.color;
    ctx.fillStyle = food.color;
    
    ctx.beginPath();
    ctx.arc(cx, cy, (grid/2 - 3) + pulse, 0, Math.PI * 2);
    ctx.fill();
    ctx.shadowBlur = 0;
}

function drawScore() {
    ctx.fillStyle = '#fff';
    ctx.font = '16px "Press Start 2P"';
    ctx.textAlign = 'left';
    ctx.textBaseline = 'top';
    ctx.fillText(`SCORE: ${score}/100`, 30, 30);
}

// --- OPTIMIZED FIREWORKS SYSTEM ---
class Particle {
    constructor(x, y, color, speedScale = 1) {
        this.x = x; this.y = y;
        this.color = color;
        const angle = Math.random() * Math.PI * 2;
        // Tốc độ ngẫu nhiên hơn
        const speed = Math.random() * 6 * speedScale;
        this.vx = Math.cos(angle) * speed;
        this.vy = Math.sin(angle) * speed;
        this.alpha = 1;
        this.decay = Math.random() * 0.02 + 0.015; // Tan biến nhanh hơn xíu để đỡ lag
        this.gravity = 0.08;
    }
    update() {
        this.x += this.vx;
        this.y += this.vy;
        this.vy += this.gravity;
        this.alpha -= this.decay;
    }
    draw() {
        ctx.save();
        ctx.globalAlpha = this.alpha;
        ctx.fillStyle = this.color;
        
        // TỐI ƯU QUAN TRỌNG: Không dùng shadowBlur cho từng hạt pháo hoa
        // Thay vào đó dùng globalCompositeOperation 'lighter' ở vòng lặp chính
        
        ctx.beginPath();
        ctx.arc(this.x, this.y, 2.5, 0, Math.PI * 2); // Hạt to hơn chút để bù cho việc mất shadow
        ctx.fill();
        ctx.restore();
    }
}

function createExplosion(x, y, count, color, speedScale = 1) {
    // Giới hạn số lượng hạt tối đa để tránh crash
    const safeCount = Math.min(count, 100); 
    for(let i=0; i<safeCount; i++) {
        fireworks.push(new Particle(x, y, color || '#FFD700', speedScale));
    }
}

function spawnBigFireworks() {
    // Pháo hoa mỗi 10 điểm: Giảm số lượng hạt xuống mức hợp lý (40-50)
    const colors = ['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#00FFFF', '#FF00FF'];
    let shots = 0;
    const interval = setInterval(() => {
        const cx = Math.random() * canvas.width;
        const cy = Math.random() * canvas.height * 0.7;
        createExplosion(cx, cy, 40, colors[Math.floor(Math.random() * colors.length)]);
        shots++;
        if(shots > 3) clearInterval(interval);
    }, 300);
}

function spawnGiantFireworks() {
    // Pháo hoa khổng lồ: Vẫn hoành tráng nhưng chia nhỏ đợt bắn
    const colors = ['#FFD700', '#FFFFFF', '#FF0055', '#00FFFF'];
    let shots = 0;
    const interval = setInterval(() => {
        const cx = Math.random() * canvas.width;
        const cy = Math.random() * canvas.height * 0.8;
        // Giảm số hạt mỗi lần nổ (80 thay vì 200) nhưng nổ nhiều lần
        createExplosion(cx, cy, 80, colors[Math.floor(Math.random() * colors.length)], 1.2);
        shots++;
        if(shots > 10) clearInterval(interval);
    }, 250);
}

// --- MAIN LOOP ---
function loop(timestamp) {
    requestAnimationFrame(loop);

    const deltaTime = timestamp - lastTime;
    accumulator += deltaTime;
    if (accumulator > 1000 / snakeSpeed) {
        updateSnake();
        accumulator = 0;
    }
    lastTime = timestamp;

    // Clear màn hình
    ctx.globalCompositeOperation = 'destination-out';
    ctx.fillStyle = 'rgba(0, 0, 0, 0.2)'; 
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Vẽ bình thường
    ctx.globalCompositeOperation = 'source-over';
    drawFood();
    drawSnake();
    drawScore();

    // Vẽ pháo hoa chế độ Lighter (Giả lập phát sáng mà không cần shadow nặng)
    ctx.globalCompositeOperation = 'lighter'; 
    for (let i = fireworks.length - 1; i >= 0; i--) {
        fireworks[i].update();
        fireworks[i].draw();
        if (fireworks[i].alpha <= 0) fireworks.splice(i, 1);
    }
    
    // Reset về mặc định để vẽ vòng sau
    ctx.globalCompositeOperation = 'source-over';
}

spawnFood();
requestAnimationFrame(loop);

</script>

</body>
</html>