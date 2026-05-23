<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevMatch</title>
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
      --blue: #4da6ff;
      --text: #e0e0e0;
      --muted: #555;
    }

    body {
      background: var(--bg);
      font-family: 'JetBrains Mono', monospace;
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      overflow-x: hidden;
    }

    /* grid background */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(0,255,136,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,255,136,0.03) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
    }

    .container {
      width: 100%;
      max-width: 560px;
      position: relative;
      z-index: 1;
    }

    /* terminal bar */
    .terminal-bar {
      background: #1a1a1a;
      border: 1px solid var(--border);
      border-bottom: none;
      border-radius: 10px 10px 0 0;
      padding: 10px 16px;
      display: flex;
      align-items: center;
      gap: 7px;
    }
    .dot { width: 12px; height: 12px; border-radius: 50%; }
    .dot.r { background: #ff5f57; }
    .dot.y { background: #febc2e; }
    .dot.g { background: #28c840; }
    .t-title { margin-left: 10px; color: var(--muted); font-size: 12px; }

    /* main card */
    .card-wrap {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 0 0 10px 10px;
      padding: 40px 36px;
    }

    .prompt {
      color: var(--green);
      font-size: 13px;
      margin-bottom: 28px;
    }
    .prompt span { color: var(--muted); }

    h1 {
      font-size: clamp(36px, 8vw, 58px);
      font-weight: 800;
      line-height: 1;
      margin-bottom: 10px;
      letter-spacing: -1px;
    }
    h1 .dev { color: var(--green); }
    h1 .match { color: var(--text); }
    h1 .cur {
      color: var(--green);
      animation: blink 1s step-end infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }

    .subtitle {
      color: var(--muted);
      font-size: 13px;
      margin-bottom: 36px;
    }
    .subtitle .comment { color: #3a3a3a; }

    /* difficulty */
    .diff-label {
      font-size: 11px;
      color: var(--muted);
      letter-spacing: 2px;
      margin-bottom: 14px;
    }

    .diff-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 36px;
    }

    .diff-btn {
      display: block;
      text-decoration: none;
      background: #0d0d0d;
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 18px 12px;
      text-align: center;
      transition: all .2s;
      position: relative;
      overflow: hidden;
    }
    .diff-btn::before {
      content: '';
      position: absolute;
      inset: 0;
      opacity: 0;
      transition: opacity .2s;
    }
    .diff-btn.easy::before   { background: radial-gradient(circle at 50% 0, rgba(0,255,136,.12), transparent 70%); }
    .diff-btn.medium::before { background: radial-gradient(circle at 50% 0, rgba(255,184,0,.12), transparent 70%); }
    .diff-btn.hard::before   { background: radial-gradient(circle at 50% 0, rgba(255,69,69,.12), transparent 70%); }

    .diff-btn:hover { transform: translateY(-2px); }
    .diff-btn:hover::before { opacity: 1; }
    .diff-btn.easy:hover   { border-color: var(--green); }
    .diff-btn.medium:hover { border-color: var(--amber); }
    .diff-btn.hard:hover   { border-color: var(--red); }

    .diff-icon { font-size: 22px; margin-bottom: 8px; }
    .diff-name { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .diff-easy   .diff-name { color: var(--green); }
    .diff-medium .diff-name { color: var(--amber); }
    .diff-hard   .diff-name { color: var(--red); }
    .diff-desc { font-size: 11px; color: var(--muted); }

    /* footer */
    .footer {
      border-top: 1px solid var(--border);
      padding-top: 20px;
      font-size: 11px;
      color: var(--muted);
      display: flex;
      justify-content: space-between;
    }
    .footer .version { color: var(--green); }
  </style>
</head>
<body>
<div class="container">
  <div class="terminal-bar">
    <div class="dot r"></div>
    <div class="dot y"></div>
    <div class="dot g"></div>
    <span class="t-title">devmatch.exe — bash</span>
  </div>
  <div class="card-wrap">
    <div class="prompt">
      <span>~/games $</span> ./devmatch --init
    </div>

    <h1>
      <span class="dev">Dev</span><span class="match">Match</span><span class="cur">_</span>
    </h1>
    <p class="subtitle">
      <span class="comment">// </span>Encuentra los pares de tecnologías antes de que el tiempo se agote
    </p>

    <div class="diff-label">// SELECT DIFFICULTY</div>
    <div class="diff-grid">
      <a href="game.php?level=easy" class="diff-btn easy diff-easy">
        <div class="diff-icon">🟢</div>
        <div class="diff-name">EASY</div>
        <div class="diff-desc">8 cartas · 60s</div>
      </a>
      <a href="game.php?level=medium" class="diff-btn medium diff-medium">
        <div class="diff-icon">🟡</div>
        <div class="diff-name">MEDIUM</div>
        <div class="diff-desc">16 cartas · 90s</div>
      </a>
      <a href="game.php?level=hard" class="diff-btn hard diff-hard">
        <div class="diff-icon">🔴</div>
        <div class="diff-name">HARD</div>
        <div class="diff-desc">24 cartas · 120s</div>
      </a>
    </div>

    <div class="footer">
      <span>PHP · CSS · JS · no frameworks</span>
      <span class="version">v1.0.0</span>
    </div>
  </div>
</div>
</body>
</html>
