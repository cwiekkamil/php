<ul>
	<li><a href="index.php">Strona g³ówna</a></li>
	<li><a href="index.php?dzial=katalog">Katalog ksi±¿ek</a></li>
	<li><a href="index.php?dzial=wyszukiwanie">Wyszukiwanie</a></li>
	<li><a href="index.php?dzial=koszyk">Koszyk <?
		if(isset($_SESSION['wkoszyku']))
		{
			if($_SESSION['wkoszyku'])
						echo "[".$_SESSION['wkoszyku']."]";
					else 
						echo "";
		}
	?></a></li>
	<li><a href="#">Zaloguj siê</a></li>
</ul>
