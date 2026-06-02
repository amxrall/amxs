<?php

class Access {

	public static function login($login, $password) {
		
		$login = trim($login);
		$password = trim($password);

		$pass1 = base64_encode(pack('H*', sha1($password)));
		$pass2 = base64_encode(hash('whirlpool', $password, true));

		$sql = DB::Executa("
			SELECT login
			FROM accounts
			WHERE login = '".addslashes($login)."'
			AND (
				password = '".addslashes($pass1)."'
				OR password = '".addslashes($pass2)."'
			)
			LIMIT 1
		");

		if (!$sql || count($sql) <= 0) {
			return false;
		}

		return true;
	}
	
	public static function logout() {
		
		$_SESSION['acc'] = '';
		$_SESSION['ses'] = '';
		
		unset($_SESSION['acc']);
		unset($_SESSION['ses']);
		
		header('Location: ./');
		exit;
	}

	public static function isLogged($uniqueKey) {
		
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		if (empty($_SESSION['acc']) || empty($_SESSION['ses'])) {
			return false;
		}

		$validSession = md5($_SERVER['HTTP_USER_AGENT'] . $uniqueKey . 'logged');

		if ($_SESSION['ses'] !== $validSession) {
			return false;
		}

		return true;
	}
	
	public static function registerAccess($login) {
		
		$login = trim($login);

		$sql = DB::Executa("
			INSERT INTO site_ucp_lastlogins (login, ip, logdate)
			VALUES (
				'".addslashes($login)."',
				'".addslashes($_SERVER['REMOTE_ADDR'])."',
				'".time()."'
			)
		");
		if (!$sql) {
			return false;
		}
		
		$sql = DB::Executa("
			SELECT *
			FROM site_ucp_lastlogins
			WHERE login = '".addslashes($login)."'
			ORDER BY logdate DESC
			LIMIT 5
		");

		if ($sql && count($sql) > 0) {
			$DATEs = '';
			for ($i = 0, $c = count($sql); $i < $c; $i++) {
				$DATEs .= $sql[$i]['logdate'] . ',';
			}
			$DATEs = substr($DATEs, 0, -1);

			$del = DB::Executa("
				DELETE FROM site_ucp_lastlogins
				WHERE login = '".addslashes($login)."'
				AND logdate NOT IN (".$DATEs.")
				LIMIT 10
			");

			if (!$del) {
				return false;
			}
		}
		
		return true;
	}
}