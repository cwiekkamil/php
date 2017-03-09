<?

function wyswietlRodzajeKsiazek() {
	$link = mysql_connect(dbHost, dbLogin, dbPassword) or die("Nie uda³o siê po³±czyæ z baz± danych.");
	mysql_select_db(dbName, $link) or die("Nie uda³o siê wybraæ bazy danych.");
	mysql_query("SET NAMES latin2");
	
	$query = "SELECT * FROM rodzaje_ksiazek";
	$result = mysql_query($query);
	
	while($row = mysql_fetch_array($result))
		echo "<option value='$row[id]'>$row[nazwa]</option>";
}

?>

<form method="get" action="index.php">

<input type="hidden" name="dzial" value="katalog"/>

<h1>Wyszukiwanie</h1>

<table>
	<tr>
		<td>Tytu³</td>
		<td><input type="text" name="tytul" /></td>
	</tr>
	<tr>
		<td>Autor</td>
		<td><input type="text" name="autor" /></td>
	</tr>
	<tr>
		<td>Rodzaj</td>
		<td>
			<select name="rodzaj_ksiazki">
				<option value="">-</option>
				<? echo wyswietlRodzajeKsiazek(); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Cena od</td>
		<td><input type="text" name="cena_od" /></td>
	</tr>
	<tr>
		<td>Cena do</td>
		<td><input type="text" name="cena_do" /></td>
	</tr>
	<tr>
		<td>Sortuj po</td>
		<td>
			<select name="sortuj_po">
				<option value="autor">autorze</option>
				<option value="tytul">tytule</option>
				<option value="cena">cenie</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Kierunek sortowania</td>
		<td>
			<select name="sortuj_kierunek">
				<option value="asc">rosn±co</option>
				<option value="desc">malej±co</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" name="szukaj" value="Szukaj"/>
		</td>
	</tr>
</table>

</form>