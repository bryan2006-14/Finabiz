<?php
session_start();
session_destroy();
header("Location: ../index.php");
exit; // 👈 siempre es buena práctica poner exit después de un header
?>
