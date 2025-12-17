<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <title>Ping-Pong</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<style>
      *,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  background: radial-gradient(circle at top, #1f2937, #020617);
  color: #e5e7eb;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.game-container {
  background: rgba(15, 23, 42, 0.95);
  border-radius: 16px;
  padding: 20px 24px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.7);
  max-width: 960px;
  width: 100%;
}

.game-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.game-header h1 {
  font-size: 1.4rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #f9fafb;
}

.scoreboard {
  display: flex;
  gap: 16px;
  font-weight: 600;
}

.header-controls {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.difficulty {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
}

.difficulty label {
  color: #d1d5db;
}

.difficulty select,
.speed select {
  background: rgba(15, 23, 42, 0.9);
  color: #e5e7eb;
  border-radius: 999px;
  border: 1px solid rgba(148, 163, 184, 0.7);
  padding: 3px 10px;
  font-size: 0.85rem;
  outline: none;
}

.speed {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
}

.speed label {
  color: #d1d5db;
}

.scoreboard span {
  background: rgba(15, 23, 42, 0.9);
  padding: 4px 10px;
  border-radius: 999px;
  border: 1px solid rgba(148, 163, 184, 0.5);
}

.game-main {
  background: radial-gradient(circle at center, #020617 0, #020617 40%, #0b1120 100%);
  border-radius: 12px;
  padding: 10px;
  border: 1px solid rgba(148, 163, 184, 0.4);
}

canvas {
  display: block;
  width: 100%;
  height: auto;
  border-radius: 10px;
  background: radial-gradient(circle at center, #020617 0, #020617 40%, #020617 100%);
}

.game-footer {
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.controls {
  display: flex;
  gap: 8px;
}

.controls button {
  padding: 6px 14px;
  border-radius: 999px;
  border: 1px solid rgba(148, 163, 184, 0.7);
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: #022c22;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
}

.controls button:nth-child(2) {
  background: linear-gradient(135deg, #f97316, #ea580c);
  color: #111827;
}

.controls button:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
}

.controls button:active {
  transform: translateY(0);
  box-shadow: none;
}

.hint {
  font-size: 0.8rem;
  color: #9ca3af;
}

@media (max-width: 768px) {
  .game-container {
    margin: 10px;
    padding: 16px;
  }

  .game-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
  }

  .game-header h1 {
    font-size: 1.1rem;
  }

  .game-footer {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
  </head>
  <body>
    <div class="game-container">
      <header class="game-header">
        <h1>Ping-Pong</h1>
        <div class="scoreboard">
          <span>Игрок: <span id="playerScore">0</span></span>
          <span>AI: <span id="aiScore">0</span></span>
        </div>
        <div class="header-controls">
          <div class="difficulty">
            <label for="difficultySelect">Сложность:</label>
            <select id="difficultySelect">
              <option value="easy">Лёгкая</option>
              <option value="normal" selected>Нормальная</option>
              <option value="hard">Сложная</option>
            </select>
          </div>
          <div class="speed">
            <label for="speedSelect">Скорость:</label>
            <select id="speedSelect">
              <option value="slow">Низкая</option>
              <option value="normal" selected>Средняя</option>
              <option value="fast">Высокая</option>
            </select>
          </div>
        </div>
      </header>

      <main class="game-main">
        <canvas id="gameCanvas" width="640" height="380"></canvas>
      </main>

      <footer class="game-footer">
        <div class="controls">
          <button id="startPauseBtn">Старт</button>
          <button id="resetBtn">Сброс</button>
        </div>
        <p class="hint">
          Управление: <strong>W / S</strong> или <strong>↑ / ↓</strong>
        </p>
      </footer>
    </div>
<script language="javascript">
// Константы игры
const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");

let WIDTH = canvas.width;
let HEIGHT = canvas.height;

const PADDLE_WIDTH = 14;
const PADDLE_HEIGHT = 90;
const BALL_RADIUS = 8;

const PLAYER_X = 26;
let AI_X = WIDTH - 26 - PADDLE_WIDTH;

const PLAYER_SPEED = 7;

// Параметры сложностей AI
const AI_DIFFICULTY_PROFILES = {
  easy: {
    baseSpeed: 3.1,
    maxSpeed: 5.0,
    noiseAmplitude: 28,
  },
  normal: {
    baseSpeed: 4.2,
    maxSpeed: 7.0,
    noiseAmplitude: 18,
  },
  hard: {
    baseSpeed: 5.6,
    maxSpeed: 8.5,
    noiseAmplitude: 8,
  },
};

let currentDifficulty = "normal";
let aiProfile = AI_DIFFICULTY_PROFILES[currentDifficulty];

// Профили скорости мяча
const BALL_SPEED_PROFILES = {
  slow: {
    startSpeedX: 4.0,
    maxSpeedX: 7.0,
    accelStep: 0.35,
    spinFactor: 5,
  },
  normal: {
    startSpeedX: 5.5,
    maxSpeedX: 9.0,
    accelStep: 0.45,
    spinFactor: 6,
  },
  fast: {
    startSpeedX: 7.0,
    maxSpeedX: 11.0,
    accelStep: 0.6,
    spinFactor: 7,
  },
};

let currentSpeedProfile = "normal";
let ballProfile = BALL_SPEED_PROFILES[currentSpeedProfile];

// Состояние игры
let playerY = HEIGHT / 2 - PADDLE_HEIGHT / 2;
let aiY = HEIGHT / 2 - PADDLE_HEIGHT / 2;

let ballX = WIDTH / 2;
let ballY = HEIGHT / 2;
let ballSpeedX = ballProfile.startSpeedX;
let ballSpeedY = 3;

let playerScore = 0;
let aiScore = 0;

let isRunning = false;
let lastTime = 0;

// Ввод
const keys = {
  ArrowUp: false,
  ArrowDown: false,
  w: false,
  s: false,
};

document.addEventListener("keydown", (e) => {
  if (e.key in keys) {
    keys[e.key] = true;
  }
});

document.addEventListener("keyup", (e) => {
  if (e.key in keys) {
    keys[e.key] = false;
  }
});

// Кнопки управления
const startPauseBtn = document.getElementById("startPauseBtn");
const resetBtn = document.getElementById("resetBtn");
const playerScoreEl = document.getElementById("playerScore");
const aiScoreEl = document.getElementById("aiScore");
const difficultySelect = document.getElementById("difficultySelect");
const speedSelect = document.getElementById("speedSelect");

startPauseBtn.addEventListener("click", () => {
  isRunning = !isRunning;
  startPauseBtn.textContent = isRunning ? "Пауза" : "Старт";
  if (isRunning) {
    lastTime = performance.now();
    requestAnimationFrame(gameLoop);
  }
});

resetBtn.addEventListener("click", () => {
  playerScore = 0;
  aiScore = 0;
  updateScoreboard();
  resetBall();
});

difficultySelect.addEventListener("change", () => {
  const value = difficultySelect.value;
  if (AI_DIFFICULTY_PROFILES[value]) {
    currentDifficulty = value;
    aiProfile = AI_DIFFICULTY_PROFILES[currentDifficulty];
    // Небольшой «ресет» положения AI, чтобы смена сложности ощущалась честно
    aiY = HEIGHT / 2 - PADDLE_HEIGHT / 2;
  }
});

speedSelect.addEventListener("change", () => {
  const value = speedSelect.value;
  if (BALL_SPEED_PROFILES[value]) {
    currentSpeedProfile = value;
    ballProfile = BALL_SPEED_PROFILES[currentSpeedProfile];
    // Меняем профиль только для новых розыгрышей (мяч останется с текущей скоростью до сброса)
  }
});

// Функции игры
function update(dt) {
  handlePlayerInput();
  updateAI(dt);
  updateBall();
}

function handlePlayerInput() {
  let dy = 0;
  if (keys.ArrowUp || keys.w) dy -= PLAYER_SPEED;
  if (keys.ArrowDown || keys.s) dy += PLAYER_SPEED;

  playerY += dy;
  clampPaddles();
}

function clampPaddles() {
  playerY = Math.max(0, Math.min(HEIGHT - PADDLE_HEIGHT, playerY));
  aiY = Math.max(0, Math.min(HEIGHT - PADDLE_HEIGHT, aiY));
}

function updateAI(dt) {
  // Целевая позиция - центр мяча
  const targetY = ballY - PADDLE_HEIGHT / 2;

  // Простое предсказание движения мяча по вертикали
  const maxDelta = aiProfile.maxSpeed - aiProfile.baseSpeed;
  let aiSpeed = aiProfile.baseSpeed + Math.min(Math.abs(ballSpeedX) * 0.4, maxDelta);

  // Немного "человеческой" неточности (на лёгкой сложности - больше)
  const noise = (Math.random() - 0.5) * aiProfile.noiseAmplitude;
  const desiredY = targetY + noise;

  if (desiredY > aiY + 6) {
    aiY += aiSpeed;
  } else if (desiredY < aiY - 6) {
    aiY -= aiSpeed;
  }

  clampPaddles();
}

function updateBall() {
  ballX += ballSpeedX;
  ballY += ballSpeedY;

  // Столкновение с верхом/низом
  if (ballY - BALL_RADIUS <= 0 || ballY + BALL_RADIUS >= HEIGHT) {
    ballSpeedY *= -1;
    ballY = Math.max(BALL_RADIUS, Math.min(HEIGHT - BALL_RADIUS, ballY));
  }

  // Проверка столкновения с игроком
  if (
    ballX - BALL_RADIUS <= PLAYER_X + PADDLE_WIDTH &&
    ballX - BALL_RADIUS >= PLAYER_X &&
    ballY >= playerY &&
    ballY <= playerY + PADDLE_HEIGHT
  ) {
    ballSpeedX = Math.abs(ballSpeedX); // направо
    addBallSpin(playerY);
  }

  // Проверка столкновения с AI
  if (
    ballX + BALL_RADIUS >= AI_X &&
    ballX + BALL_RADIUS <= AI_X + PADDLE_WIDTH &&
    ballY >= aiY &&
    ballY <= aiY + PADDLE_HEIGHT
  ) {
    ballSpeedX = -Math.abs(ballSpeedX); // налево
    addBallSpin(aiY);
  }

  // Гол
  if (ballX < 0) {
    aiScore++;
    updateScoreboard();
    resetBall(-1);
  } else if (ballX > WIDTH) {
    playerScore++;
    updateScoreboard();
    resetBall(1);
  }
}

function addBallSpin(paddleY) {
  const paddleCenter = paddleY + PADDLE_HEIGHT / 2;
  const diff = ballY - paddleCenter;
  const norm = diff / (PADDLE_HEIGHT / 2);
  ballSpeedY = norm * ballProfile.spinFactor;

  // Немного увеличиваем скорость по X для динамики
  const signX = Math.sign(ballSpeedX);
  const speed = Math.min(
    Math.abs(ballSpeedX) + ballProfile.accelStep,
    ballProfile.maxSpeedX
  );
  ballSpeedX = signX * speed;
}

function resetBall(direction = 0) {
  ballX = WIDTH / 2;
  ballY = HEIGHT / 2;

  // Направление мяча: либо случайное, либо в сторону забившего
  const dirX =
    direction !== 0
      ? direction
      : Math.random() < 0.5
      ? -1
      : 1;

  ballSpeedX = dirX * ballProfile.startSpeedX;
  ballSpeedY = (Math.random() - 0.5) * ballProfile.spinFactor;
}

function updateScoreboard() {
  playerScoreEl.textContent = playerScore;
  aiScoreEl.textContent = aiScore;
}

function draw() {
  // Фон
  ctx.clearRect(0, 0, WIDTH, HEIGHT);

  // Центральная линия
  ctx.setLineDash([10, 12]);
  ctx.strokeStyle = "rgba(148, 163, 184, 0.45)";
  ctx.lineWidth = 2;
  ctx.beginPath();
  ctx.moveTo(WIDTH / 2, 10);
  ctx.lineTo(WIDTH / 2, HEIGHT - 10);
  ctx.stroke();
  ctx.setLineDash([]);

  // Платформа игрока
  drawPaddle(PLAYER_X, playerY, "#22c55e");

  // Платформа AI
  drawPaddle(AI_X, aiY, "#f97316");

  // Мяч
  drawBall();
}

function drawPaddle(x, y, color) {
  const radius = 6;
  ctx.fillStyle = color;
  ctx.beginPath();
  ctx.moveTo(x + radius, y);
  ctx.lineTo(x + PADDLE_WIDTH - radius, y);
  ctx.quadraticCurveTo(x + PADDLE_WIDTH, y, x + PADDLE_WIDTH, y + radius);
  ctx.lineTo(x + PADDLE_WIDTH, y + PADDLE_HEIGHT - radius);
  ctx.quadraticCurveTo(
    x + PADDLE_WIDTH,
    y + PADDLE_HEIGHT,
    x + PADDLE_WIDTH - radius,
    y + PADDLE_HEIGHT
  );
  ctx.lineTo(x + radius, y + PADDLE_HEIGHT);
  ctx.quadraticCurveTo(x, y + PADDLE_HEIGHT, x, y + PADDLE_HEIGHT - radius);
  ctx.lineTo(x, y + radius);
  ctx.quadraticCurveTo(x, y, x + radius, y);
  ctx.fill();
}

function drawBall() {
  const gradient = ctx.createRadialGradient(
    ballX - 4,
    ballY - 4,
    2,
    ballX,
    ballY,
    BALL_RADIUS + 2
  );
  gradient.addColorStop(0, "#fef9c3");
  gradient.addColorStop(0.4, "#fbbf24");
  gradient.addColorStop(1, "#ea580c");

  ctx.fillStyle = gradient;
  ctx.beginPath();
  ctx.arc(ballX, ballY, BALL_RADIUS, 0, Math.PI * 2);
  ctx.fill();
}

function gameLoop(timestamp) {
  if (!isRunning) return;

  const dt = (timestamp - lastTime) / 16.67; // ~60 FPS нормировка
  lastTime = timestamp;

  update(dt);
  draw();

  requestAnimationFrame(gameLoop);
}

// Первый отрисовочный кадр (пауза)
draw();
</script>
  </body>
</html>
