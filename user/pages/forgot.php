<?php if(!$indexing) { exit; } ?>



<ul class="breadcrumb">
							<li><i class='fa fa-home'></i><?php echo $LANG[39000]; ?></li>
						</ul>



	<h1><?php echo $LANG[12040]; ?></h1>

	<br /><br />
	
	<form class='usarJquery' method='POST' action='./?engine=recover'>
		
		<?php $_SESSION['key'] = md5(time().rand(100,999).$uniqueKey); echo "<input type='hidden' name='key' value='".$_SESSION['key']."' />"; ?>
		
		<label class='formpadrao'>
			<div>
				<div class='desc'>E-mail:</div>
				<div class='camp'><input type='text' maxlength='100' name='email' autocomplete='off' /></div>
			</div>
		</label>
		
		<label class='formpadrao captcha'>
			<div>
				<div class='desc'><img style='margin:5px 0 0 0;' class='captchaImage' src='captcha/securimage_show.php?sid=<?php echo md5(uniqid()) ?>' /></div>
				<div class='camp'><input type='text' id='captchaInput' maxlength='5' name='captcha' autocomplete='off' /></div>
				<a tabindex='-1' href='#'><img src='captcha/refresh.png' /></a>
			</div>
		</label>
		
		<input style='margin: 20px auto; display: table;' type='submit' class='default big' value='<?php echo $LANG[12034]; ?>' />
		
	</form>

</div>
