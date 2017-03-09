<?

session_start();
ob_start();

// ustalamy parametry logowania do bazy danych
define('dbLogin', 'root');
define('dbPassword', '');
define('dbHost', 'localhost');
define('dbName', 'ibd');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Internetowe Bazy Danych 2008</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
		<link href="css/styl.css" type="text/css" rel="stylesheet" />
	</head>
	<body>
		<div id="container">
			<div id="header">Księgarnia internetowa</div>
			<div id="menu">
				<? include('menu.php'); ?>
			</div>
			<div id="body">
				<?
					if(isset($_GET['dzial']))
						$dzial = $_GET['dzial'];
					else 
						$dzial = '';
						
					switch ($dzial) {
						case 'katalog':
							$nazwaPliku = 'katalog.php';
							break;
						case 'wyszukiwanie':
							$nazwaPliku = 'wyszukiwanie.php';
							break;
						case 'koszyk':
							$nazwaPliku = 'koszyk.php';
							break;
						default:
							$nazwaPliku = 'default.php';
					}
					
					include($nazwaPliku);
				?>
			</div>
			<div id="footer">
				Internetowe Bazy Danych 2008
			</div>
		</div>
	</body>
</html>