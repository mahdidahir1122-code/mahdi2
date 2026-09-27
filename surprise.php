<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

set_exception_handler(static function (Throwable $e): void {
    error_log($e->__toString());
    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><meta charset="utf-8"><title>Hadiyad</title><p style="font-family:Georgia,serif;padding:48px">Wax baa dhacay. Isku day mar kale.</p>';
});

$code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) ($_GET['code'] ?? '')) ?? '');
$surprise = preg_match('/^[A-Z0-9]{4,12}$/', $code) ? find_surprise_by_code($code) : null;

header('Content-Type: text/html; charset=UTF-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('Cache-Control: private, no-cache');

$missing = $surprise === null;
if ($missing) {
    http_response_code(404);
}
$theme = $missing ? 'rose' : clean_choice((string) $surprise['theme'], themes(), 'rose');
$font = $missing ? 'elegant' : clean_choice((string) $surprise['font_style'], fonts(), 'elegant');
$bg = $missing ? 'glow' : clean_choice((string) $surprise['bg_style'], backgrounds(), 'glow');
$photos = $missing ? [] : photos_for((int) $surprise['id']);
$payload = $missing ? [] : surprise_payload($surprise, $photos);
$particles = [
    [8, 14, 6, 0], [18, 72, 4, 1.2], [78, 18, 5, .4], [88, 64, 3, 2],
    [28, 30, 3, 1.6], [66, 78, 4, .8], [46, 12, 3, 2.4], [92, 38, 5, 1],
    [12, 48, 3, 3], [58, 42, 2, 1.8], [36, 84, 4, .2], [72, 8, 3, 2.8],
];
?>
<!DOCTYPE html>
<html lang="so">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="<?= e(theme_color($theme)) ?>">
  <title><?= $missing ? 'Hadiyad' : e((string) $surprise['recipient_name']) . ' ❤️' ?></title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E🎁%3C/text%3E%3C/svg%3E">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Great+Vibes&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(web('assets/css/gift.css?v=17')) ?>">
</head>
<body data-theme="<?= e($theme) ?>" data-font="<?= e($font) ?>" data-bg="<?= e($bg) ?>">
  <div class="atmosphere" aria-hidden="true">
    <span class="orb orb-a"></span>
    <span class="orb orb-b"></span>
    <span class="orb orb-c"></span>
  </div>
  <div class="particles" aria-hidden="true">
    <?php foreach ($particles as [$left, $top, $size, $delay]): ?>
      <span style="left:<?= (int) $left ?>%;top:<?= (int) $top ?>%;width:<?= (int) $size ?>px;height:<?= (int) $size ?>px;animation-delay:<?= e((string) $delay) ?>s"></span>
    <?php endforeach; ?>
  </div>
  <div class="bloom" id="bloom" hidden aria-hidden="true">
    <?php
      $kinds = ['blue', 'white', 'mix', 'blue', 'white', 'mix'];
      $spots = [];
      $cols = 4;
      $rows = 8;
      for ($r = 0; $r < $rows; $r++) {
          for ($c = 0; $c < $cols; $c++) {
              $spots[] = [
                  'x' => -14 + ($c * 30) + ((($r * 3 + $c * 5) % 7) - 3),
                  'y' => -10 + ($r * 14) + ((($r * 5 + $c) % 5) - 2),
                  's' => 46 + (($r + $c) % 3) * 6,
                  'ring' => $r,
              ];
          }
      }
      $total = count($spots) - 1;
      foreach ($spots as $n => $spot) {
          $last = $n === $total ? ' pansy-last' : '';
          $tilt = (($n % 2 === 0) ? -1 : 1) * (($n % 5) * 3);
          echo '<span class="pansy' . $last . '" style="left:' . round($spot['x'], 1) . '%;top:' . round($spot['y'], 1)
              . '%;width:' . $spot['s'] . '%;--i:' . $n . ';--tilt:' . $tilt . 'deg">'
              . pansy_svg($kinds[$n % 3]) . '</span>';
      }
    ?>
  </div>
  <p class="sr-only" id="live" aria-live="polite"></p>

  <?php if ($missing): ?>
    <main class="missing">
      <p class="eyebrow">Hadiyad</p>
      <h1>Lama helin</h1>
      <p>Liiskan ma furmi karo. Hubi QR-ka, kadibna isku day mar kale.</p>
    </main>
  <?php else: ?>
    <main class="stage" id="stage">
      <section class="welcome-screen" id="welcome">
        <p class="eyebrow"><?= e((string) $surprise['recipient_name']) ?></p>
        <h1 class="welcome-line"><?= e((string) $surprise['welcome_text']) ?></h1>
      </section>

      <section class="gift-screen" id="giftScreen">
        <button type="button" class="gift" id="gift" aria-label="Fur hadiyadda">
          <span class="present">
            <span class="base">
              <span class="glow" aria-hidden="true"></span>
              <span class="side" aria-hidden="true"></span>
              <span class="front">
                <span class="ribbon-v"></span>
                <span class="ribbon-h"></span>
              </span>
            </span>
            <span class="lid">
              <span class="lid-slab">
                <span class="ribbon-v"></span>
              </span>
              <span class="bow" aria-hidden="true">
                <span class="loop left"></span>
                <span class="loop right"></span>
                <span class="knot"></span>
                <span class="tail left"></span>
                <span class="tail right"></span>
              </span>
            </span>
            <span class="floor" aria-hidden="true"></span>
          </span>
        </button>
        <p class="hint" id="hint">Taabo hadiyadda <span aria-hidden="true">🎁</span></p>
      </section>
    </main>

    <section class="option-screen" id="options" hidden>
      <p class="who"><?= e((string) $surprise['recipient_name']) ?></p>
      <div class="choices">
        <button type="button" class="choice choice-letter" data-panel="message">
          <span class="choice-icon" aria-hidden="true">💌</span>
          <span class="choice-copy">
            <span class="choice-label">QORAAL</span>
            <span class="choice-hint">Akhriso fariinta</span>
          </span>
        </button>
        <button type="button" class="choice choice-music" data-panel="music">
          <span class="choice-icon" aria-hidden="true">🎵</span>
          <span class="choice-copy">
            <span class="choice-label">HEES</span>
            <span class="choice-hint">Dhageyso codka</span>
          </span>
        </button>
        <button type="button" class="choice choice-photos" data-panel="photos">
          <span class="choice-icon" aria-hidden="true">🖼️</span>
          <span class="choice-copy">
            <span class="choice-label">SAWIRRO</span>
            <span class="choice-hint">Fiiri xasuusta</span>
          </span>
        </button>
      </div>
    </section>

    <div class="panel" id="panel" hidden>
      <div class="panel-card" role="dialog" aria-modal="true" aria-labelledby="dialogTitle">
        <button type="button" class="close" id="panelClose" aria-label="Xir">×</button>

        <section id="panel-message" hidden>
          <div class="hearts" aria-hidden="true"><span>♥</span><span>♥</span><span>♥</span><span>♥</span><span>♥</span><span>♥</span></div>
          <p class="dear"><?= e((string) $surprise['recipient_name']) ?></p>
          <h2 id="dialogTitle"><?= e((string) $surprise['message_title']) ?></h2>
          <p class="message-body" id="messageBody"></p>
        </section>

        <section id="panel-music" hidden>
          <div class="player">
            <div class="disc<?= $payload['cover'] === '' ? ' is-empty' : '' ?>" id="disc">
              <?php if ($payload['cover'] !== ''): ?>
                <img src="<?= e($payload['cover']) ?>" alt="">
              <?php endif; ?>
            </div>
            <div class="viz" id="viz" aria-hidden="true">
              <?php for ($i = 0; $i < 24; $i++): ?><span></span><?php endfor; ?>
            </div>
            <h2 class="song-title"><?= e((string) ($surprise['song_title'] !== '' ? $surprise['song_title'] : 'Hees')) ?></h2>
            <?php if ($payload['audio'] === ''): ?>
              <p class="empty-note">Heesta ayaa dhowaan kuu iman doonta.</p>
            <?php else: ?>
              <button type="button" class="play" id="playBtn" aria-label="Daar heesta"><span class="play-icon"></span></button>
              <label class="scrub">
                <span class="sr-only">Meesha heesta</span>
                <input type="range" id="seek" min="0" max="1000" value="0" step="1">
              </label>
              <div class="times"><span id="timeNow">0:00</span><span id="timeDur">0:00</span></div>
              <audio class="audio-el" id="audio" preload="metadata" playsinline src="<?= e($payload['audio']) ?>"></audio>
            <?php endif; ?>
          </div>
        </section>

        <section id="panel-photos" hidden>
          <?php if ($payload['photos'] === []): ?>
            <h2>Sawirro</h2>
            <p class="empty-note">Sawirradi weli way qarsoon yihiin.</p>
          <?php else: ?>
            <h2 class="gallery-heading">Sawirro</h2>
            <div class="gallery" id="gallery">
              <?php foreach ($payload['photos'] as $i => $src): ?>
                <button type="button" class="shot" data-index="<?= (int) $i ?>">
                  <img src="<?= e($src) ?>" alt="Sawir <?= (int) $i + 1 ?>" loading="lazy">
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>
      </div>
    </div>

    <div class="lightbox" id="lightbox" hidden>
      <button type="button" class="icon-btn close-light" id="lightClose" aria-label="Xir sawirka">×</button>
      <button type="button" class="icon-btn nav-btn prev" id="lightPrev" aria-label="Sawirka hore">‹</button>
      <button type="button" class="icon-btn nav-btn next" id="lightNext" aria-label="Sawirka xiga">›</button>
      <figure>
        <img id="lightboxImg" alt="">
        <figcaption id="lightCount"></figcaption>
      </figure>
    </div>

    <script type="application/json" id="surprise-data"><?= json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?></script>
    <script src="<?= e(web('assets/js/gift.js?v=11')) ?>" defer></script>
  <?php endif; ?>
</body>
</html>
