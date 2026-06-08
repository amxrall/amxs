<?php if(!$indexing) { exit; } ?>
<?php
// CONEXÃO COM O BANCO
$dbHost = "127.0.0.1";
$dbName = "stone";
$dbUser = "root";
$dbPass = "123456";

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}

// TOP PVP
$sqlPvp = $pdo->query("
    SELECT char_name, pvpkills, pkkills, online
    FROM characters
    WHERE accesslevel = 0
    ORDER BY pvpkills DESC, pkkills DESC
    LIMIT 5
");
$rankPvp = $sqlPvp->fetchAll();

// TOP PK
$sqlPk = $pdo->query("
    SELECT char_name, pkkills, pvpkills, online
    FROM characters
    WHERE accesslevel = 0
    ORDER BY pkkills DESC, pvpkills DESC
    LIMIT 5
");
$rankPk = $sqlPk->fetchAll();

// TOP CLAN
$sqlClan = $pdo->query("
    SELECT 
        c.clan_id,
        c.clan_name,
        c.clan_level,
        c.reputation_score,
        leader.char_name AS leader_name,
        leader.online AS leader_online,
        COALESCE(cp.boss_points, 0) AS boss_points,
        (
            SELECT COUNT(*)
            FROM characters ch
            WHERE ch.clanid = c.clan_id
        ) AS members
    FROM clan_data c
    LEFT JOIN characters leader ON leader.obj_Id = c.leader_id
    LEFT JOIN clan_points cp ON c.clan_id = cp.clan_id
    ORDER BY boss_points DESC, c.reputation_score DESC
    LIMIT 5
");
$rankClan = $sqlClan->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <?php require('private/seo.php'); ?>

  <meta charset="UTF-8">
  <link rel="shortcut icon" href="assets/img/favicon.ico">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?php echo $SEO['title']; ?></title>
  <meta name="description" content="<?php echo $SEO['description']; ?>">
  <meta name="author" content="<?php echo $server_name; ?>">
  <meta name="keywords" content="<?php echo strtolower($server_name . ', ' . $server_chronicle); ?>, lineage 2, interlude, pvp">
  <meta name="robots" content="index, follow">

  <meta property="og:type" content="website">
  <meta property="og:title" content="<?php echo $SEO['title']; ?>">
  <meta property="og:site_name" content="<?php echo $server_name; ?>">
  <meta property="og:url" content="http://<?php echo $server_url; ?>">
  <meta property="og:description" content="<?php echo $SEO['description']; ?>">
  <meta property="og:image" content="http://<?php echo $server_url; ?>/imgs/image_src.jpg">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo $SEO['title']; ?>">
  <meta name="twitter:description" content="<?php echo $SEO['description']; ?>">
  <meta name="twitter:image" content="http://<?php echo $server_url; ?>/imgs/image_src.jpg">

  <link rel="stylesheet" crossorigin href="assets/style.css">

  <style type="text/css">:where(html[dir="ltr"]),
:where([data-sonner-toaster][dir="ltr"]) {
    --toast-icon-margin-start: -3px;
    --toast-icon-margin-end: 4px;
    --toast-svg-margin-start: -1px;
    --toast-svg-margin-end: 0px;
    --toast-button-margin-start: auto;
    --toast-button-margin-end: 0;
    --toast-close-button-start: 0;
    --toast-close-button-end: unset;
    --toast-close-button-transform: translate(-35%, -35%)
}

:where(html[dir="rtl"]),
:where([data-sonner-toaster][dir="rtl"]) {
    --toast-icon-margin-start: 4px;
    --toast-icon-margin-end: -3px;
    --toast-svg-margin-start: 0px;
    --toast-svg-margin-end: -1px;
    --toast-button-margin-start: 0;
    --toast-button-margin-end: auto;
    --toast-close-button-start: unset;
    --toast-close-button-end: 0;
    --toast-close-button-transform: translate(35%, -35%)
}

:where([data-sonner-toaster]) {
    position: fixed;
    width: var(--width);
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
    --gray1: hsl(0, 0%, 99%);
    --gray2: hsl(0, 0%, 97.3%);
    --gray3: hsl(0, 0%, 95.1%);
    --gray4: hsl(0, 0%, 93%);
    --gray5: hsl(0, 0%, 90.9%);
    --gray6: hsl(0, 0%, 88.7%);
    --gray7: hsl(0, 0%, 85.8%);
    --gray8: hsl(0, 0%, 78%);
    --gray9: hsl(0, 0%, 56.1%);
    --gray10: hsl(0, 0%, 52.3%);
    --gray11: hsl(0, 0%, 43.5%);
    --gray12: hsl(0, 0%, 9%);
    --border-radius: 8px;
    box-sizing: border-box;
    padding: 0;
    margin: 0;
    list-style: none;
    outline: none;
    z-index: 999999999;
    transition: transform .4s ease
}

:where([data-sonner-toaster][data-lifted="true"]) {
    transform: translateY(-10px)
}

@media (hover: none) and (pointer: coarse) {
    :where([data-sonner-toaster][data-lifted="true"]) {
        transform: none
    }
}

:where([data-sonner-toaster][data-x-position="right"]) {
    right: var(--offset-right)
}

:where([data-sonner-toaster][data-x-position="left"]) {
    left: var(--offset-left)
}

:where([data-sonner-toaster][data-x-position="center"]) {
    left: 50%;
    transform: translate(-50%)
}

:where([data-sonner-toaster][data-y-position="top"]) {
    top: var(--offset-top)
}

:where([data-sonner-toaster][data-y-position="bottom"]) {
    bottom: var(--offset-bottom)
}

:where([data-sonner-toast]) {
    --y: translateY(100%);
    --lift-amount: calc(var(--lift) * var(--gap));
    z-index: var(--z-index);
    position: absolute;
    opacity: 0;
    transform: var(--y);
    filter: blur(0);
    touch-action: none;
    transition: transform .4s, opacity .4s, height .4s, box-shadow .2s;
    box-sizing: border-box;
    outline: none;
    overflow-wrap: anywhere
}

:where([data-sonner-toast][data-styled="true"]) {
    padding: 16px;
    background: var(--normal-bg);
    border: 1px solid var(--normal-border);
    color: var(--normal-text);
    border-radius: var(--border-radius);
    box-shadow: 0 4px 12px #0000001a;
    width: var(--width);
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px
}

:where([data-sonner-toast]:focus-visible) {
    box-shadow: 0 4px 12px #0000001a, 0 0 0 2px #0003
}

:where([data-sonner-toast][data-y-position="top"]) {
    top: 0;
    --y: translateY(-100%);
    --lift: 1;
    --lift-amount: calc(1 * var(--gap))
}

:where([data-sonner-toast][data-y-position="bottom"]) {
    bottom: 0;
    --y: translateY(100%);
    --lift: -1;
    --lift-amount: calc(var(--lift) * var(--gap))
}

:where([data-sonner-toast]) :where([data-description]) {
    font-weight: 400;
    line-height: 1.4;
    color: inherit
}

:where([data-sonner-toast]) :where([data-title]) {
    font-weight: 500;
    line-height: 1.5;
    color: inherit
}

:where([data-sonner-toast]) :where([data-icon]) {
    display: flex;
    height: 16px;
    width: 16px;
    position: relative;
    justify-content: flex-start;
    align-items: center;
    flex-shrink: 0;
    margin-left: var(--toast-icon-margin-start);
    margin-right: var(--toast-icon-margin-end)
}

:where([data-sonner-toast][data-promise="true"]) :where([data-icon])>svg {
    opacity: 0;
    transform: scale(.8);
    transform-origin: center;
    animation: sonner-fade-in .3s ease forwards
}

:where([data-sonner-toast]) :where([data-icon])>* {
    flex-shrink: 0
}

:where([data-sonner-toast]) :where([data-icon]) svg {
    margin-left: var(--toast-svg-margin-start);
    margin-right: var(--toast-svg-margin-end)
}

:where([data-sonner-toast]) :where([data-content]) {
    display: flex;
    flex-direction: column;
    gap: 2px
}

[data-sonner-toast][data-styled=true] [data-button] {
    border-radius: 4px;
    padding-left: 8px;
    padding-right: 8px;
    height: 24px;
    font-size: 12px;
    color: var(--normal-bg);
    background: var(--normal-text);
    margin-left: var(--toast-button-margin-start);
    margin-right: var(--toast-button-margin-end);
    border: none;
    cursor: pointer;
    outline: none;
    display: flex;
    align-items: center;
    flex-shrink: 0;
    transition: opacity .4s, box-shadow .2s
}

:where([data-sonner-toast]) :where([data-button]):focus-visible {
    box-shadow: 0 0 0 2px #0006
}

:where([data-sonner-toast]) :where([data-button]):first-of-type {
    margin-left: var(--toast-button-margin-start);
    margin-right: var(--toast-button-margin-end)
}

:where([data-sonner-toast]) :where([data-cancel]) {
    color: var(--normal-text);
    background: rgba(0, 0, 0, .08)
}

:where([data-sonner-toast][data-theme="dark"]) :where([data-cancel]) {
    background: rgba(255, 255, 255, .3)
}

:where([data-sonner-toast]) :where([data-close-button]) {
    position: absolute;
    left: var(--toast-close-button-start);
    right: var(--toast-close-button-end);
    top: 0;
    height: 20px;
    width: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0;
    color: var(--gray12);
    border: 1px solid var(--gray4);
    transform: var(--toast-close-button-transform);
    border-radius: 50%;
    cursor: pointer;
    z-index: 1;
    transition: opacity .1s, background .2s, border-color .2s
}

[data-sonner-toast] [data-close-button] {
    background: var(--gray1)
}

:where([data-sonner-toast]) :where([data-close-button]):focus-visible {
    box-shadow: 0 4px 12px #0000001a, 0 0 0 2px #0003
}

:where([data-sonner-toast]) :where([data-disabled="true"]) {
    cursor: not-allowed
}

:where([data-sonner-toast]):hover :where([data-close-button]):hover {
    background: var(--gray2);
    border-color: var(--gray5)
}

:where([data-sonner-toast][data-swiping="true"]):before {
    content: "";
    position: absolute;
    left: -50%;
    right: -50%;
    height: 100%;
    z-index: -1
}

:where([data-sonner-toast][data-y-position="top"][data-swiping="true"]):before {
    bottom: 50%;
    transform: scaleY(3) translateY(50%)
}

:where([data-sonner-toast][data-y-position="bottom"][data-swiping="true"]):before {
    top: 50%;
    transform: scaleY(3) translateY(-50%)
}

:where([data-sonner-toast][data-swiping="false"][data-removed="true"]):before {
    content: "";
    position: absolute;
    inset: 0;
    transform: scaleY(2)
}

:where([data-sonner-toast]):after {
    content: "";
    position: absolute;
    left: 0;
    height: calc(var(--gap) + 1px);
    bottom: 100%;
    width: 100%
}

:where([data-sonner-toast][data-mounted="true"]) {
    --y: translateY(0);
    opacity: 1
}

:where([data-sonner-toast][data-expanded="false"][data-front="false"]) {
    --scale: var(--toasts-before) * .05 + 1;
    --y: translateY(calc(var(--lift-amount) * var(--toasts-before))) scale(calc(-1 * var(--scale)));
    height: var(--front-toast-height)
}

:where([data-sonner-toast])>* {
    transition: opacity .4s
}

:where([data-sonner-toast][data-expanded="false"][data-front="false"][data-styled="true"])>* {
    opacity: 0
}

:where([data-sonner-toast][data-visible="false"]) {
    opacity: 0;
    pointer-events: none
}

:where([data-sonner-toast][data-mounted="true"][data-expanded="true"]) {
    --y: translateY(calc(var(--lift) * var(--offset)));
    height: var(--initial-height)
}

:where([data-sonner-toast][data-removed="true"][data-front="true"][data-swipe-out="false"]) {
    --y: translateY(calc(var(--lift) * -100%));
    opacity: 0
}

:where([data-sonner-toast][data-removed="true"][data-front="false"][data-swipe-out="false"][data-expanded="true"]) {
    --y: translateY(calc(var(--lift) * var(--offset) + var(--lift) * -100%));
    opacity: 0
}

:where([data-sonner-toast][data-removed="true"][data-front="false"][data-swipe-out="false"][data-expanded="false"]) {
    --y: translateY(40%);
    opacity: 0;
    transition: transform .5s, opacity .2s
}

:where([data-sonner-toast][data-removed="true"][data-front="false"]):before {
    height: calc(var(--initial-height) + 20%)
}

[data-sonner-toast][data-swiping=true] {
    transform: var(--y) translateY(var(--swipe-amount-y, 0px)) translate(var(--swipe-amount-x, 0px));
    transition: none
}

[data-sonner-toast][data-swiped=true] {
    user-select: none
}

[data-sonner-toast][data-swipe-out=true][data-y-position=bottom],
[data-sonner-toast][data-swipe-out=true][data-y-position=top] {
    animation-duration: .2s;
    animation-timing-function: ease-out;
    animation-fill-mode: forwards
}

[data-sonner-toast][data-swipe-out=true][data-swipe-direction=left] {
    animation-name: swipe-out-left
}

[data-sonner-toast][data-swipe-out=true][data-swipe-direction=right] {
    animation-name: swipe-out-right
}

[data-sonner-toast][data-swipe-out=true][data-swipe-direction=up] {
    animation-name: swipe-out-up
}

[data-sonner-toast][data-swipe-out=true][data-swipe-direction=down] {
    animation-name: swipe-out-down
}

@keyframes swipe-out-left {
    0% {
        transform: var(--y) translate(var(--swipe-amount-x));
        opacity: 1
    }

    to {
        transform: var(--y) translate(calc(var(--swipe-amount-x) - 100%));
        opacity: 0
    }
}

@keyframes swipe-out-right {
    0% {
        transform: var(--y) translate(var(--swipe-amount-x));
        opacity: 1
    }

    to {
        transform: var(--y) translate(calc(var(--swipe-amount-x) + 100%));
        opacity: 0
    }
}

@keyframes swipe-out-up {
    0% {
        transform: var(--y) translateY(var(--swipe-amount-y));
        opacity: 1
    }

    to {
        transform: var(--y) translateY(calc(var(--swipe-amount-y) - 100%));
        opacity: 0
    }
}

@keyframes swipe-out-down {
    0% {
        transform: var(--y) translateY(var(--swipe-amount-y));
        opacity: 1
    }

    to {
        transform: var(--y) translateY(calc(var(--swipe-amount-y) + 100%));
        opacity: 0
    }
}

@media (max-width: 600px) {
    [data-sonner-toaster] {
        position: fixed;
        right: var(--mobile-offset-right);
        left: var(--mobile-offset-left);
        width: 100%
    }

    [data-sonner-toaster][dir=rtl] {
        left: calc(var(--mobile-offset-left) * -1)
    }

    [data-sonner-toaster] [data-sonner-toast] {
        left: 0;
        right: 0;
        width: calc(100% - var(--mobile-offset-left) * 2)
    }

    [data-sonner-toaster][data-x-position=left] {
        left: var(--mobile-offset-left)
    }

    [data-sonner-toaster][data-y-position=bottom] {
        bottom: var(--mobile-offset-bottom)
    }

    [data-sonner-toaster][data-y-position=top] {
        top: var(--mobile-offset-top)
    }

    [data-sonner-toaster][data-x-position=center] {
        left: var(--mobile-offset-left);
        right: var(--mobile-offset-right);
        transform: none
    }
}

[data-sonner-toaster][data-theme=light] {
    --normal-bg: #fff;
    --normal-border: var(--gray4);
    --normal-text: var(--gray12);
    --success-bg: hsl(143, 85%, 96%);
    --success-border: hsl(145, 92%, 91%);
    --success-text: hsl(140, 100%, 27%);
    --info-bg: hsl(208, 100%, 97%);
    --info-border: hsl(221, 91%, 91%);
    --info-text: hsl(210, 92%, 45%);
    --warning-bg: hsl(49, 100%, 97%);
    --warning-border: hsl(49, 91%, 91%);
    --warning-text: hsl(31, 92%, 45%);
    --error-bg: hsl(359, 100%, 97%);
    --error-border: hsl(359, 100%, 94%);
    --error-text: hsl(360, 100%, 45%)
}

[data-sonner-toaster][data-theme=light] [data-sonner-toast][data-invert=true] {
    --normal-bg: #000;
    --normal-border: hsl(0, 0%, 20%);
    --normal-text: var(--gray1)
}

[data-sonner-toaster][data-theme=dark] [data-sonner-toast][data-invert=true] {
    --normal-bg: #fff;
    --normal-border: var(--gray3);
    --normal-text: var(--gray12)
}

[data-sonner-toaster][data-theme=dark] {
    --normal-bg: #000;
    --normal-bg-hover: hsl(0, 0%, 12%);
    --normal-border: hsl(0, 0%, 20%);
    --normal-border-hover: hsl(0, 0%, 25%);
    --normal-text: var(--gray1);
    --success-bg: hsl(150, 100%, 6%);
    --success-border: hsl(147, 100%, 12%);
    --success-text: hsl(150, 86%, 65%);
    --info-bg: hsl(215, 100%, 6%);
    --info-border: hsl(223, 100%, 12%);
    --info-text: hsl(216, 87%, 65%);
    --warning-bg: hsl(64, 100%, 6%);
    --warning-border: hsl(60, 100%, 12%);
    --warning-text: hsl(46, 87%, 65%);
    --error-bg: hsl(358, 76%, 10%);
    --error-border: hsl(357, 89%, 16%);
    --error-text: hsl(358, 100%, 81%)
}

[data-sonner-toaster][data-theme=dark] [data-sonner-toast] [data-close-button] {
    background: var(--normal-bg);
    border-color: var(--normal-border);
    color: var(--normal-text)
}

[data-sonner-toaster][data-theme=dark] [data-sonner-toast] [data-close-button]:hover {
    background: var(--normal-bg-hover);
    border-color: var(--normal-border-hover)
}

[data-rich-colors=true][data-sonner-toast][data-type=success],
[data-rich-colors=true][data-sonner-toast][data-type=success] [data-close-button] {
    background: var(--success-bg);
    border-color: var(--success-border);
    color: var(--success-text)
}

[data-rich-colors=true][data-sonner-toast][data-type=info],
[data-rich-colors=true][data-sonner-toast][data-type=info] [data-close-button] {
    background: var(--info-bg);
    border-color: var(--info-border);
    color: var(--info-text)
}

[data-rich-colors=true][data-sonner-toast][data-type=warning],
[data-rich-colors=true][data-sonner-toast][data-type=warning] [data-close-button] {
    background: var(--warning-bg);
    border-color: var(--warning-border);
    color: var(--warning-text)
}

[data-rich-colors=true][data-sonner-toast][data-type=error],
[data-rich-colors=true][data-sonner-toast][data-type=error] [data-close-button] {
    background: var(--error-bg);
    border-color: var(--error-border);
    color: var(--error-text)
}

.sonner-loading-wrapper {
    --size: 16px;
    height: var(--size);
    width: var(--size);
    position: absolute;
    inset: 0;
    z-index: 10
}

.sonner-loading-wrapper[data-visible=false] {
    transform-origin: center;
    animation: sonner-fade-out .2s ease forwards
}

.sonner-spinner {
    position: relative;
    top: 50%;
    left: 50%;
    height: var(--size);
    width: var(--size)
}

.sonner-loading-bar {
    animation: sonner-spin 1.2s linear infinite;
    background: var(--gray11);
    border-radius: 6px;
    height: 8%;
    left: -10%;
    position: absolute;
    top: -3.9%;
    width: 24%
}

.sonner-loading-bar:nth-child(1) {
    animation-delay: -1.2s;
    transform: rotate(.0001deg) translate(146%)
}

.sonner-loading-bar:nth-child(2) {
    animation-delay: -1.1s;
    transform: rotate(30deg) translate(146%)
}

.sonner-loading-bar:nth-child(3) {
    animation-delay: -1s;
    transform: rotate(60deg) translate(146%)
}

.sonner-loading-bar:nth-child(4) {
    animation-delay: -.9s;
    transform: rotate(90deg) translate(146%)
}

.sonner-loading-bar:nth-child(5) {
    animation-delay: -.8s;
    transform: rotate(120deg) translate(146%)
}

.sonner-loading-bar:nth-child(6) {
    animation-delay: -.7s;
    transform: rotate(150deg) translate(146%)
}

.sonner-loading-bar:nth-child(7) {
    animation-delay: -.6s;
    transform: rotate(180deg) translate(146%)
}

.sonner-loading-bar:nth-child(8) {
    animation-delay: -.5s;
    transform: rotate(210deg) translate(146%)
}

.sonner-loading-bar:nth-child(9) {
    animation-delay: -.4s;
    transform: rotate(240deg) translate(146%)
}

.sonner-loading-bar:nth-child(10) {
    animation-delay: -.3s;
    transform: rotate(270deg) translate(146%)
}

.sonner-loading-bar:nth-child(11) {
    animation-delay: -.2s;
    transform: rotate(300deg) translate(146%)
}

.sonner-loading-bar:nth-child(12) {
    animation-delay: -.1s;
    transform: rotate(330deg) translate(146%)
}

@keyframes sonner-fade-in {
    0% {
        opacity: 0;
        transform: scale(.8)
    }

    to {
        opacity: 1;
        transform: scale(1)
    }
}

@keyframes sonner-fade-out {
    0% {
        opacity: 1;
        transform: scale(1)
    }

    to {
        opacity: 0;
        transform: scale(.8)
    }
}

@keyframes sonner-spin {
    0% {
        opacity: 1
    }

    to {
        opacity: .15
    }
}

@media (prefers-reduced-motion) {

    [data-sonner-toast],
    [data-sonner-toast]>*,
    .sonner-loading-bar {
        transition: none !important;
        animation: none !important
    }
}

.sonner-loader {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    transform-origin: center;
    transition: opacity .2s, transform .2s
}

.sonner-loader[data-visible=false] {
    opacity: 0;
    transform: scale(.8) translate(-50%, -50%)
}

</style>
</head>

<body>
  <div id="preloader">
  <div class="preloader-inner">
    <div class="preloader-ring"></div>
    <img src="assets/logo-3tb60inW.png" alt="L2RP" class="preloader-logo">
    <p class="preloader-text preloader-type">
  <span>C</span>
  <span>A</span>
  <span>R</span>
  <span>R</span>
  <span>E</span>
  <span>G</span>
  <span>A</span>
  <span>N</span>
  <span>D</span>
  <span>O</span>
  <span class="space"></span>
  <span>.</span>
  <span>.</span>
  <span>.</span>
</p>
  </div>
</div>
  <div id="root">
    <div role="region" aria-label="Notifications (F8)" tabindex="-1" style="pointer-events: none;">
      <ol tabindex="-1"
        class="fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]">
      </ol>
    </div>
    <section aria-label="Notifications alt+T" tabindex="-1" aria-live="polite" aria-relevant="additions text"
      aria-atomic="false">

    </section>
    <div class="relative min-h-screen bg-background">
      <div id="particles-container" class="fixed inset-0 pointer-events-none z-[1] overflow-hidden">
        <div class="particle"
          style="left: 27.4615%; width: 1.10816px; height: 1.10816px; background: rgb(44, 150, 88); box-shadow: rgba(44, 150, 88, 0.6) 0px 0px 3.32447px; animation-duration: 15.5532s; animation-delay: 0.601569s;">

        </div>
        <div class="particle"
          style="left: 33.9672%; width: 3.25234px; height: 3.25234px; background: rgb(44, 150, 88); box-shadow: rgba(44, 150, 88, 0.6) 0px 0px 9.75702px; animation-duration: 9.49984s; animation-delay: 4.98197s;">

        </div>
        <div class="particle"
          style="left: 6.74844%; width: 1.42368px; height: 1.42368px; background: rgb(231, 182, 35); box-shadow: rgba(231, 182, 35, 0.6) 0px 0px 4.27104px; animation-duration: 17.7427s; animation-delay: 3.08969s;">
        </div>
        <div class="particle"
          style="left: 89.009%; width: 1.62008px; height: 1.62008px; background: rgb(44, 150, 88); box-shadow: rgba(44, 150, 88, 0.6) 0px 0px 4.86023px; animation-duration: 12.1853s; animation-delay: 1.31828s;">
        </div>
        <div class="particle"
          style="left: 37.596%; width: 3.07073px; height: 3.07073px; background: rgb(44, 150, 88); box-shadow: rgba(44, 150, 88, 0.6) 0px 0px 9.21219px; animation-duration: 17.853s; animation-delay: 1.96944s;">
        </div>
        <div class="particle"
          style="left: 46.9705%; width: 2.66824px; height: 2.66824px; background: rgb(44, 150, 88); box-shadow: rgba(44, 150, 88, 0.6) 0px 0px 8.00471px; animation-duration: 13.7561s; animation-delay: 0.5756s;">
        </div>
        <div class="particle"
          style="left: 71.8702%; width: 1.55938px; height: 1.55938px; background: rgb(231, 182, 35); box-shadow: rgba(231, 182, 35, 0.6) 0px 0px 4.67813px; animation-duration: 11.7699s; animation-delay: 3.75495s;">
        </div>
        <div class="particle"
          style="left: 90.9943%; width: 1.06354px; height: 1.06354px; background: rgb(231, 182, 35); box-shadow: rgba(231, 182, 35, 0.6) 0px 0px 3.19062px; animation-duration: 10.7838s; animation-delay: 4.52556s;">
        </div>
        <div class="particle"
          style="left: 70.5697%; width: 3.1358px; height: 3.1358px; background: rgb(44, 150, 88); box-shadow: rgba(44, 150, 88, 0.6) 0px 0px 9.4074px; animation-duration: 16.3524s; animation-delay: 3.52898s;">
        </div>
        <div class="particle"
          style="left: 4.38057%; width: 2.48947px; height: 2.48947px; background: rgb(44, 150, 88); box-shadow: rgba(44, 150, 88, 0.6) 0px 0px 7.4684px; animation-duration: 13.4002s; animation-delay: 2.99149s;">
        </div>
        <div class="particle"
          style="left: 67.9858%; width: 2.88109px; height: 2.88109px; background: rgb(44, 150, 88); box-shadow: rgba(44, 150, 88, 0.6) 0px 0px 8.64327px; animation-duration: 16.3772s; animation-delay: 0.0241685s;">
        </div>
        <div class="particle"
          style="left: 83.3671%; width: 3.55916px; height: 3.55916px; background: rgb(231, 182, 35); box-shadow: rgba(231, 182, 35, 0.6) 0px 0px 10.6775px; animation-duration: 10.0896s; animation-delay: 1.88877s;">
        </div>
        <div class="particle"
          style="left: 62.1163%; width: 2.53477px; height: 2.53477px; background: rgb(44, 150, 88); box-shadow: rgba(44, 150, 88, 0.6) 0px 0px 7.6043px; animation-duration: 10.2978s; animation-delay: 2.79386s;">
        </div>
        <div class="particle"
          style="left: 17.0441%; width: 1.56353px; height: 1.56353px; background: rgb(231, 182, 35); box-shadow: rgba(231, 182, 35, 0.6) 0px 0px 4.6906px; animation-duration: 12.4379s; animation-delay: 1.69526s;">
        </div>
        <div class="particle"
          style="left: 62.771%; width: 1.05878px; height: 1.05878px; background: rgb(44, 150, 88); box-shadow: rgba(44, 150, 88, 0.6) 0px 0px 3.17634px; animation-duration: 12.4881s; animation-delay: 1.8284s;">
        </div>
        <div class="particle"
          style="left: 89.2423%; width: 3.59787px; height: 3.59787px; background: rgb(231, 182, 35); box-shadow: rgba(231, 182, 35, 0.6) 0px 0px 10.7936px; animation-duration: 17.4618s; animation-delay: 0.0530934s;">
        </div>
      </div>
      <div class="noise-overlay"></div>
      <div class="fog-layer"></div>
      <div class="fog-layer" style="animation-delay: 12s; bottom: 50px;"></div>
      <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-700 translate-y-0 opacity-100">
        <div class="nav-bar-bg relative">
          <div
            class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-[hsl(45_80%_52%/0.2)] to-transparent">
          </div>
          <div class="container mx-auto flex items-center px-4 h-16 lg:h-[72px]"><a href="#"
              class="relative group flex-shrink-0"><img src="/assets/logo-3tb60inW.png" alt="L2RP"
                class="h-14 lg:h-16 w-auto relative z-10 transition-transform duration-500 group-hover:scale-105 nav-logo-glow">
              <div class="nav-logo-shimmer"></div>
            </a>
            <nav class="hidden lg:flex items-center gap-0 flex-1 justify-center">
              <div class="flex items-center"><a href="#home" class="nav-item group"><span
                    class="nav-item-text">HOME</span><span class="nav-item-shimmer"></span><span
                    class="nav-item-glow"></span></a></div>
              <div class="flex items-center">
                <div class="nav-separator"></div><a href="#como-jogar" class="nav-item group"><span
                    class="nav-item-text">COMO JOGAR</span><span class="nav-item-shimmer"></span><span
                    class="nav-item-glow"></span></a>
              </div>
              <div class="flex items-center">
                <div class="nav-separator"></div><a href="#servidor" class="nav-item group"><span
                    class="nav-item-text">SERVIDOR</span><span class="nav-item-shimmer"></span><span
                    class="nav-item-glow"></span></a>
              </div>
              <div class="flex items-center">
                <div class="nav-separator"></div><a href="#download" class="nav-item group"><span
                    class="nav-item-text">DOWNLOADS</span><span class="nav-item-shimmer"></span><span
                    class="nav-item-glow"></span></a>
              </div>
              <div class="flex items-center">
                <div class="nav-separator"></div><a href="#regras" class="nav-item group"><span
                    class="nav-item-text">REGRAS</span><span class="nav-item-shimmer"></span><span
                    class="nav-item-glow"></span></a>
              </div>
              <div class="flex items-center">
                <div class="nav-separator"></div><a href="#doacoes" class="nav-item group"><span
                    class="nav-item-text">DOAÇÕES</span><span class="nav-item-shimmer"></span><span
                    class="nav-item-glow"></span></a>
              </div>
              <div class="flex items-center">
                <div class="nav-separator"></div><a href="#suporte" class="nav-item group"><span
                    class="nav-item-text">SUPORTE</span><span class="nav-item-shimmer"></span><span
                    class="nav-item-glow"></span></a>
              </div>
            </nav>
            <div class="flex items-center gap-2 flex-shrink-0 ml-auto">
  <button
    type="button"
    id="openLoginPanel"
    class="nav-mobile-btn w-9 h-9 lg:w-10 lg:h-10 flex items-center justify-center flex-shrink-0"
    title="Painel Admin">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
      fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
      class="lucide lucide-settings lg:w-[18px] lg:h-[18px]">
      <path
        d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
      </path>
      <circle cx="12" cy="12" r="3"></circle>
    </svg>
  </button>

  <button
    type="button"
    class="lg:hidden relative w-10 h-10 flex items-center justify-center nav-mobile-btn">
    <div class="transition-all duration-300">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu">
        <line x1="4" x2="20" y1="12" y2="12"></line>
        <line x1="4" x2="20" y1="6" y2="6"></line>
        <line x1="4" x2="20" y1="18" y2="18"></line>
      </svg>
    </div>
  </button>
</div>
          <div class="nav-bottom-border"></div>
        </div>
      </header>
      <div class="fixed inset-0 z-40 transition-opacity duration-500 lg:hidden opacity-0 pointer-events-none"
        style="background: rgba(4, 5, 6, 0.7); backdrop-filter: blur(4px);"></div>
      <div
        class="fixed top-0 right-0 z-50 h-full w-[280px] transition-transform duration-500 ease-[cubic-bezier(0.23,1,0.32,1)] lg:hidden translate-x-full">
        <div class="nav-mobile-panel h-full flex flex-col">
          <div class="flex items-center justify-between p-5 border-b border-[hsl(45_80%_52%/0.1)]"><img
              src="/assets/logo-3tb60inW.png" alt="L2RP" class="h-12 nav-logo-glow"><button
              class="nav-mobile-btn w-9 h-9 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg"
                width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
              </svg></button></div>
          <nav class="flex-1 py-4 overflow-y-auto"><a href="#home" class="nav-mobile-item"
              style="transition-delay: 0ms; opacity: 0; transform: translateX(20px);"><span
                class="nav-mobile-item-diamond"></span><span>HOME</span></a><a href="#como-jogar"
              class="nav-mobile-item" style="transition-delay: 0ms; opacity: 0; transform: translateX(20px);"><span
                class="nav-mobile-item-diamond"></span><span>COMO JOGAR</span></a><a href="#servidor"
              class="nav-mobile-item" style="transition-delay: 0ms; opacity: 0; transform: translateX(20px);"><span
                class="nav-mobile-item-diamond"></span><span>SERVIDOR</span></a><a href="#download"
              class="nav-mobile-item" style="transition-delay: 0ms; opacity: 0; transform: translateX(20px);"><span
                class="nav-mobile-item-diamond"></span><span>DOWNLOADS</span></a><a href="#regras"
              class="nav-mobile-item" style="transition-delay: 0ms; opacity: 0; transform: translateX(20px);"><span
                class="nav-mobile-item-diamond"></span><span>REGRAS</span></a><a href="#doacoes" class="nav-mobile-item"
              style="transition-delay: 0ms; opacity: 0; transform: translateX(20px);"><span
                class="nav-mobile-item-diamond"></span><span>DOAÇÕES</span></a><a href="#suporte"
              class="nav-mobile-item" style="transition-delay: 0ms; opacity: 0; transform: translateX(20px);"><span
                class="nav-mobile-item-diamond"></span><span>SUPORTE</span></a></nav>
          <div
            class="absolute top-0 left-0 w-[1px] h-full bg-gradient-to-b from-[hsl(45_80%_52%/0.3)] via-[hsl(145_55%_38%/0.15)] to-transparent">
          </div>
        </div>
      </div>
      <main class="relative z-10">
        <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden">
          <div class="absolute inset-0"><img src="assets/hero-bg-Zkalgoya.jpg" alt="Epic battle" width="1920"
              height="1080" class="w-full h-full object-cover" style="transform: scale(1.1);">
            <div class="absolute inset-0 bg-gradient-to-t from-background via-background/70 to-background/20"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-background/40 via-transparent to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-background/60 via-transparent to-background/60"></div>
            <div class="absolute inset-0" style="background: radial-gradient(transparent 40%, rgb(9, 10, 12) 100%);">
            </div>
            <div
              class="absolute bottom-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-primary/40 to-transparent">
            </div>
          </div>
          <div class="relative z-10 text-center px-4 max-w-5xl mx-auto pt-24 pb-20">
            <div class="mb-8 reveal-scale active"><img src="assets/logo-3tb60inW.png" alt="L2RP"
                class="h-32 sm:h-40 md:h-48 mx-auto drop-shadow-[0_0_30px_hsl(45_80%_52%/0.4)]"></div>
            <h1
              class="font-fantasy text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black leading-tight mb-4 gradient-text-gold text-glow-gold reveal active">
              SEU DESTINO COMEÇA AGORA ENTRE NA ERA DO L2 SUNSTROKE</h1>
            <p
              class="text-base sm:text-lg md:text-xl text-foreground/50 mb-10 max-w-xl mx-auto font-light tracking-wide reveal active">
              O CAMPO DE BATALHA DEFINITIVO É AQUI</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center reveal active"><a
                href="https://drive.google.com/file/d/1R6caPBBuWjRRObNX5QGnlSqI6GVFjMEA/view?usp=drive_link"
                class="game-btn text-sm sm:text-base">⚔️ JOGAR AGORA</a><a
                href="https://chat.whatsapp.com/EoRk9M16DOC5yJhNCfmoqY?mode=gi_t"
                class="game-btn-outline text-sm sm:text-base">💬 ENTRAR GRUPO WHATSAPP</a></div>
          </div>
          <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-40"><span
              class="font-fantasy text-[10px] tracking-[0.3em] text-primary">SCROLL</span>
            <div class="w-[1px] h-8 bg-gradient-to-b from-primary to-transparent animate-pulse"></div>
          </div>
        </section>
        <div class="section-divider"></div>
        <section id="countdown" class="py-20 px-4 relative overflow-hidden">
          <div class="absolute inset-0 pointer-events-none">
            <div
              class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-primary/5 blur-[150px]">
            </div>
          </div>
          <div class="container mx-auto max-w-4xl text-center relative z-10">
            <div class="mb-2"><span
                class="inline-block px-4 py-1.5 rounded-full border border-primary/20 bg-primary/5 text-primary text-xs font-semibold tracking-[0.2em]">INAUGURAÇÃO</span>
            </div>
            <h2 class="font-fantasy text-2xl md:text-4xl font-bold gradient-text-gold mb-2">EM BREVE</h2>
            <p class="text-foreground/50 text-sm md:text-base mb-10">Prepare-se! O servidor será inaugurado em:</p>
            <div class="flex justify-center gap-3 sm:gap-5 md:gap-8 mb-10">
              <div class="flex flex-col items-center">
                <div
                  class="relative w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 flex items-center justify-center rounded-lg border border-primary/15 bg-card/80 backdrop-blur-sm"
                  style="box-shadow: rgba(231, 182, 35, 0.06) 0px 0px 30px, rgba(231, 182, 35, 0.08) 0px 1px 0px inset;">
                  <span
                    class="font-fantasy text-2xl sm:text-3xl md:text-4xl font-black gradient-text-gold tabular-nums">19</span>
                  <div
                    class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-primary/20 to-transparent">
                  </div>
                </div><span
                  class="mt-2 text-[10px] sm:text-xs tracking-[0.15em] text-foreground/40 font-semibold">DIAS</span>
              </div>
              <div class="flex items-center text-primary/30 font-fantasy text-2xl md:text-4xl mt-[-20px]">:</div>
              <div class="flex flex-col items-center">
                <div
                  class="relative w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 flex items-center justify-center rounded-lg border border-primary/15 bg-card/80 backdrop-blur-sm"
                  style="box-shadow: rgba(231, 182, 35, 0.06) 0px 0px 30px, rgba(231, 182, 35, 0.08) 0px 1px 0px inset;">
                  <span
                    class="font-fantasy text-2xl sm:text-3xl md:text-4xl font-black gradient-text-gold tabular-nums">00</span>
                  <div
                    class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-primary/20 to-transparent">
                  </div>
                </div><span
                  class="mt-2 text-[10px] sm:text-xs tracking-[0.15em] text-foreground/40 font-semibold">HORAS</span>
              </div>
              <div class="flex items-center text-primary/30 font-fantasy text-2xl md:text-4xl mt-[-20px]">:</div>
              <div class="flex flex-col items-center">
                <div
                  class="relative w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 flex items-center justify-center rounded-lg border border-primary/15 bg-card/80 backdrop-blur-sm"
                  style="box-shadow: rgba(231, 182, 35, 0.06) 0px 0px 30px, rgba(231, 182, 35, 0.08) 0px 1px 0px inset;">
                  <span
                    class="font-fantasy text-2xl sm:text-3xl md:text-4xl font-black gradient-text-gold tabular-nums">14</span>
                  <div
                    class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-primary/20 to-transparent">
                  </div>
                </div><span
                  class="mt-2 text-[10px] sm:text-xs tracking-[0.15em] text-foreground/40 font-semibold">MIN</span>
              </div>
              <div class="flex items-center text-primary/30 font-fantasy text-2xl md:text-4xl mt-[-20px]">:</div>
              <div class="flex flex-col items-center">
                <div
                  class="relative w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 flex items-center justify-center rounded-lg border border-primary/15 bg-card/80 backdrop-blur-sm"
                  style="box-shadow: rgba(231, 182, 35, 0.06) 0px 0px 30px, rgba(231, 182, 35, 0.08) 0px 1px 0px inset;">
                  <span
                    class="font-fantasy text-2xl sm:text-3xl md:text-4xl font-black gradient-text-gold tabular-nums">01</span>
                  <div
                    class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-primary/20 to-transparent">
                  </div>
                </div><span
                  class="mt-2 text-[10px] sm:text-xs tracking-[0.15em] text-foreground/40 font-semibold">SEG</span>
              </div>
            </div>
            <p class="text-foreground/30 text-xs">Fuso horário: Brasília (UTC-3)</p>
          </div>
        <!--</section>
        <div class="section-divider"></div>
        <section class="relative py-24 px-4 overflow-hidden">
          <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-[600px] h-[400px] rounded-full bg-secondary/5 blur-[100px]"></div>
          </div>
          <div class="container mx-auto max-w-4xl relative z-10">
            <div class="game-card game-card-ornate text-center reveal-scale"><span class="corner corner-tl"></span><span
                class="corner corner-tr"></span><span class="corner corner-bl"></span><span
                class="corner corner-br"></span>
              <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-secondary/10 border border-secondary/30 mb-6 pulse-ring">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-mic w-10 h-10 text-secondary icon-glow-emerald">
                  <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
                  <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                  <line x1="12" x2="12" y1="19" y2="22"></line>
                </svg></div>
              <h2 class="font-fantasy text-2xl sm:text-3xl md:text-4xl font-bold gradient-text-gold mb-3">COMUNICAÇÃO
                POR VOZ</h2>
              <p class="text-lg text-secondary font-fantasy tracking-wider mb-3">Dentro do jogo, por proximidade</p>
              <p class="text-muted-foreground max-w-lg mx-auto mb-8 leading-relaxed">Fale com jogadores próximos dentro
                do jogo sem precisar de Discord. Uma experiência imersiva e única no Lineage 2.</p>
              <div class="flex flex-wrap justify-center gap-8">
                <div class="flex items-center gap-2.5 text-primary/80">
                  <div class="p-2 rounded bg-primary/5 border border-primary/10"><svg xmlns="http://www.w3.org/2000/svg"
                      width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mic w-4 h-4 icon-glow-gold">
                      <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
                      <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                      <line x1="12" x2="12" y1="19" y2="22"></line>
                    </svg></div><span class="font-fantasy text-xs tracking-[0.15em]">Voz em tempo real</span>
                </div>
                <div class="flex items-center gap-2.5 text-primary/80">
                  <div class="p-2 rounded bg-primary/5 border border-primary/10"><svg xmlns="http://www.w3.org/2000/svg"
                      width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-radio w-4 h-4 icon-glow-gold">
                      <path d="M4.9 19.1C1 15.2 1 8.8 4.9 4.9"></path>
                      <path d="M7.8 16.2c-2.3-2.3-2.3-6.1 0-8.5"></path>
                      <circle cx="12" cy="12" r="2"></circle>
                      <path d="M16.2 7.8c2.3 2.3 2.3 6.1 0 8.5"></path>
                      <path d="M19.1 4.9C23 8.8 23 15.1 19.1 19"></path>
                    </svg></div><span class="font-fantasy text-xs tracking-[0.15em]">Por proximidade</span>
                </div>
                <div class="flex items-center gap-2.5 text-primary/80">
                  <div class="p-2 rounded bg-primary/5 border border-primary/10"><svg xmlns="http://www.w3.org/2000/svg"
                      width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-4 h-4 icon-glow-gold">
                      <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                      <circle cx="9" cy="7" r="4"></circle>
                      <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                      <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg></div><span class="font-fantasy text-xs tracking-[0.15em]">Imersão total</span>
                </div>
              </div>
            </div>
          </div>
        </section>-->
        <div class="section-divider"></div>
        <section id="servidor" class="py-24 px-4 relative">
          <div class="absolute inset-0 pointer-events-none">
            <div
              class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[600px] rounded-full bg-primary/3 blur-[120px]">
            </div>
          </div>
          <div class="container mx-auto max-w-6xl relative z-10">
            <div class="text-center mb-14">
              <h2 class="font-fantasy text-3xl md:text-4xl font-bold gradient-text-gold mb-3 reveal">CONFIGURAÇÕES DO
                SERVIDOR</h2>
              <div class="section-divider max-w-xs mx-auto reveal"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
              <div class="game-card game-card-ornate reveal" style="transition-delay: 0ms;"><span
                  class="corner corner-tl"></span><span class="corner corner-tr"></span><span
                  class="corner corner-bl"></span><span class="corner corner-br"></span>
                <div class="flex items-center gap-3 mb-5">
                  <div class="p-2.5 rounded border bg-primary/10 border-primary/20"><svg
                      xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-swords w-5 h-5 text-primary icon-glow-gold">
                      <polyline points="14.5 17.5 3 6 3 3 6 3 17.5 14.5"></polyline>
                      <line x1="13" x2="19" y1="19" y2="13"></line>
                      <line x1="16" x2="20" y1="16" y2="20"></line>
                      <line x1="19" x2="21" y1="21" y2="19"></line>
                      <polyline points="14.5 6.5 18 3 21 3 21 6 17.5 9.5"></polyline>
                      <line x1="5" x2="9" y1="14" y2="18"></line>
                      <line x1="7" x2="4" y1="17" y2="20"></line>
                      <line x1="3" x2="5" y1="19" y2="21"></line>
                    </svg></div>
                  <h3 class="font-fantasy text-sm font-bold tracking-[0.15em] text-primary">INFORMAÇÕES GERAIS</h3>
                </div>
                <ul class="space-y-2.5">
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-primary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Rate: 300x</span></li>
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-primary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Crônica: Interlude</span></li>
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-primary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Estilo: PvP</span></li>
                </ul>
              </div>
              <div class="game-card game-card-ornate reveal" style="transition-delay: 80ms;"><span
                  class="corner corner-tl"></span><span class="corner corner-tr"></span><span
                  class="corner corner-bl"></span><span class="corner corner-br"></span>
                <div class="flex items-center gap-3 mb-5">
                  <div class="p-2.5 rounded border bg-secondary/10 border-secondary/20"><svg
                      xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-shield w-5 h-5 text-secondary icon-glow-emerald">
                      <path
                        d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                      </path>
                    </svg></div>
                  <h3 class="font-fantasy text-sm font-bold tracking-[0.15em] text-secondary">ENCHANT</h3>
                </div>
                <ul class="space-y-2.5">
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-secondary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Safe: +5</span></li>
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-secondary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Max Weapon: +12</span></li>
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-secondary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Max Armor: +10</span></li>
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-secondary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Max Jewels: +10</span></li>
                </ul>
              </div>
              <div class="game-card game-card-ornate reveal" style="transition-delay: 160ms;"><span
                  class="corner corner-tl"></span><span class="corner corner-tr"></span><span
                  class="corner corner-bl"></span><span class="corner corner-br"></span>
                <div class="flex items-center gap-3 mb-5">
                  <div class="p-2.5 rounded border bg-primary/10 border-primary/20"><svg
                      xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-trophy w-5 h-5 text-primary icon-glow-gold">
                      <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                      <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                      <path d="M4 22h16"></path>
                      <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                      <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                      <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                    </svg></div>
                  <h3 class="font-fantasy text-sm font-bold tracking-[0.15em] text-primary">SISTEMAS</h3>
                </div>
                <ul class="space-y-2.5">
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-primary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Olimpíadas: Grade S</span></li>
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-primary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Eventos automáticos (TVT, PvP, Dungeon)</span></li>
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-primary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Sistema Solo Boss</span></li>
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-primary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Farm Solo e Party</span></li>
                </ul>
              </div>
              <div class="game-card game-card-ornate reveal" style="transition-delay: 240ms;"><span
                  class="corner corner-tl"></span><span class="corner corner-tr"></span><span
                  class="corner corner-bl"></span><span class="corner corner-br"></span>
                <div class="flex items-center gap-3 mb-5">
                  <div class="p-2.5 rounded border bg-secondary/10 border-secondary/20"><svg
                      xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-backpack w-5 h-5 text-secondary icon-glow-emerald">
                      <path d="M4 10a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"></path>
                      <path d="M8 10h8"></path>
                      <path d="M8 18h8"></path>
                      <path d="M8 22v-6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v6"></path>
                      <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"></path>
                    </svg></div>
                  <h3 class="font-fantasy text-sm font-bold tracking-[0.15em] text-secondary">ITENS INICIAIS</h3>
                </div>
                <ul class="space-y-2.5">
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-secondary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Set iniciante: Titanium</span></li>
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-secondary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Arma inicial: Grade S Black + Epic</span></li>
                </ul>
              </div>
              <div class="game-card game-card-ornate reveal" style="transition-delay: 320ms;"><span
                  class="corner corner-tl"></span><span class="corner corner-tr"></span><span
                  class="corner corner-bl"></span><span class="corner corner-br"></span>
                <div class="flex items-center gap-3 mb-5">
                  <div class="p-2.5 rounded border bg-primary/10 border-primary/20"><svg
                      xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-flame w-5 h-5 text-primary icon-glow-gold">
                      <path
                        d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z">
                      </path>
                    </svg></div>
                  <h3 class="font-fantasy text-sm font-bold tracking-[0.15em] text-primary">ITENS TOP</h3>
                </div>
                <ul class="space-y-2.5">
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-primary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Set Top: DK</span></li>
                  <li class="flex items-center gap-3 text-foreground/80"><span
                      class="w-1.5 h-1.5 rounded-full bg-primary shrink-0 shadow-[0_0_6px_currentColor]"></span><span
                      class="text-sm">Arma Top: Icarus</span></li>
                </ul>
              </div>
            </div>
          </div>
        </section>
        <div class="section-divider"></div>
        <section class="py-24 px-4 relative animated-bg">
          <div class="container mx-auto max-w-6xl relative z-10">
            <div class="text-center mb-14">
              <h2 class="font-fantasy text-3xl md:text-4xl font-bold gradient-text-gold mb-3 reveal">FEATURES</h2>
              <div class="section-divider max-w-xs mx-auto reveal"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
              <div class="game-card text-center reveal" style="transition-delay: 0ms;">
                <div
                  class="inline-flex items-center justify-center w-14 h-14 rounded-full border mb-5 bg-primary/8 border-primary/20">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-swords w-7 h-7 text-primary icon-glow-gold">
                    <polyline points="14.5 17.5 3 6 3 3 6 3 17.5 14.5"></polyline>
                    <line x1="13" x2="19" y1="19" y2="13"></line>
                    <line x1="16" x2="20" y1="16" y2="20"></line>
                    <line x1="19" x2="21" y1="21" y2="19"></line>
                    <polyline points="14.5 6.5 18 3 21 3 21 6 17.5 9.5"></polyline>
                    <line x1="5" x2="9" y1="14" y2="18"></line>
                    <line x1="7" x2="4" y1="17" y2="20"></line>
                    <line x1="3" x2="5" y1="19" y2="21"></line>
                  </svg></div>
                <h3 class="font-fantasy text-base font-bold mb-2 tracking-wider text-primary">PvP Intenso</h3>
                <p class="text-muted-foreground text-sm leading-relaxed">Combates épicos e estratégicos entre jogadores
                </p>
              </div>
              <div class="game-card text-center reveal" style="transition-delay: 80ms;">
                <div
                  class="inline-flex items-center justify-center w-14 h-14 rounded-full border mb-5 bg-secondary/8 border-secondary/20">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-zap w-7 h-7 text-secondary icon-glow-emerald">
                    <path
                      d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z">
                    </path>
                  </svg></div>
                <h3 class="font-fantasy text-base font-bold mb-2 tracking-wider text-secondary">Eventos Automáticos</h3>
                <p class="text-muted-foreground text-sm leading-relaxed">TVT, PvP e Dungeons acontecem automaticamente
                </p>
              </div>
              <div class="game-card text-center reveal" style="transition-delay: 160ms;">
                <div
                  class="inline-flex items-center justify-center w-14 h-14 rounded-full border mb-5 bg-primary/8 border-primary/20">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-skull w-7 h-7 text-primary icon-glow-gold">
                    <path d="m12.5 17-.5-1-.5 1h1z"></path>
                    <path
                      d="M15 22a1 1 0 0 0 1-1v-1a2 2 0 0 0 1.56-3.25 8 8 0 1 0-11.12 0A2 2 0 0 0 8 20v1a1 1 0 0 0 1 1z">
                    </path>
                    <circle cx="15" cy="12" r="1"></circle>
                    <circle cx="9" cy="12" r="1"></circle>
                  </svg></div>
                <h3 class="font-fantasy text-base font-bold mb-2 tracking-wider text-primary">Sistema Solo Boss</h3>
                <p class="text-muted-foreground text-sm leading-relaxed">Enfrente bosses poderosos sozinho e ganhe
                  recompensas</p>
              </div>
              <div class="game-card text-center reveal" style="transition-delay: 240ms;">
                <div
                  class="inline-flex items-center justify-center w-14 h-14 rounded-full border mb-5 bg-secondary/8 border-secondary/20">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-trending-up w-7 h-7 text-secondary icon-glow-emerald">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                  </svg></div>
                <h3 class="font-fantasy text-base font-bold mb-2 tracking-wider text-secondary">Progressão Equilibrada
                </h3>
                <p class="text-muted-foreground text-sm leading-relaxed">Evolua de forma justa e divertida</p>
              </div>
              <div class="game-card text-center reveal" style="transition-delay: 320ms;">
                <div
                  class="inline-flex items-center justify-center w-14 h-14 rounded-full border mb-5 bg-primary/8 border-primary/20">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-coins w-7 h-7 text-primary icon-glow-gold">
                    <circle cx="8" cy="8" r="6"></circle>
                    <path d="M18.09 10.37A6 6 0 1 1 10.34 18"></path>
                    <path d="M7 6h1v4"></path>
                    <path d="m16.71 13.88.7.71-2.82 2.82"></path>
                  </svg></div>
                <h3 class="font-fantasy text-base font-bold mb-2 tracking-wider text-primary">Economia Dinâmica</h3>
                <p class="text-muted-foreground text-sm leading-relaxed">Mercado ativo com trocas e negociações</p>
              </div>
            </div>
          </div>
        </section>
        <div class="section-divider"></div>
        <section class="py-24 px-4 relative">
          <div class="absolute inset-0 pointer-events-none">
            <div
              class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] rounded-full bg-primary/3 blur-[100px]">
            </div>
          </div>
          <div class="container mx-auto max-w-4xl relative z-10">
            <div class="text-center mb-14">
              <h2 class="font-fantasy text-3xl md:text-4xl font-bold gradient-text-gold mb-3 reveal">STATUS DO SERVIDOR
              </h2>
              <div class="section-divider max-w-xs mx-auto reveal"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div class="game-card game-card-ornate text-center reveal"><span class="corner corner-tl"></span><span
                  class="corner corner-tr"></span><span class="corner corner-bl"></span><span
                  class="corner corner-br"></span>
                <div
                  class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-primary/10 border border-primary/20 mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-users w-7 h-7 text-primary icon-glow-gold">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                  </svg></div>
                <div class="font-fantasy text-4xl font-black gradient-text-gold mb-1">0</div>
                <div class="text-muted-foreground text-xs font-fantasy tracking-[0.2em]">PLAYERS ONLINE</div>
              </div>
              <div class="game-card game-card-ornate text-center reveal" style="transition-delay: 100ms;"><span
                  class="corner corner-tl"></span><span class="corner corner-tr"></span><span
                  class="corner corner-bl"></span><span class="corner corner-br"></span>
                <div
                  class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-secondary/10 border border-secondary/20 mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-server w-7 h-7 text-secondary icon-glow-emerald">
                    <rect width="20" height="8" x="2" y="2" rx="2" ry="2"></rect>
                    <rect width="20" height="8" x="2" y="14" rx="2" ry="2"></rect>
                    <line x1="6" x2="6.01" y1="6" y2="6"></line>
                    <line x1="6" x2="6.01" y1="18" y2="18"></line>
                  </svg></div>
                <div
                  class="font-fantasy text-4xl font-black text-secondary mb-1 flex items-center justify-center gap-2">
                  <span class="w-3 h-3 rounded-full bg-emerald-400 pulse-ring"></span>ONLINE</div>
                <div class="text-muted-foreground text-xs font-fantasy tracking-[0.2em]">SERVER STATUS</div>
              </div>
              <div class="game-card game-card-ornate text-center reveal" style="transition-delay: 200ms;"><span
                  class="corner corner-tl"></span><span class="corner corner-tr"></span><span
                  class="corner corner-bl"></span><span class="corner corner-br"></span>
                <div
                  class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-primary/10 border border-primary/20 mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-activity w-7 h-7 text-primary icon-glow-gold">
                    <path
                      d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2">
                    </path>
                  </svg></div>
                <div class="font-fantasy text-4xl font-black gradient-text-gold mb-1">0ms</div>
                <div class="text-muted-foreground text-xs font-fantasy tracking-[0.2em]">PING</div>
              </div>
            </div>
          </div>
        </section>
        <div class="section-divider"></div>
        <section id="como-jogar" class="py-24 px-4 relative animated-bg">
          <div class="container mx-auto max-w-5xl relative z-10">
            <div class="text-center mb-14">
              <h2 class="font-fantasy text-3xl md:text-4xl font-bold gradient-text-gold mb-3 reveal">COMO JOGAR</h2>
              <div class="section-divider max-w-xs mx-auto reveal"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-14">
              <div class="game-card text-center reveal" style="transition-delay: 0ms;">
                <div class="font-fantasy text-[10px] tracking-[0.3em] text-primary/40 mb-3">PASSO</div>
                <div class="font-fantasy text-3xl font-black gradient-text-gold mb-4">01</div>
                <div
                  class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary/8 border border-primary/15 mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-download w-6 h-6 text-primary icon-glow-gold">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" x2="12" y1="15" y2="3"></line>
                  </svg></div>
                <h3 class="font-fantasy text-sm font-bold text-foreground mb-1.5 tracking-wider">Baixe o jogo</h3>
                <p class="text-muted-foreground text-xs leading-relaxed">Faça o download do cliente completo</p>
              </div>
              <div class="game-card text-center reveal" style="transition-delay: 100ms;">
                <div class="font-fantasy text-[10px] tracking-[0.3em] text-primary/40 mb-3">PASSO</div>
                <div class="font-fantasy text-3xl font-black gradient-text-gold mb-4">02</div>
                <div
                  class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary/8 border border-primary/15 mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-monitor w-6 h-6 text-primary icon-glow-gold">
                    <rect width="20" height="14" x="2" y="3" rx="2"></rect>
                    <line x1="8" x2="16" y1="21" y2="21"></line>
                    <line x1="12" x2="12" y1="17" y2="21"></line>
                  </svg></div>
                <h3 class="font-fantasy text-sm font-bold text-foreground mb-1.5 tracking-wider">Abra o cliente</h3>
                <p class="text-muted-foreground text-xs leading-relaxed">Execute o launcher do L2RP</p>
              </div>
              <div class="game-card text-center reveal" style="transition-delay: 200ms;">
                <div class="font-fantasy text-[10px] tracking-[0.3em] text-primary/40 mb-3">PASSO</div>
                <div class="font-fantasy text-3xl font-black gradient-text-gold mb-4">03</div>
                <div
                  class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary/8 border border-primary/15 mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-user-plus w-6 h-6 text-primary icon-glow-gold">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="19" x2="19" y1="8" y2="14"></line>
                    <line x1="22" x2="16" y1="11" y2="11"></line>
                  </svg></div>
                <h3 class="font-fantasy text-sm font-bold text-foreground mb-1.5 tracking-wider">Crie login e senha</h3>
                <p class="text-muted-foreground text-xs leading-relaxed">Digite dentro do jogo</p>
              </div>
              <div class="game-card text-center reveal" style="transition-delay: 300ms;">
                <div class="font-fantasy text-[10px] tracking-[0.3em] text-primary/40 mb-3">PASSO</div>
                <div class="font-fantasy text-3xl font-black gradient-text-gold mb-4">04</div>
                <div
                  class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary/8 border border-primary/15 mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-gamepad2 w-6 h-6 text-primary icon-glow-gold">
                    <line x1="6" x2="10" y1="11" y2="11"></line>
                    <line x1="8" x2="8" y1="9" y2="13"></line>
                    <line x1="15" x2="15.01" y1="12" y2="12"></line>
                    <line x1="18" x2="18.01" y1="10" y2="10"></line>
                    <path
                      d="M17.32 5H6.68a4 4 0 0 0-3.978 3.59c-.006.052-.01.101-.017.152C2.604 9.416 2 14.456 2 16a3 3 0 0 0 3 3c1 0 1.5-.5 2-1l1.414-1.414A2 2 0 0 1 9.828 16h4.344a2 2 0 0 1 1.414.586L17 18c.5.5 1 1 2 1a3 3 0 0 0 3-3c0-1.545-.604-6.584-.685-7.258-.007-.05-.011-.1-.017-.151A4 4 0 0 0 17.32 5z">
                    </path>
                  </svg></div>
                <h3 class="font-fantasy text-sm font-bold text-foreground mb-1.5 tracking-wider">Entre e jogue!</h3>
                <p class="text-muted-foreground text-xs leading-relaxed">Sua aventura começa agora</p>
              </div>
            </div>
            <div class="game-card game-card-ornate text-center max-w-2xl mx-auto reveal-scale"><span
                class="corner corner-tl"></span><span class="corner corner-tr"></span><span
                class="corner corner-bl"></span><span class="corner corner-br"></span>
              <div class="font-fantasy text-xl sm:text-2xl font-bold text-secondary mb-3 text-glow-emerald">🔥 AUTO
                CREATE ATIVO</div>
              <p class="text-foreground mb-2">Não é necessário criar conta no site.</p>
              <p class="text-muted-foreground text-sm leading-relaxed">Basta entrar no jogo com qualquer login e senha
                que sua conta será criada automaticamente.</p>
            </div>
          </div>
        </section>
        <div class="section-divider"></div>
        <section id="download" class="py-24 px-4 relative">
          <div class="absolute inset-0 pointer-events-none">
            <div
              class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] rounded-full bg-primary/4 blur-[120px]">
            </div>
          </div>
          <div class="container mx-auto max-w-3xl text-center relative z-10">
            <h2 class="font-fantasy text-3xl md:text-4xl font-bold gradient-text-gold mb-4 reveal">PRONTO PARA JOGAR?
            </h2>
            <div class="section-divider max-w-xs mx-auto mb-6 reveal"></div>
            <p class="text-muted-foreground text-lg mb-10 reveal">Baixe o cliente e entre no mundo de L2RP agora mesmo.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center reveal"><a
                href="https://drive.google.com/file/d/1R6caPBBuWjRRObNX5QGnlSqI6GVFjMEA/view?usp=drive_link"
                class="game-btn text-sm sm:text-base">⬇️ DOWNLOAD CLIENTE</a><a
                href="https://chat.whatsapp.com/EoRk9M16DOC5yJhNCfmoqY?mode=gi_t"
                class="game-btn-outline text-sm sm:text-base">💬 ENTRAR GRUPO WHATSAPP</a></div>
          </div>
        </section>
      </main>
      <footer class="relative border-t border-primary/10 py-12 px-4">
        <div
          class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-primary/30 to-transparent">
        </div>
        <div class="container mx-auto max-w-5xl text-center relative z-10"><img src="/assets/logo-3tb60inW.png"
            alt="L2RP" class="h-16 mx-auto mb-4 drop-shadow-[0_0_12px_hsl(45_80%_52%/0.3)]">
          <p class="text-muted-foreground text-xs tracking-wider mb-5">© 2025 L2RP — Todos os direitos reservados.</p>
          <div class="flex justify-center gap-8"><a href="https://chat.whatsapp.com/EoRk9M16DOC5yJhNCfmoqY?mode=gi_t"
              target="_blank" rel="noopener noreferrer"
              class="text-muted-foreground hover:text-primary transition-all duration-300 text-xs font-fantasy tracking-[0.15em] hover:drop-shadow-[0_0_8px_hsl(45_80%_52%/0.4)]">WHATSAPP</a><a
              href="https://www.instagram.com/l2realplayer?igsh=Nm03Zm1uOHN4dzh0&amp;utm_source=qr" target="_blank"
              rel="noopener noreferrer"
              class="text-muted-foreground hover:text-primary transition-all duration-300 text-xs font-fantasy tracking-[0.15em] hover:drop-shadow-[0_0_8px_hsl(45_80%_52%/0.4)]">INSTAGRAM</a><a
              href="https://youtube.com/@regysoficial?si=c7n7mXPUbUyIj6E4" target="_blank" rel="noopener noreferrer"
              class="text-muted-foreground hover:text-primary transition-all duration-300 text-xs font-fantasy tracking-[0.15em] hover:drop-shadow-[0_0_8px_hsl(45_80%_52%/0.4)]">YOUTUBE</a>
          </div>
        </div>
      </footer>
    </div>
  </div>

<script>
  const PRELOADER_MIN_TIME = 5000; // mínimo
  let startTime = Date.now();

  window.addEventListener("load", function () {
    const preloader = document.getElementById("preloader");

    let elapsed = Date.now() - startTime;
    let remaining = PRELOADER_MIN_TIME - elapsed;

    setTimeout(() => {
      preloader.classList.add("hidden");

      setTimeout(() => {
        preloader.remove();
      }, 700);
    }, remaining > 0 ? remaining : 0);
  });
</script>
<!-- PAINEL USER / LOGIN -->
<div id="loginPanelOverlay"
     class="fixed inset-0 z-[9998] opacity-0 pointer-events-none transition-opacity duration-500"
     style="background: hsla(220,15%,2%,0.8); backdrop-filter: blur(4px);"></div>

<div id="loginPanel"
     class="fixed top-0 right-0 z-[9999] h-full w-full sm:w-[420px] translate-x-full transition-transform duration-500">

    <div class="h-full flex flex-col"
         style="background: rgba(10, 12, 15, 0.98); backdrop-filter: blur(24px); border-left: 1px solid rgba(231, 182, 35, 0.1);">

        <div class="flex items-center justify-between p-5"
             style="border-bottom: 1px solid rgba(231, 182, 35, 0.1);">
            <h2 class="font-fantasy text-sm tracking-[0.2em] gradient-text-gold font-bold">
                PAINEL USER
            </h2>

            <button type="button" id="loginPanelClose" class="nav-mobile-btn w-8 h-8 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-x">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-6 flex flex-col items-center justify-center h-full">
                <div class="w-full max-w-xs space-y-5">
<?php
if (empty($_SESSION['lkey'])) {
    $_SESSION['lkey'] = md5(time().rand(100,999).$uniqueKey);
}
?>



                    <?php if ($logged != 1) { ?>

                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4 bg-primary/10 border border-primary/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     class="lucide lucide-lock w-7 h-7 text-primary icon-glow-gold">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </div>

                            <p class="text-muted-foreground text-sm">
                                Faça login para acessar o painel.
                            </p>
                        </div>

                        <div class="top-login">
                           <form 
    id="top-login-form"
    action="./?engine=login"
    method="POST"
>
    <?php
    $_SESSION['lkey'] = md5(time().rand(100,999).$uniqueKey);
    ?>

    <input type="hidden" name="lkey" value="<?php echo $_SESSION['lkey']; ?>">
    <input type="hidden" name="captcha" id="ucp_captcha" value="">
    <input type="hidden" name="ucp_uniqid" value="<?php echo md5(uniqid()); ?>">

    <input type="text" name="ucp_login" placeholder="Login" autocomplete="off" required
           class="w-full px-4 py-3 rounded bg-muted border border-border text-foreground text-sm">

    <input type="password" name="ucp_passw" placeholder="Senha" autocomplete="off" required
           class="w-full px-4 py-3 rounded bg-muted border border-border text-foreground text-sm">

    <?php if ($captcha_cp_on == 1) { ?>
        <button type="button" class="game-btn w-full text-xs" onclick="opencaptcha();">
            ENTRAR
        </button>
    <?php } else { ?>
        <button type="submit" class="game-btn w-full text-xs">
            ENTRAR
        </button>
    <?php } ?>
</form>
                        </div>

                    <?php } else { ?>

                        <?php if (file_exists('ucp/index.php')) { ?>
                            <div class="top-logged text-center space-y-3">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-2 bg-primary/10 border border-primary/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="lucide lucide-lock w-7 h-7 text-primary icon-glow-gold">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </div>

                                <span class="user block text-foreground/70 text-sm"><?php echo $_SESSION['acc']; ?></span>
                                <a href="#" class="game-btn w-full text-xs" onclick="openPageModal('./ucp'); return false;">DASHBOARD</a>
                                <a href="./?engine=logout" class="game-btn-outline w-full text-xs"><?php echo $LANG[12023]; ?></a>
                            </div>
                        <?php } else { ?>
                            <div class="top-logged dropdown-account text-center space-y-3">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-2 bg-primary/10 border border-primary/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="lucide lucide-lock w-7 h-7 text-primary icon-glow-gold">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </div>

                                <span class="user block text-foreground/70 text-sm"><?php echo $_SESSION['acc']; ?></span>

                                <div class="account-menu flex flex-col gap-3">
                                    <a href="#" class="game-btn-outline w-full text-xs" onclick="openUcpChangePass(); return false;"><?php echo $LANG[12022]; ?></a>

                                    <?php if ($chaemail == 1) { ?>
                                        <a href="#" class="game-btn-outline w-full text-xs" onclick="openUcpEmailChange(); return false;"><?php echo $LANG[11014]; ?></a>
                                    <?php } ?>

                                    <?php if ($dpage['unstuk'] == 1) { ?>
                                        <a href="#" class="game-btn-outline w-full text-xs" onclick="openUcpUnstuck(); return false;">Unstuck Char</a>
                                    <?php } ?>

                                    <a href="./?engine=logout" class="game-btn-outline w-full text-xs"><?php echo $LANG[12023]; ?></a>
                                </div>
                            </div>
                        <?php } ?>

                    <?php } ?>

                </div>
            </div>
        </div>

        <div class="absolute top-0 left-0 w-[1px] h-full"
             style="background: linear-gradient(rgba(231, 182, 35, 0.3), rgba(44, 150, 88, 0.15), transparent);">
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const openBtn = document.getElementById('openLoginPanel');
    const closeBtn = document.getElementById('loginPanelClose');
    const panel = document.getElementById('loginPanel');
    const overlay = document.getElementById('loginPanelOverlay');

    function openPanel() {
        panel.classList.remove('translate-x-full');
        overlay.classList.remove('opacity-0', 'pointer-events-none');
    }

    function closePanel() {
        panel.classList.add('translate-x-full');
        overlay.classList.add('opacity-0', 'pointer-events-none');
    }

    if (openBtn) openBtn.addEventListener('click', openPanel);
    if (closeBtn) closeBtn.addEventListener('click', closePanel);
    if (overlay) overlay.addEventListener('click', closePanel);
});
</script>
<script src="js/jquery-1.12.4.min.js"></script>
<script src="js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script type="module" crossorigin src="assets/script.js"></script>
</body>

</html>