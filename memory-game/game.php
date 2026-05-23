<?php
$level = isset($_GET['level']) ? $_GET['level'] : 'medium';
if (!in_array($level, ['easy','medium','hard'])) $level = 'medium';

$all_techs = [
  ['id'=>'js',     'icon'=>'JS',  'label'=>'JavaScript', 'color'=>'#f7df1e', 'bg'=>'#2a2700'],
  ['id'=>'ts',     'icon'=>'TS',  'label'=>'TypeScript',  'color'=>'#4da6ff', 'bg'=>'#001a33'],
  ['id'=>'py',     'icon'=>'🐍',  'label'=>'Python',      'color'=>'#3572a5', 'bg'=>'#001020'],
  ['id'=>'go',     'icon'=>'Go',  'label'=>'Golang',      'color'=>'#00add8', 'bg'=>'#002233'],
  ['id'=>'rust',   'icon'=>'⚙️',  'label'=>'Rust',        'color'=>'#ff6b3d', 'bg'=>'#2a1000'],
  ['id'=>'php',    'icon'=>'🐘',  'label'=>'PHP',         'color'=>'#a78bfa', 'bg'=>'#1a0033'],
  ['id'=>'docker', 'icon'=>'🐳',  'label'=>'Docker',      'color'=>'#2496ed', 'bg'=>'#001830'],
  ['id'=>'k8s',    'icon'=>'☸',   'label'=>'Kubernetes',  'color'=>'#326ce5', 'bg'=>'#000d2a'],
  ['id'=>'react',  'icon'=>'⚛',   'label'=>'React',       'color'=>'#61dafb', 'bg'=>'#002233'],
  ['id'=>'vue',    'icon'=>'▲',   'label'=>'Vue',         'color'=>'#42b883', 'bg'=>'#002218'],
  ['id'=>'git',    'icon'=>'⌥',   'label'=>'Git',         'color'=>'#f05032', 'bg'=>'#2a0800'],
  ['id'=>'linux',  'icon'=>'🐧',  'label'=>'Linux',       'color'=>'#fcc624', 'bg'=>'#2a2200'],
];

$counts = ['easy'=>4, 'medium'=>8, 'hard'=>12];
$times  = ['easy'=>60, 'medium'=>90, 'hard'=>120];
$count  = $counts[$level];
$time   = $times[$level];

$selected = array_slice($all_techs, 0, $count);
$cards    = array_merge($selected, $selected);
shuffle($cards);

$cols = ['easy'=>4, 'medium'=>4, 'hard'=>6];
$col  = $cols[$level];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevMatch — <?= strtoupper($level) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg: #0d0d0d;
      --surface: #141414;
      --border: #2a2a2a;
      --green: #00ff88;
      --amber: #ffb800;
      --red: #ff4545;
      --text: #e0e0e0;
      --muted: #555;
    }
    body {
      background: var(--bg);
      font-family: 'JetBrains Mono', monospace;
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 0 16px 40px;
      position: relative;
    }
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(0,255,136,0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,255,136,0.025) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
    }

    /* HUD */
    .hud {
      width: 100%;
      max-width: 700px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 0 20px;
      border-bottom: 1px solid var(--border);
      margin-bottom: 28px;
      position: relative;
      z-index: 1;
    }
    .hud-back {
      color: var(--muted);
      text-decoration: none;
      font-size: 13px;
      transition: color .2s;
    }
    .hud-back:hover { color: var(--green); }

    .hud-center { font-size: 14px; font-weight: 700; }
    .level-easy   { color: var(--green); }
    .level-medium { color: var(--amber); }
    .level-hard   { color: var(--red); }

    .hud-stats { display: flex; gap: 20px; }
    .stat { text-align: center; }
    .stat-label { display: block; font-size: 10px; color: var(--muted); letter-spacing: 1px; margin-bottom: 2px; }
    .stat-val { display: block; font-size: 18px; font-weight: 700; color: var(--text); }

    #timer-box.warning .stat-val { color: var(--amber); animation: pulse .6s ease-in-out infinite; }
    #timer-box.danger  .stat-val { color: var(--red);   animation: pulse .3s ease-in-out infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

    /* BOARD */
    .board {
      display: grid;
      grid-template-columns: repeat(<?= $col ?>, 1fr);
      gap: 12px;
      width: 100%;
      max-width: 700px;
      position: relative;
      z-index: 1;
    }

    /* CARD */
    .card {
      aspect-ratio: 3/4;
      cursor: pointer;
      perspective: 600px;
    }
    .card-inner {
      width: 100%;
      height: 100%;
      position: relative;
      transform-style: preserve-3d;
      transition: transform .45s cubic-bezier(.4,0,.2,1);
      border-radius: 10px;
    }
    .card.flipped .card-inner,
    .card.matched .card-inner {
      transform: rotateY(180deg);
    }

    .card-back, .card-front {
      position: absolute;
      inset: 0;
      border-radius: 10px;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 6px;
      border: 1px solid var(--border);
    }

    .card-back {
      background: #111;
      color: var(--border);
      font-size: clamp(20px, 4vw, 32px);
      font-weight: 800;
      letter-spacing: -1px;
      transition: border-color .2s;
    }
    .card:hover:not(.flipped):not(.matched) .card-back {
      border-color: #444;
      background: #181818;
    }
    .card-back::after {
      content: '';
      position: absolute;
      inset: 3px;
      border-radius: 8px;
      border: 1px dashed #222;
    }

    .card-front {
      transform: rotateY(180deg);
      background: var(--card-bg, #111);
      border-color: var(--card-color, #333);
    }
    .card.matched .card-front {
      box-shadow: 0 0 18px var(--card-color, #333), 0 0 4px var(--card-color, #333);
    }
    .card-icon {
      font-size: clamp(20px, 4.5vw, 34px);
      font-weight: 800;
      color: var(--card-color);
      line-height: 1;
    }
    .card-label {
      font-size: clamp(8px, 1.5vw, 11px);
      color: var(--muted);
      letter-spacing: 1px;
    }

    /* OVERLAY */
    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.85);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 100;
      backdrop-filter: blur(6px);
    }
    .overlay.hidden { display: none; }
    .overlay-box {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 48px 40px;
      text-align: center;
      max-width: 360px;
      width: 90%;
      animation: popIn .3s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes popIn { from{transform:scale(.8);opacity:0} to{transform:scale(1);opacity:1} }
    .overlay-emoji { font-size: 56px; margin-bottom: 16px; }
    .overlay-title { font-size: 28px; font-weight: 800; margin-bottom: 8px; }
    .overlay-msg   { font-size: 13px; color: var(--muted); margin-bottom: 28px; line-height: 1.6; }
    .overlay-actions { display: flex; gap: 12px; justify-content: center; }
    .btn-retry, .btn-home {
      font-family: 'JetBrains Mono', monospace;
      font-size: 13px;
      font-weight: 700;
      padding: 10px 20px;
      border-radius: 7px;
      cursor: pointer;
      text-decoration: none;
      border: 1px solid var(--border);
      transition: all .2s;
    }
    .btn-retry { background: var(--green); color: #000; border-color: var(--green); }
    .btn-retry:hover { background: #00cc6a; }
    .btn-home  { background: transparent; color: var(--text); }
    .btn-home:hover { border-color: #666; }
  </style>
</head>
<body>

<!-- OVERLAY -->
<div id="overlay" class="overlay hidden">
  <div class="overlay-box">
    <div id="ov-emoji" class="overlay-emoji"></div>
    <div id="ov-title" class="overlay-title"></div>
    <div id="ov-msg"   class="overlay-msg"></div>
    <div class="overlay-actions">
      <button onclick="location.reload()" class="btn-retry">↺ Reintentar</button>
      <a href="index.php" class="btn-home">⌂ Inicio</a>
    </div>
  </div>
</div>

<!-- HUD -->
<div class="hud">
  <a href="index.php" class="hud-back">← back</a>
  <div class="hud-center">
    <span class="level-<?= $level ?>"><?= strtoupper($level) ?> MODE</span>
  </div>
  <div class="hud-stats">
    <div class="stat">
      <span class="stat-label">MOVES</span>
      <span class="stat-val" id="moves">0</span>
    </div>
    <div class="stat">
      <span class="stat-label">PAIRS</span>
      <span class="stat-val" id="pairs">0/<?= $count ?></span>
    </div>
    <div class="stat" id="timer-box">
      <span class="stat-label">TIME</span>
      <span class="stat-val" id="timer"><?= $time ?></span>
    </div>
  </div>
</div>

<!-- BOARD -->
<div class="board" id="board">
  <?php foreach ($cards as $i => $c): ?>
  <div class="card" data-id="<?= $c['id'] ?>"
       style="--card-color:<?= $c['color'] ?>; --card-bg:<?= $c['bg'] ?>">
    <div class="card-inner">
      <div class="card-back">&lt;/&gt;</div>
      <div class="card-front">
        <span class="card-icon"><?= $c['icon'] ?></span>
        <span class="card-label"><?= $c['label'] ?></span>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<script>
  const TOTAL = <?= $count ?>;
  let timeLeft  = <?= $time ?>;
  let moves     = 0;
  let matched   = 0;
  let flipped   = [];
  let locked    = false;

  const timerEl = document.getElementById('timer');
  const timerBox= document.getElementById('timer-box');
  const movesEl = document.getElementById('moves');
  const pairsEl = document.getElementById('pairs');
  const overlay = document.getElementById('overlay');

  // Timer
  const ticker = setInterval(() => {
    timeLeft--;
    timerEl.textContent = timeLeft;
    if (timeLeft <= 10) timerBox.className = 'stat danger';
    else if (timeLeft <= 20) timerBox.className = 'stat warning';
    if (timeLeft <= 0) {
      clearInterval(ticker);
      endGame(false);
    }
  }, 1000);

  // Card click
  document.getElementById('board').addEventListener('click', e => {
    const card = e.target.closest('.card');
    if (!card || locked || card.classList.contains('flipped') || card.classList.contains('matched')) return;

    card.classList.add('flipped');
    flipped.push(card);

    if (flipped.length === 2) {
      locked = true;
      moves++;
      movesEl.textContent = moves;
      checkMatch();
    }
  });

  function checkMatch() {
    const [a, b] = flipped;
    if (a.dataset.id === b.dataset.id) {
      a.classList.add('matched');
      b.classList.add('matched');
      matched++;
      pairsEl.textContent = matched + '/' + TOTAL;
      flipped = [];
      locked  = false;
      if (matched === TOTAL) { clearInterval(ticker); setTimeout(() => endGame(true), 500); }
    } else {
      setTimeout(() => {
        a.classList.remove('flipped');
        b.classList.remove('flipped');
        flipped = [];
        locked  = false;
      }, 900);
    }
  }

  function endGame(win) {
    const emoji = document.getElementById('ov-emoji');
    const title = document.getElementById('ov-title');
    const msg   = document.getElementById('ov-msg');
    if (win) {
      emoji.textContent = '🎉';
      title.textContent = '¡Ganaste!';
      title.style.color = '#00ff88';
      msg.innerHTML = moves + ' movimientos · ' + timeLeft + 's restantes<br><span style="color:#555">// stack desbloqueado</span>';
    } else {
      emoji.textContent = '💀';
      title.textContent = 'Timeout';
      title.style.color = '#ff4545';
      msg.innerHTML = matched + '/' + TOTAL + ' pares encontrados<br><span style="color:#555">// proceso terminado</span>';
    }
    overlay.classList.remove('hidden');
  }
</script>
</body>
</html>
