<?php if(!$indexing) { exit; } ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($language); ?>">
<head>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '2554996941557329');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=2554996941557329&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="imagetoolbar" content="no">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="shortcut icon" href="https://cdn.ncwest.com/lineage2/01312024-4E97B564EEF42656/images/global/favicon.png">
<title><?php echo $server_name; ?></title>

<!-- ✅ MANTÉM SÓ ÍCONES -->
<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css" media="all" />

<!-- ✅ CSS NOVO COMPLETO -->
<link rel="stylesheet" type="text/css" href="css/ucp-modern.css?v=2" media="all" />

<script type="text/javascript" src="js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="js/global.js?1"></script>
</head>

<body class="<?php echo ($logged != 1 ? "is-guest" : "is-auth"); ?>">

<div class='server_status'></div>

<section class="<?php echo $language; ?>">

<?php
// mantém suas exceções sem login (se quiser)
$pagesExceptions = array('register', 'forgot', 'forgot_confirm');
?>

<?php if($logged != 1 && !($m == '' && in_array($p, $pagesExceptions))) { ?>

	<!-- ===================== -->
	<!-- LOGIN PRIMEIRO (COM CAPTCHA) -->
	<!-- ===================== -->
	<div class="auth-shell">
		<div class="auth-card">
			<div class="auth-brand">
				<div class="brand-dot"></div>
				<div class="brand-text">
					<div class="brand-name"><?php echo $server_name; ?></div>
					<div class="brand-sub"><?php echo $LANG[39007]; ?></div>
				</div>
			</div>

			<form class="auth-form usarJquery" action="./?engine=login" method="POST">
				<?php
				$_SESSION['lkey'] = md5(time().rand(100,999).$uniqueKey);
				echo "<input type='hidden' name='lkey' value='".$_SESSION['lkey']."' />";
				?>

				<label class="field">
					<div class="label">Login</div>
					<input type="text" name="ucp_login" autocomplete="off" value="" />
				</label>

				<label class="field">
					<div class="label"><?php echo $LANG[12049]; ?></div>
					<input type="password" name="ucp_passw" autocomplete="off" value="" />
				</label>

				<?php if($funct['forgot'] == 1 || !empty($link_forgot)) { ?>
					<div class="note">
						<?php echo $LANG[12020]; ?>
						<a href="<?php echo ($funct['forgot'] == 1 ? "./?page=forgot" : $link_forgot); ?>"><?php echo $LANG[12034]; ?></a>
					</div>
				<?php } ?>

				<?php if($captcha_cp_on == 1) { ?>
					<!-- ✅ CAPTCHA MANTIDO -->
					<div class="captcha">
						<img class="captcha-img" src="captcha/securimage_show.php?sid=<?php echo md5(uniqid()) ?>" alt="captcha" />
						<div class="captcha-row">
							<input type="text" id="captchaInput" maxlength="5" name="captcha" autocomplete="off" placeholder="Digite o código" />
							<a class="captcha-refresh" tabindex="-1" href="#"><img src="captcha/refresh.png" alt="refresh" /></a>
						</div>
					</div>
				<?php } ?>

				<button type="submit" class="btn btn-primary">Login</button>
			</form>

			<div class="auth-footer">
				<?php if($funct['regist'] == 1 || !empty($link_regist)) { ?>
					<div class="note">
						<?php echo $LANG[12019]; ?>
						<a href="<?php echo ($funct['regist'] == 1 ? "./?page=register" : $link_regist); ?>"><?php echo $LANG[12077]; ?></a>
					</div>
				<?php } ?>

				<div class="langbar">
					<span>Idioma:</span>
					<a href='?changelang=en' onclick="document.location.replace('./index.php?changelang=en<?php echo $addp; ?>');return false;">EN</a>
					<a href='?changelang=pt' onclick="document.location.replace('./index.php?changelang=pt<?php echo $addp; ?>');return false;">PT</a>
					<a href='?changelang=es' onclick="document.location.replace('./index.php?changelang=es<?php echo $addp; ?>');return false;">ES</a>
				</div>
			</div>
		</div>
	</div>

<?php } else { ?>

	<!-- ===================== -->
	<!-- PAINEL (APÓS LOGAR) -->
	<!-- ===================== -->
	<header class="topbar">
		<div class="container">
			<a href="./" class="topbar-brand">
				<div class="brand-dot"></div>
				<div class="topbar-title"><?php echo $server_name; ?></div>
			</a>

			<div class="topbar-actions">
				<div class="topbar-langs">
					<a href='?changelang=en' class='en' title='English' onclick="document.location.replace('./index.php?changelang=en<?php echo $addp; ?>');return false;"><span></span></a>
					<a href='?changelang=pt' class='pt' title='Portugu&ecirc;s' onclick="document.location.replace('./index.php?changelang=pt<?php echo $addp; ?>');return false;"><span></span></a>
					<a href='?changelang=es' class='es' title='Espa&ntilde;ol' onclick="document.location.replace('./index.php?changelang=es<?php echo $addp; ?>');return false;"><span></span></a>
				</div>

				<div class="topbar-icons">
					<a href="./?module=configs&page=changedata" title="Configurações" class="iconbtn"><i class="fa fa-cog"></i></a>
					<a href="./?engine=logout" title="Sair" class="iconbtn"><i class="fa fa-sign-out"></i></a>
				</div>
			</div>
		</div>
	</header>

	<div class="container">
		<div class="layout">

			<aside class="sidebar">
				<div class="hero-box"></div>

				<nav class="navcard">
					<a href="./"<?php echo ($p == 'index' ? " class='active'" : ""); ?>>
						<span class="nav-ico"><i class="fa fa-home"></i></span>
						<span>Home</span>
					</a>

					<a href="./?module=stats&page=toppvp">
						<span class="nav-ico"><i class="fa fa-bar-chart"></i></span>
						<span>Rankings</span>
					</a>

					<?php
					if($funct['config'] == 1) {
						echo "
						<a href='./?module=configs&page=changedata'".($m == 'configs/' ? " class='active'" : "").">
							<span class='nav-ico'><i class='fa fa-cog'></i></span>
							<span>".$LANG[39012]."</span>
						</a>
						";
					}
					?>

					<a href="./?engine=logout">
						<span class="nav-ico"><i class="fa fa-lock"></i></span>
						<span><?php echo $LANG[12023]; ?></span>
					</a>

					<a href="https://l2mace.club">
						<span class="nav-ico"><i class="fa fa-arrow-circle-left"></i></span>
						<span>WebSite Official</span>
					</a>
				</nav>
			</aside>

			<main class="main">
				<section class="card usercard">
					<div class="userrow">
						<div class="avatar">
							<img src="<?php echo "imgs/avatar/$avatar[$numero].jpg"; ?>" alt="avatar" />
						</div>

						<div class="usertext">
							<div class="username"><?php echo $_SESSION['acc']; ?></div>
							<div class="usertime"><?php echo date("d/M/Y H:i:s"); ?></div>
						</div>

						<a class="chip" href="./?module=configs&page=changedata" title="Configurações">
							<i class="fa fa-key"></i> <?php echo $LANG[999999]; ?>
						</a>
					</div>

					<div class="balance">
						<div class="bal-title">Saldo</div>
						<div class="bal-value">
							<?php
							if(empty($port)) {
								$saldo = DB::Executa("SELECT saldo FROM site_balance WHERE account = '".$_SESSION['acc']."' LIMIT 1");
							} else {
								$saldo = DB::Executa("SELECT TOP 1 saldo FROM site_balance WHERE account = '".$_SESSION['acc']."'", "SITE");
							}
							if(count($saldo) == 0) { $saldo[0]['saldo'] = 0; }
							echo intval(trim($saldo[0]['saldo']))." ".$coinName_mini."'s";
							?>
						</div>

						<div class="actions">
							<a class="btn" href="./?module=donate&page=add"><i class="fa fa-plus-circle"></i> <?php echo $LANG[39010]; ?> <b><?php echo $coinName_mini; ?></b></a>
							<a class="btn" href="./?module=donate&page=transfer"><i class="fa fa-exchange"></i> <?php echo $LANG[10013]; ?> <b><?php echo $coinName_mini; ?></b></a>
							<a class="btn" href="./?module=donate&page=orders"><i class="fa fa-calculator"></i> <?php echo $LANG[39011]; ?></a>
							<a class="btn" href="./?module=donate&page=transfers_list"><i class="fa fa-random"></i> <?php echo $LANG[40037]; ?></a>
						</div>
					</div>
				</section>

				<section class="card content">
					<article>
						<?php
						// mantém suas exceções / include igual
						if(!$logged && $m == '' && in_array($p, $pagesExceptions)) {
							require('./pages/'.$p.'.php');
						} else {
							require('./pages/'.$p.'.php');
						}
						?>
					</article>
				</section>
			</main>

		</div>
	</div>

<?php } ?>

</section>

<footer class="footer">
	<div class="container">
		&copy; <?php echo date('Y'); ?> <?php echo $server_name; ?> - All rights reserved
	</div>
</footer>

<?php
if(!empty($_SESSION['aAlert_msg'])) {
	echo "<script>atualAlert('".$_SESSION['aAlert_msg']."', '".$_SESSION['aAlert_act']."', '".(isset($_SESSION['aAlert_url']) ? $_SESSION['aAlert_url'] : '')."');</script>";
	$_SESSION['aAlert_msg'] = ''; $_SESSION['aAlert_act'] = ''; $_SESSION['aAlert_url'] = '';
	unset($_SESSION['aAlert_msg']); unset($_SESSION['aAlert_act']); unset($_SESSION['aAlert_url']);
}
?>

<!-- Important Terms to JS Scripts -->
<input type='hidden' id='l11015' value='<?php echo ($LANG[11015]); ?>' />
<input type='hidden' id='l11016' value='<?php echo ($LANG[11016]); ?>' />
<input type='hidden' id='l20001' value='<?php echo ($LANG[20001]); ?>' />
<input type='hidden' id='l12004' value='<?php echo ($LANG[12004]); ?>' />
<input type='hidden' id='l40044' value='<?php echo ($LANG[40044]); ?>' />

</body>
</html>
