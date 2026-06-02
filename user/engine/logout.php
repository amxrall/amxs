<?php



if(!$indexing) { exit; }



require_once('private/classes/classAccess.php');

Access::logout();


header("Location: l2system.net");

?>

