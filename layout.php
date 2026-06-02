<?php if(!$indexing) { exit; } ?>
<?php
// CONFIG DO BANCO – AJUSTA AQUI
$dbHost = "127.0.0.1";
$dbName = "l2cyclone";       // nome do seu banco
$dbUser = "root";     // usuário
$dbPass = "l2cyclone26";   // senha

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

// TOP 10 PVP
$sqlPvp = $pdo->query("
    SELECT char_name, pvpkills, pkkills, online
    FROM characters
    WHERE accesslevel = 0
    ORDER BY pvpkills DESC, pkkills DESC
    LIMIT 5
");
$rankPvp = $sqlPvp->fetchAll();

// TOP 10 CLÃS
$sqlClan = $pdo->query("
    SELECT 
        c.clan_id,
        c.clan_name,
        c.clan_level,
        c.reputation_score,
        leader.char_name AS leader_name,
        leader.online AS leader_online,
        COALESCE(cp.boss_points, 0) as boss_points,
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

// TOP 10 PK
$sqlPk = $pdo->query("
    SELECT char_name, pkkills, pvpkills, online
    FROM characters
    WHERE accesslevel = 0
    ORDER BY pkkills DESC, pvpkills DESC
    LIMIT 5
");
$rankPk = $sqlPk->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    
    <?php require('private/seo.php'); ?>
    
    <meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<link rel="shortcut icon" href="assets/img/favicon.ico">

<meta name="keywords" content="<?php echo strtolower($server_name . ', ' . $server_chronicle); ?>, Lineage 2, L2 Interlude, L2 StonePVP, L2 Stone, StonePvP, Stone, Lineage Stone, l2stonepvp, l2stone, servidor privado L2, PvP L2, clan, raid boss, MMORPG, 1000x, Custom, Easy farm" />

<meta name="description" content="<?php echo $SEO['description']; ?>"/>
<meta name="robots" content="index, follow">

<title><?php echo $SEO['title']; ?></title>

<!-- Open Graph (Facebook, Discord, WhatsApp) -->
<meta property="og:title" content="<?php echo $SEO['title']; ?>" />
<meta property="og:description" content="<?php echo $SEO['description']; ?>" />
<meta property="og:site_name" content="<?php echo $server_name; ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://<?php echo $server_url; ?>" />

<meta property="og:image" content="https://<?php echo $server_url; ?>/assets/img/image_src.jpg" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />

<!-- Twitter (opcional mas bom ter) -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $SEO['title']; ?>">
<meta name="twitter:description" content="<?php echo $SEO['description']; ?>">
<meta name="twitter:image" content="https://<?php echo $server_url; ?>/assets/img/image_src.jpg">

<!-- CSS -->
<link rel="stylesheet" href="assets/css/styles.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<!-- JS -->
<script src="js/jquery-1.12.4.min.js"></script>
<script src="assets/js/app.js"></script>

    <style>
    .floating-banner {
        position: fixed;
        bottom: 20px;
        left: 20px;
        width: 100%;
        max-width: 500px;
        height: auto;
        background: #fff;
        border: 2px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        overflow: hidden;
        box-sizing: border-box;
    }

    .floating-banner img {
        width: 100%;
        height: auto;
        display: block;
    }

    .close-btn {
        position: absolute;
        top: 5px;
        right: 10px;
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 16px;
        width: 25px;
        height: 25px;
        line-height: 25px;
        text-align: center;
        cursor: pointer;
        z-index: 10000;
        padding: 0;
    }

    @media (max-width: 560px) {
        .floating-banner {
            left: 50%;
            transform: translateX(-50%);
            bottom: 10px;
            width: 90%;
        }
    }
    </style>
</head>

<body>
<!--<div class="floating-banner" id="banner">
    <button class="close-btn" onclick="document.getElementById('banner').style.display='none'">×</button>
    <img src="/assets/img/ATENCAO.jpg" alt="Banner Promocional">
</div>-->

<div id="preloader">
    <div class="preloader-card">
        <div class="preloader-logo">
            <img src="assets/img/logo.png" alt="L2 Stone PVP" class="logo-img"> 
        </div>

        <div class="preloader-dots">
            <span class="preloader-dot"></span>
            <span class="preloader-dot"></span>
            <span class="preloader-dot"></span>
        </div>
    </div>
</div>

<div class="bg-top"></div>

<header class="header">
    <div class="logo">
        <img src="assets/img/logo.png" alt="L2 Stone PVP" class="logo-img">
    </div>

    <nav class="nav">
        <a href="#hero-section">Home</a>
        <a href="#download-section">Download</a>
        <a href="#" onclick="openRegisterModal()">Register</a>
        <a href="#ranking-section">Ranking</a>
        <a href="#news-section">Server Info</a>
        <a href="https://www.l2cyclone.com.br/donate/" target="_blank"><span style="color:#ffd46b;">DONATE</span></a>
    </nav>

    <button class="menu-toggle" onclick="toggleMobileMenu()">☰</button>

    <nav class="mobile-nav">
        <a href="#hero-section">Home</a>
        <a href="#download-section">Download</a>
        <a href="#" onclick="openRegisterModal()">Register</a>
        <a href="#ranking-section">Ranking</a>
        <a href="#news-section">Server Info</a>
        <a href="https://www.l2cyclone.com.br/donate/"><span style="color:#ffd46b;">DONATE</span></a>
    </nav>

    <div class="right-menu">

<?php if ($logged != 1) { ?>

    <div class="top-login">
        <form 
            id="top-login-form"
            action="<?php echo (file_exists('ucp/engine/login.php') ? './ucp/?engine=login&fromsite' : './?engine=login'); ?>" 
            method="POST"
        >

            <?php
                $_SESSION['lkey'] = md5(time().rand(100,999).$uniqueKey);
            ?>
            <input type="hidden" name="lkey" value="<?php echo $_SESSION['lkey']; ?>">
            <input type="hidden" name="captcha" id="ucp_captcha" value="">
            <input type="hidden" name="ucp_uniqid" value="<?php echo md5(uniqid()); ?>">

            <input type="text" name="ucp_login" placeholder="Login" autocomplete="off" required>
            <input type="password" name="ucp_passw" placeholder="Senha" autocomplete="off" required>

            <?php if ($captcha_cp_on == 1) { ?>
                <button type="button" class="login-btn" onclick="opencaptcha();">
                    LOGIN
                </button>
            <?php } else { ?>
                <button type="submit" class="login-btn">
                    LOGIN
                </button>
            <?php } ?>

        </form>
    </div>

<?php } else { ?>

    <?php if (file_exists('ucp/index.php')) { ?>
        <div class="top-logged">
            <span class="user"><?php echo $_SESSION['acc']; ?></span>
            <a href="#" class="cp-btn" onclick="openPageModal('./ucp'); return false;">DASHBOARD</a>
            <a href="./?engine=logout" class="logout-btn"><?php echo $LANG[12023]; ?></a>
        </div>
    <?php } else { ?>
        <div class="top-logged dropdown-account">
            <span class="user"><?php echo $_SESSION['acc']; ?></span>
            <div class="account-menu">
                <a href="#" onclick="openUcpChangePass(); return false;"><?php echo $LANG[12022]; ?></a>
                <?php if ($chaemail == 1) { ?>
                    <a href="#" onclick="openUcpEmailChange(); return false;"><?php echo $LANG[11014]; ?></a>
                <?php } ?>
                <?php if ($dpage['unstuk'] == 1) { ?>
                    <a href="#" onclick="openUcpUnstuck(); return false;">Unstuck Char</a>
                <?php } ?>
                <a href="./?engine=logout" class="logout"><?php echo $LANG[12023]; ?></a>
            </div>
        </div>
    <?php } ?>

<?php } ?>

    </div>
</header>

<div class="page-nav-float">
    <button class="page-nav-dot active" data-target="#hero-section" title="Hero"></button>
    <button class="page-nav-dot" data-target="#news-section" title="News"></button>
    <button class="page-nav-dot" data-target="#ranking-section" title="Rankings"></button>
    <button class="page-nav-dot" data-target="#download-section" title="Download & Discord"></button>
</div>

<section id="hero-section" class="hero">
    <div class="hero-overlay"></div>

    <div class="hero-inner">
        <div class="hero-left">
            <div class="hero-logo-wrap">
                <img src="assets/img/logo.png" alt="L2 Stone PVP" class="hero-logo">
            </div>
        </div>

        <div class="hero-right hero-text">


<h2>X300 <span>HIGH RATE PVP</span></h2>

<p>
    Prepare for intense battles, fast progression, and nonstop action.
    Create your character and step into a world built for true PvP warriors.
</p>

            <div class="hero-cta">
                <a href="#download-section" class="btn-start">START TO PLAY</a>

                <?php
                if ($counterActived == 1) {
                    $inauguracao = mktime($cHor, $cMin, 0, $cMes, $cDia, $cAno);
                    if (time() < $inauguracao):
                ?>
                <div class="hero-countdown-date">
                    <?php
                        echo $cDia . " " . date('F', $inauguracao) . ", " . $cAno .
                             " • " . sprintf('%02d:%02d', $cHor, $cMin);
                    ?>
                    <span style="font-size:11px;font-weight:bold;font-style:italic;vertical-align:super;">
                        (UTC <?php echo $cGMT; ?>)
                    </span>
                </div>

                <div class="hero-countdown">
                    <div class="hero-countdown-title">
                        NEW X300 SERVER OPENS IN:
                    </div>

                    <div class="hero-countdown-row">
                        <div class="hero-countdown-box">
                            <span id="cd-days" class="hero-countdown-number">00</span>
                            <span class="hero-countdown-label">Days</span>
                        </div>
                        <div class="hero-countdown-box">
                            <span id="cd-hours" class="hero-countdown-number">00</span>
                            <span class="hero-countdown-label">Hours</span>
                        </div>
                        <div class="hero-countdown-box">
                            <span id="cd-minutes" class="hero-countdown-number">00</span>
                            <span class="hero-countdown-label">Minutes</span>
                        </div>
                        <div class="hero-countdown-box">
                            <span id="cd-seconds" class="hero-countdown-number">00</span>
                            <span class="hero-countdown-label">Seconds</span>
                        </div>
                    </div>
                </div>
                <?php
                    endif;
                }
                ?>
            </div>
        </div>
    </div>
</section>

<div class="line d-flex">
    <div class="line__back"></div>
    <div class="line__def"></div>
    <div class="line__center"></div>
    <div class="line__def"></div>
</div>

<section id="news-section" class="news-section">
    <div class="news-header">
        <h2>Server Informations</h2>
        <p>Stay updated with our latest announcements and server events.</p>
    </div>

    <div class="news-grid">
        <article class="news-card">
            <div class="news-card__bg" style="background-image:url('assets/img/news/news-1.jpg');"></div>
            <div class="news-card__overlay"></div>

            <div class="news-card__content">
                <h3 class="news-title">SIEGE DE GIRAN</h3>
                <p class="news-text">
                   🏰 SIEGE DE GIRAN<br>
                   💰 R$ 500 NO PIX pro clã que conquistar o castelo<br>
                   🏆 Troféu exclusivo para o Top Leader<br><br>
                   É guerra, é estratégia, é glória.<br>
                   Só um clã leva tudo. 🔥
                </p>
                <a href="#"
                   class="news-btn"
                   data-title="SIEGE DE GIRAN + 500$ NO PIX + TROFEU PRO LIDER"
                   data-image="assets/img/news/news-1-1.jpg"
                   data-text="🏰 SIEGE DE GIRAN
💰 R$ 500 NO PIX pro clã que conquistar o castelo
🏆 Troféu exclusivo para o Top Leader

É guerra, é estratégia, é glória.
Só um clã leva tudo. 🔥">
                   Detail
                </a>
            </div>
        </article>

        <article class="news-card">
            <div class="news-card__bg" style="background-image:url('assets/img/news/news-2.jpg');"></div>
            <div class="news-card__overlay"></div>

            <div class="news-card__content">
                <h3 class="news-title">INFORMAÇOES GERAIS DO SERVIDOR</h3>
                <p class="news-text">
                    📘 Informaçoes do servidor L2 Cyclone.
                </p>
                <a href="#" class="news-btn" data-title="📘 INFORMAÇÕES DO SERVIDOR" data-image="assets/img/news/news-2.jpg" data-text="🔹 Rate XP / SP / Adena: 1000x
🛡️ Sets: TT, DY, DK
⚔️ Armas: Epic, DY, LC, KDUS
🎯 Nível Inicial: 80
👑 Noblesse: Barakiel ou Farm Coin
🤖 Auto Farm: VIP!
🛠️ Comandos: .menu
🌾 Dificuldade de Farm: Facil
💸 RMT: Permitido
🎟️ Loja VIP: Sistema de Tickets
🏰 Castle Sieges: Semanais
🏆 Olympiad: Ciclo de 7 dias
">Detail</a>
            </div>
        </article>

        <article class="news-card">
            <div class="news-card__bg" style="background-image:url('assets/img/news/news-3.jpg');"></div>
            <div class="news-card__overlay"></div>

            <div class="news-card__content">
                <h3 class="news-title">SISTEMA DE ENCHANT</h3>
                <p class="news-text">
                    📈 Sistema de enchante do L2 Cyclone.
                </p>
                <a href="#" class="news-btn" data-title="📈 SISTEMA DE ENCHANT" data-image="assets/img/news/news-2.jpg" data-text="✅ Safe Enchant: +14
🎯 Max Enchant: +30
📜 Normal Scroll: 300% (+1 a +4)
💥 Falha (Normal): Item cristaliza
📜 Blessed Scroll: 50% (+4a +18)
🔄 Falha (Blessed): Volta para +10
✨ Golden Scroll: 30% (+18a +30)
❌ Falha (Golden): Apenas o scroll é perdido">Detail</a>
            </div>
        </article>

        <article class="news-card">
            <div class="news-card__bg" style="background-image:url('assets/img/news/news-4.jpg');"></div>
            <div class="news-card__overlay"></div>

            <div class="news-card__content">
                <h3 class="news-title">Regras da Olympiad</h3>
                <p class="news-text">
                    Regras Olympiad L2 Cyclone.
                </p>
                <a href="#" class="news-btn" data-title="🏅 Regras da Olympiad" data-image="assets/img/news/news-2.jpg" data-text="🗓️ Ciclo: 7 dias
🕕 Horário: 18:00 - 00:00 (GMT+3)
📊 Min. Partidas: 9
🔢 Min. Pontos: 4
💠 Itens Permitidos: Custom
📏 Enchant Máx. Olympiad: +30">Detail</a>
            </div>
        </article>
    </div>
</section>

<div class="line d-flex">
    <div class="line__back"></div>
    <div class="line__def"></div>
    <div class="line__center"></div>
    <div class="line__def"></div>
</div>

<section id="ranking-section" class="ranking-section">
    <div class="ranking-header">
        <h2>Server Rankings</h2>
        <p>Top players in PvP and PK, updated directly from the database.</p>
    </div>

    <div class="ranking-swiper swiper">
        <div class="swiper-wrapper">

            <div class="swiper-slide">
                <div class="ranking-box">
                    <h3>PvP Ranking</h3>

                    <div class="ranking-table">
                        <div class="ranking-row ranking-row--head">
                            <span>#</span>
                            <span>Nickname</span>
                            <span>PvP</span>
                            <span>PK</span>
                            <span>Status</span>
                        </div>

                        <?php
                        $pos = 1;
                        foreach ($rankPvp as $player):
                            $online = (int)$player['online'] === 1;
                        ?>
                        <div class="ranking-row">
                            <span><?php echo $pos; ?></span>
                            <span><?php echo htmlspecialchars($player['char_name']); ?></span>
                            <span style="color: #ffc107; font-weight: bold;"><?php echo (int)$player['pvpkills']; ?></span>
                            <span><?php echo (int)$player['pkkills']; ?></span>
                            <span class="rk-status <?php echo $online ? 'online' : 'offline'; ?>">
                                <?php echo $online ? 'Online' : 'Offline'; ?>
                            </span>
                        </div>
                        <?php $pos++; endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="ranking-box">
                    <h3>PK Ranking</h3>

                    <div class="ranking-table">
                        <div class="ranking-row ranking-row--head">
                            <span>#</span>
                            <span>Nickname</span>
                            <span>PK</span>
                            <span>PvP</span>
                            <span>Status</span>
                        </div>

                        <?php
                        $pos = 1;
                        foreach ($rankPk as $player):
                            $online = (int)$player['online'] === 1;
                        ?>
                        <div class="ranking-row">
                            <span><?php echo $pos; ?></span>
                            <span><?php echo htmlspecialchars($player['char_name']); ?></span>
                            <span style="color: #ffc107; font-weight: bold;"><?php echo (int)$player['pkkills']; ?></span>
                            <span><?php echo (int)$player['pvpkills']; ?></span>
                            <span class="rk-status <?php echo $online ? 'online' : 'offline'; ?>">
                                <?php echo $online ? 'Online' : 'Offline'; ?>
                            </span>
                        </div>
                        <?php $pos++; endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="ranking-box">
                    <h3>Clan Ranking</h3>

                    <div class="ranking-table">
                        <div class="ranking-row ranking-row--head">
                            <span>#</span>
                            <span>Clan</span>
                            <span>Boss Pts</span>
                            <span>Members</span>
                            <span>Leader</span>
                        </div>

                        <?php
                        $pos = 1;
                        foreach ($rankClan as $clan):
                        ?>
                        <div class="ranking-row">
                            <span><?php echo $pos; ?></span>
                            <span><?php echo htmlspecialchars($clan['clan_name']); ?></span>
                            <span style="color: #ffc107; font-weight: bold;">
                                <?php echo (int)$clan['boss_points']; ?>
                            </span>
                            <span><?php echo (int)$clan['members']; ?></span>

                            <?php
                            $leaderOnline = (int)($clan['leader_online'] ?? 0) === 1;
                            ?>
                            <span class="rk-status <?php echo $leaderOnline ? 'online' : 'offline'; ?>">
                                <?php echo htmlspecialchars($clan['leader_name'] ?? 'N/A'); ?>
                            </span>
                        </div>
                        <?php $pos++; endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>

<div class="line d-flex">
    <div class="line__back"></div>
    <div class="line__def"></div>
    <div class="line__center"></div>
    <div class="line__def"></div>
</div>

<section id="download-section" class="download-discord-section">
    <div class="download-discord-inner">
        <div class="download-discord-header">
            <h2>Download & Community</h2>
            <p>Download the server client and join our official Discord server.</p>
        </div>

        <div class="download-discord-layout">
            <div class="download-col">
                <h3>Download Area</h3>

                <div class="download-wrapper">
                    <a href="" target="_blank" class="download-card">
                        <div class="download-card-icon">
                            <img src="assets/img/icons/mediafire.png" alt="MediaFire">
                        </div>
                        <div class="download-card-title">Patch L2 Cyclone</div>
                        <div class="download-card-sub">Fast &amp; Stable</div>
                    </a>

                    <a href="https://www.mediafire.com/file/sraamzmuwckpg7a/lin2_746w10_interlude_24122022.rar/file" target="_blank" class="download-card">
                        <div class="download-card-icon">
                            <img src="assets/img/icons/mediafire.png" alt="MediaFire">
                        </div>
                        <div class="download-card-title">Cliente Limpo</div>
                        <div class="download-card-sub">Fast &amp; Stable</div>
                    </a>
                </div>
            </div>

            <div class="discord-col">
                <div class="discord-widget-box">
                    <iframe
                        src="https://discord.com/widget?id=1474211437311299588&theme=dark"
                        allowtransparency="true"
                        frameborder="0"
                        sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="bg-bottom"></div>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-column">
            <h4>About</h4>
            <p>L2 Cyclone is a private Lineage II.gameserver bringing players the best PvP experience with stability and quality.</p>
        </div>

        <div class="footer-column">
            <h4>Quick Links</h4>
            <a href="#download-section">Download</a>
            <a href="#" onclick="openRegisterModal()">Register</a>
            <a href="#ranking-section">Ranking</a>
            <a href="#news-section">Server Info</a>
        </div>

        <div class="footer-column">
            <h4>Contact</h4>
            <p>Email: l2cyclone.contato@gmail.com</p>
            <p><a href="https://discord.gg/rRwBTVcaFM" target="_blank">Discord: L2Cyclone</a></p>
            <p>© 2026 L2 CYCLONE</p>
        </div>
    </div>

    <div class="footer-bottom">
        © 2026 L2 CYCLONE — All Rights Reserved. design by <a href="https://wa.me/5528999446993" target="_blank">AMX PROJECTS</a>
    </div>
</footer>

    <!-- SCRIPT PARA PAGINAÇÃO FLUTUANTE (SCROLL SUAVE + ACTIVE) -->
    <script>
        (function () {
            const dots = document.querySelectorAll('.page-nav-dot');
            const sections = Array.from(dots).map(dot => {
                const targetSel = dot.dataset.target;
                return {
                    dot,
                    target: document.querySelector(targetSel)
                };
            });

            function scrollToTarget(target) {
                const headerOffset = 120; // compensa o header fixo
                const top = target.getBoundingClientRect().top + window.scrollY - headerOffset;
                window.scrollTo({ top, behavior: 'smooth' });
            }

            dots.forEach(dot => {
                dot.addEventListener('click', e => {
                    e.preventDefault();
                    const target = document.querySelector(dot.dataset.target);
                    if (target) {
                        scrollToTarget(target);
                    }
                });
            });

            function updateActiveByScroll() {
                const scrollPos = window.scrollY + 200;
                let current = null;

                sections.forEach(s => {
                    if (!s.target) return;
                    const offsetTop = s.target.offsetTop;
                    if (offsetTop <= scrollPos) {
                        current = s;
                    }
                });

                if (current) {
                    dots.forEach(d => d.classList.remove('active'));
                    current.dot.classList.add('active');
                }
            }

            window.addEventListener('scroll', updateActiveByScroll);
            window.addEventListener('load', updateActiveByScroll);
        })();
    </script>

<script>
(function () {
    var cdDays    = document.getElementById('cd-days');
    var cdHours   = document.getElementById('cd-hours');
    var cdMinutes = document.getElementById('cd-minutes');
    var cdSeconds = document.getElementById('cd-seconds');

    if (!cdDays) return; // se não tiver countdown, sai

    // Data alvo vinda do PHP
    var targetTime = new Date(
        <?php echo (int)$cAno; ?>,
        <?php echo (int)$cMes - 1; ?>,
        <?php echo (int)$cDia; ?>,
        <?php echo (int)$cHor + (int)$sumH; ?>,
        <?php echo (int)$cMin; ?>,
        0
    ).getTime();

    function pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    function updateCountdown() {
        var now   = Date.now();
        var diff  = targetTime - now;

        if (diff <= 0) {
            cdDays.textContent    = '00';
            cdHours.textContent   = '00';
            cdMinutes.textContent = '00';
            cdSeconds.textContent = '00';
            return;
        }

        var seconds = Math.floor(diff / 1000);
        var days    = Math.floor(seconds / 86400);
        seconds    -= days * 86400;
        var hours   = Math.floor(seconds / 3600);
        seconds    -= hours * 3600;
        var minutes = Math.floor(seconds / 60);
        seconds    -= minutes * 60;

        cdDays.textContent    = pad(days);
        cdHours.textContent   = pad(hours);
        cdMinutes.textContent = pad(minutes);
        cdSeconds.textContent = pad(seconds);
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
})();
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // CONFIG DO PRELOADER
    const preloadConfig = {
        minTime: 2.5,      // mínimo 2.5s
        maxTime: 6,        // máximo 6s
        withOnload: true,
        timeInterval: 0.3,
    };

    const preloader = document.getElementById("preloader");
    if (!preloader) return;

    let startTime = Date.now();
    let finished = false;

    function hidePreloader() {
    if (finished) return;
    finished = true;

    preloader.classList.add("preloader-hide");

    setTimeout(() => {
        if (preloader && preloader.parentNode) {
            preloader.parentNode.removeChild(preloader);
        }

        // ✅ LIBERA STATUS SERVER + VOTE
        document.body.classList.add('after-preload');

        // ✅ ABRE O DISCORD APÓS O PRELOAD SUMIR
        setTimeout(() => {
            openDiscord();
        }, 400);

    }, 600);
}

    // FUNÇÃO DE CHECK AUTOMÁTICO
    const checkInterval = setInterval(() => {
        let elapsed = (Date.now() - startTime) / 1000;

        // Se passou do máximo → esconde
        if (elapsed >= preloadConfig.maxTime) {
            clearInterval(checkInterval);
            hidePreloader();
        }

        // Se load já ocorreu e minTime passou → esconde
        if (preloadConfig.withOnload && window.pageLoaded) {
            if (elapsed >= preloadConfig.minTime) {
                clearInterval(checkInterval);
                hidePreloader();
            }
        }

    }, preloadConfig.timeInterval * 1000);

    // EVENTO ONLOAD
    window.addEventListener("load", () => {
        window.pageLoaded = true;

        let elapsed = (Date.now() - startTime) / 1000;
        if (elapsed >= preloadConfig.minTime) {
            hidePreloader();
        }
        // senão, deixa o intervalo terminar sozinho
    });

});
</script>
<!-- DISCORD POPUP -->
<div id="discordPopup" class="discord-overlay">
  <div class="discord-box">
    <button class="discord-close" onclick="closeDiscord()">×</button>

    <h2>Entre no nosso Discord</h2>

    <iframe
      src="https://discord.com/widget?id=1474211437311299588&theme=dark"
      width="100%"
      height="350"
      allowtransparency="true"
      frameborder="0"
      sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts">
    </iframe>

    <div class="discord-actions">
      <a href="https://discord.gg/rRwBTVcaFM" target="_blank" class="discord-btn discord-join">
        Entrar no Discord
      </a>
      <button class="discord-btn discord-later" onclick="closeDiscord()">
        Talvez depois
      </button>
    </div>
  </div>
</div>
<script>
function openDiscord() {
    document.getElementById('discordPopup').style.display = 'flex';
}

function closeDiscord() {
    document.getElementById('discordPopup').style.display = 'none';
}


</script>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/* GERA A KEY DO CORE (OBRIGATÓRIA) */
$_SESSION['key'] = md5(time() . rand(1000,9999));
?>

<div id="ucpEmailChangeModal" class="register-overlay">
    <div class="register-box">
        <button class="register-close" onclick="closeUcpEmailChange()">×</button>

        <?php 
        // Em vez de exit, usamos um IF para envolver o conteúdo
        if($indexing && $logged == 1 && $chaemail == 1) { 
            require_once('private/classes/classAccount.php');
            $acc = Account::checkLoginExists($_SESSION['acc']);
        ?>
            <h2><?php echo $LANG[11014]; ?></h2>
            <p><?php echo $LANG[12985]; ?></p>

            <form class="usarJquery" method="POST" action="./?engine=ucp_emailchange">
                <div class="field field_mb">
                    <div class="field__name">Login</div>
                    <div class="field__box">
                        <input type="text" disabled class="field__input" value="<?php echo $acc[0]['login']; ?>" />
                    </div>
                </div>
                <div class="field field_mb">
                    <div class="field__name"><?php echo $LANG[12986]; ?></div>
                    <div class="field__box">
                        <input type="text" disabled class="field__input" value="<?php echo $acc[0]['email']; ?>" />
                    </div>
                </div>
                <div class="field field_mb">
                    <div class="field__name"><?php echo $LANG[12988]; ?></div>
                    <div class="field__box">
                        <input type="text" name="newemail" class="field__input" required />
                    </div>
                </div>
                <div class="field field_mb">
                    <div class="field__name"><?php echo $LANG[12987]; ?></div>
                    <div class="field__box">
                        <input type="text" name="newemail2" class="field__input" required />
                    </div>
                </div>
                <button type="submit" class="register-btn"><?php echo $LANG[11014]; ?></button>
            </form>
        <?php } else { ?>
            <p style="margin-top:20px;">Área restrita a jogadores logados.</p>
        <?php } ?>
    </div>
</div>

<div id="ucpUnstuckModal" class="register-overlay">
    <div class="register-box">
        <button class="register-close" onclick="closeUcpUnstuck()">×</button>

        <?php if($indexing && $logged == 1 && $dpage['unstuk'] == 1) { ?>
            <h2>Unstuck Char</h2>
            <p><?php echo $LANG[12095]; ?></p>
            <ul class="unstuck-info"><li><?php echo $LANG[12096]; ?></li></ul>

            <form class="usarJquery" method="POST" action="./?engine=ucp_unstuck_move">
                <div class="unstuck-title"><b><?php echo $LANG[12101]; ?>:</b></div>
                <div class="field field_mb">
                    <div class="field__name"><?php echo $LANG[29007]; ?></div>
                    <div class="field__box">
                        <select name="cid" class="field__input">
                            <?php
                            require_once 'private/classes/classAccount.php';
                            $chars = Account::listChars($_SESSION['acc']);
                            if(count($chars) > 0) {
                                foreach($chars as $char) {
                                    echo "<option value='{$char['obj_Id']}'>{$char['char_name']}</option>";
                                }
                            } else {
                                echo "<option value='0'>{$LANG[12100]}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="register-btn">Unstuck</button>
            </form>
        <?php } else { ?>
            <p style="margin-top:20px;">Você precisa estar logado para usar o Unstuck.</p>
        <?php } ?>
    </div>
</div>

<div id="ucpChangePassModal" class="register-overlay">
    <div class="register-box">
        <button class="register-close" onclick="closeUcpChangePass()">×</button>

        <?php if($indexing && $logged == 1) { 
            require_once 'private/classes/classAccount.php';
            $acc = Account::checkLoginExists($_SESSION['acc']);
        ?>
            <h2><?php echo $LANG[12022]; ?></h2>
            <p><?php echo $LANG[12090]; ?></p>

            <form class="usarJquery" method="POST" action="./?engine=ucp_updatepass">
                <div class="field field_mb">
                    <div class="field__name">Login</div>
                    <div class="field__box">
                        <input type="text" disabled value="<?php echo htmlspecialchars($acc[0]['login']); ?>" class="field__input">
                    </div>
                </div>
                <div class="field field_mb">
                    <div class="field__name">E-mail</div>
                    <div class="field__box">
                        <input type="text" disabled value="<?php echo htmlspecialchars($acc[0]['email']); ?>" class="field__input">
                    </div>
                </div>
                <div class="field field_mb">
                    <div class="field__name"><?php echo $LANG[12037]; ?></div>
                    <div class="field__box">
                        <input type="password" name="oldpass" autocomplete="off" class="field__input" required>
                    </div>
                </div>
                <div class="field field_mb">
                    <div class="field__name"><?php echo $LANG[12047]; ?></div>
                    <div class="field__box">
                        <input type="password" name="newpass" maxlength="25" autocomplete="off" class="field__input" required>
                    </div>
                </div>
                <div class="field field_mb">
                    <div class="field__name"><?php echo $LANG[12048]; ?></div>
                    <div class="field__box">
                        <input type="password" name="newpass2" maxlength="25" autocomplete="off" class="field__input" required>
                    </div>
                </div>
                <button type="submit" class="register-btn"><?php echo $LANG[12022]; ?></button>
            </form>
        <?php } else { ?>
            <p style="margin-top:20px;">Acesse sua conta para alterar a senha.</p>
        <?php } ?>
    </div>
</div>

<div id="registerModal" class="register-overlay">
    <div class="register-box">
        <button class="register-close" onclick="closeRegisterModal()">×</button>
        <h2>Create Account</h2>
        <p>Register now and start your journey on L2 CYCLONE.</p>
        <form class="usarJquery registerForm" method="POST" action="./?engine=create_account">
            <input type="hidden" name="key" value="<?= $_SESSION['key']; ?>">
            <input type="hidden" name="isJS" value="1">
            <input type="hidden" name="nosuffix" value="1">
            <div class="field field_mb">
                <div class="field__name">Login</div>
                <div class="field__box">
                    <input type="text" name="login" maxlength="14" placeholder="Digite seu nome" class="field__input" required>
                </div>
            </div>
            <div class="field field_mb">
                <div class="field__name">Senha</div>
                <div class="field__box">
                    <input type="password" name="pass" maxlength="25" placeholder="Digite a senha" class="field__input" required>
                </div>
            </div>
            <div class="field field_mb">
                <div class="field__name">Confirme sua senha</div>
                <div class="field__box">
                    <input type="password" name="pass2" maxlength="25" placeholder="Digite a senha novamente" class="field__input" required>
                </div>
            </div>
            <div class="field field_mb">
                <div class="field__name">E-mail</div>
                <div class="field__box">
                    <input type="text" name="email" maxlength="100" placeholder="Digite o e-mail" class="field__input" required>
                </div>
            </div>
            <div class="field field_mb">
                <div class="field__name">Repita o E-mail</div>
                <div class="field__box">
                    <input type="text" name="email2" maxlength="100" placeholder="Digite o e-mail novamente" class="field__input" required>
                </div>
            </div>
            <div class="field field_mb captcha">
                <div class="field__box">
                    <img class="captchaImage" src="captcha/securimage_show.php?sid=<?= md5(uniqid()); ?>" alt="captcha" style="width:100%;height:34px;">
                    <input type="text" name="captcha" maxlength="5" placeholder="Digite o captcha" class="field__input" required>
                </div>
            </div>
            <div class="form__btns">
                <button type="submit" class="register-btn">Registrar</button>
                <div class="checkbox mt">
                    <label class="checkbox__label">
                        <input type="checkbox" name="acceptrules" value="1" class="checkbox__input" checked required>
                        <span class="checkbox__block"></span>
                    </label>
                    <div class="checkbox__content">
                        Eu concordo com a <a href="./?page=privacy" target="_blank">Política de Privacidade</a>.
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="news-modal" id="newsModal">
    <div class="news-modal__overlay"></div>
    <div class="news-modal__box">
        <button class="news-modal__close">&times;</button>
        <div class="news-modal__image"></div>
        <div class="news-modal__content">
            <span class="news-modal__date"></span>
            <h2 class="news-modal__title"></h2>
            <p class="news-modal__text"></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
function toggleMobileMenu() {
    document.querySelector('.header').classList.toggle('mobile-open');
}
</script>

<script>
const modal = document.getElementById('newsModal');
const modalImg = modal.querySelector('.news-modal__image');
const modalTitle = modal.querySelector('.news-modal__title');
const modalText = modal.querySelector('.news-modal__text');
const modalDate = modal.querySelector('.news-modal__date');
const closeBtn = modal.querySelector('.news-modal__close');
const overlay = modal.querySelector('.news-modal__overlay');

document.querySelectorAll('.news-btn').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();

        modalImg.style.backgroundImage = `url('${btn.dataset.image}')`;
        modalTitle.textContent = btn.dataset.title;
        modalText.innerHTML = btn.dataset.text.replace(/\n/g, "<br>");
        modalDate.textContent = btn.dataset.date;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
});

function closeModal() {
    modal.classList.remove('active');
    document.body.style.overflow = '';
}



closeBtn.addEventListener('click', closeModal);
overlay.addEventListener('click', closeModal);
</script>

<script>

    const rankingSwiper = new Swiper('.ranking-swiper', {
        slidesPerView: 2,
        spaceBetween: 30,
        
        // 🔥 ESSENCIAIS PARA RESOLVER O PROBLEMA DE SUMIÇO:
        observer: true,
        observeParents: true,
        watchOverflow: true,

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },

        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
        }
    });

</script>
<script>
$(document).on('submit', '.registerForm', function (e) {
    e.preventDefault();

    $.ajax({
        url: './?engine=create_account',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (r) {

            if (r.act === 'OK' && r.url) {

                // ✅ FECHA O MODAL
                closeRegisterModal();

                // ⏳ pequeno delay pra não bloquear o download
                setTimeout(function () {
                    window.location.href = r.url;
                }, 300);

            } else {
                alert(r.msg || 'Erro ao criar conta');
            }

        },
        error: function () {
            alert('Erro de comunicação com o servidor.');
        }
    });

    return false;
});
</script>
<!-- GLOBAL PAGE MODAL -->
<div id="pageModal" class="page-modal">
    <div class="page-modal-overlay" onclick="closePageModal()"></div>

    <div class="page-modal-box">
        <button class="page-modal-close" onclick="closePageModal()">×</button>

        <iframe
            id="pageModalFrame"
            src=""
            frameborder="0"
            allowfullscreen>
        </iframe>
    </div>
</div>
<script>
function openUcpChangePass() {
    document.getElementById('ucpChangePassModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeUcpChangePass() {
    document.getElementById('ucpChangePassModal').style.display = 'none';
    document.body.style.overflow = '';
}
</script>
<script>
function openUcpUnstuck() {
    document.getElementById('ucpUnstuckModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeUcpUnstuck() {
    document.getElementById('ucpUnstuckModal').style.display = 'none';
    document.body.style.overflow = '';
}
</script>
<script>
function openUcpEmailChange() {
    document.getElementById('ucpEmailChangeModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeUcpEmailChange() {
    document.getElementById('ucpEmailChangeModal').style.display = 'none';
    document.body.style.overflow = '';
}
</script>
<!--<a id="top-l2jbrasil-float"
   href=""
   target="_blank"
   rel="noopener">
    <img src=""
         alt="Top L2JBrasil de Servidores Lineage 2">
</a>-->
<div id="server-status-float">

<?php

/* ========================= */
/* LOGIN SERVER STATUS       */
/* ========================= */

if ($forceLoginStatus == 'on') {
    $loginStatus = 'on';
} elseif ($forceLoginStatus == 'off') {
    $loginStatus = 'off';
} else {
    $check_login = @fsockopen($serverIp, $loginPort, $errno, $errstr, 1);
    $loginStatus = $check_login ? 'on' : 'off';
}

/* ========================= */
/* GAME SERVER STATUS        */
/* ========================= */

if ($forceGameStatus == 'on') {
    $gameStatus = 'on';
} elseif ($forceGameStatus == 'off') {
    $gameStatus = 'off';
} else {
    $check_game = @fsockopen($serverIp, $gamePort, $errno, $errstr, 1);
    $gameStatus = $check_game ? 'on' : 'off';
}

/* ========================= */
/* PLAYERS ONLINE            */
/* ========================= */

$playersOnline = 0;

if ($showPlayersOn == '1' && $gameStatus == 'on') {

    $cacheFile = "cache/playerson.xml";
    $delay = 1;

    if (!file_exists($cacheFile)) {
        require "private/playerson.php";
    }

    $xml = simplexml_load_file($cacheFile);

    if ($xml) {
        $updated = intval($xml->configs->updated);

        if (($updated + ($delay * 60)) < time()) {
            require "private/playerson.php";
            $xml = simplexml_load_file($cacheFile);
        }

        $playersOnline = intval($xml->players->online);
    }
}

/* ========================= */
/* GARANTIA FINAL            */
/* ========================= */

if ($gameStatus !== 'on') {
    $playersOnline = 0;
}

?>

    <div class="server-status-box">
        <div class="server-status-title">Server Status</div>

        <div class="server-status-row">
            <span>Login</span>
            <span class="status <?php echo $loginStatus; ?>">
                <?php echo ($loginStatus == 'on' ? 'Online' : 'Offline'); ?>
            </span>
        </div>

        <div class="server-status-row">
            <span>Game</span>
            <span class="status <?php echo $gameStatus; ?>">
                <?php echo ($gameStatus == 'on' ? 'Online' : 'Offline'); ?>
            </span>
        </div>

        <div class="server-status-players">
            <?php echo intval($playersOnline * $fakePlayers); ?> Players Online
        </div>
    </div>

</div>

</body>
</html>
