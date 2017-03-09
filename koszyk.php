<?

class koszyk {
	
	public function __construct() {
		$link = mysql_connect(dbHost, dbLogin, dbPassword) or die("Nie uda³o siê po³±czyæ z baz± danych.");
		mysql_select_db(dbName, $link) or die("Nie uda³o siê wybraæ bazy danych.");
		mysql_query("SET NAMES latin2");
	}
	
	/**
	 * Dodaje ksiazke do koszyka. Jesli ksiazka jest juz w koszyku, zwieksza ilosc o 1.
	 *
	 * @param int $idKsiazki
	 * @param string $idSesji
	 * @return bool
	 */
	public function dodajDoKoszyka($idKsiazki, $idSesji) {
		if($this->czyJestWKoszyku($idKsiazki, $idSesji))
			$sql = "UPDATE koszyk SET ilosc = ilosc+1 WHERE session_id = '$idSesji' AND id_ksiazki = $idKsiazki";
		else 
			$sql = "INSERT INTO koszyk (session_id, id_ksiazki, ilosc) VALUES ('$idSesji', $idKsiazki, 1)";

		if(mysql_query($sql))
			return true;
		else 
			return false;
	}

	public function ileWKoszyku($idSesji)
	{
		$sql = "SELECT * FROM koszyk WHERE session_id = '$idSesji'";
		return mysql_num_rows(mysql_query($sql));
	}

	public function dodajDoKoszykaIlosc($idKsiazki, $idSesji, $ilosc) 
	{
		$sql = "UPDATE koszyk SET ilosc = $ilosc WHERE session_id = '$idSesji' AND id_ksiazki = $idKsiazki";

		if(mysql_query($sql))
			return true;
		else 
			return false;
	}


	
	/**
	 * Sprawdza, czy ksiazka o podanym id jest juz w koszyku.
	 *
	 * @param int $idKsiazki
	 * @param string $idSesji
	 * @return bool
	 */
	public function czyJestWKoszyku($idKsiazki, $idSesji) {
		$sql = "SELECT * FROM koszyk WHERE id_ksiazki = $idKsiazki AND session_id = '$idSesji'";
		$result = mysql_query($sql);
		
		return (mysql_num_rows($result) > 0);
	}
	
	/**
	 * Usuwa ksiazke z koszyka.
	 *
	 * @param int $idKsiazki
	 * @param string $idSesji
	 * @param int $ilosc
	 * @return bool
	 */
	public function zmienLiczbeElementow($idKsiazki, $idSesji, $ilosc) {
		if($ilosc <= 0)
			$sql = "DELETE FROM koszyk WHERE session_id = '$idSesji' AND id_ksiazki = $idKsiazki";
		else 
			$sql = "UPDATE koszyk SET ilosc = $ilosc WHERE session_id = '$idSesji' AND id_ksiazki = $idKsiazki";
		
		if(mysql_query($sql))
			return true;
		else 
			return false;
	}
	
	/**
	 * Pobiera z bazy zawartosc koszyka oraz generuje HTML.
	 *
	 * @param string $idSesji
	 * @return string $tabela
	 */
	public function zwrocKoszyk($idSesji) {
		
			$sql = "SELECT koszyk.id, koszyk.id_ksiazki AS id_ksiazki, ksiazki.tytul AS tytul, autorzy.nazwisko AS nazwisko, autorzy.imie AS imie, ksiazki.cena AS cena, koszyk.ilosc AS ilosc
			FROM koszyk, ksiazki, autorzy
			WHERE koszyk.id_ksiazki=ksiazki.id 
			AND ksiazki.id_autora=autorzy.id 
			AND koszyk.session_id = '$idSesji'";
		
		
		$result = mysql_query($sql);
		$tabela = '';
		
		$i = 1;
		while($row = mysql_fetch_array($result)) {
			if($i%2 == 0) $tabela .= "<tr class='kolorowy'>";
			else $tabela .= "<tr>";
			$wartosc= $row[cena]*$row[ilosc];
			$tabela .= "<td>$i</td>";
			$tabela .= "<td>$row[tytul]</td>";
			$tabela .= "<td>$row[nazwisko] $row[imie]</td>";
			$tabela .= "<td>$row[cena]</td>";
			$tabela .= "<td>$wartosc</td>";
			$tabela .= "<td style='text-align: center;'>
					<form method='post' action='index.php?dzial=koszyk&akcja=zmien'>
						<input type='hidden' name='id' value='$row[id_ksiazki]' />
						<input type='text' name='ilosc' value='$row[ilosc]' style='width: 20px;' />
						<input type='submit' name='zmien_ilosc' value='Zmieñ' />
					</form>
					</td>";
			$tabela .= "</tr>";
			$i++;
		}
		
		return $tabela;
	}
}

$koszyk = new koszyk();

if(isset($_GET['akcja'])) 
{
	if($_GET['akcja'] == 'dodaj') 
	{
		// obsluga dodawania ksiazki
		if(!empty($_POST['id_ksiazki'])) 
		{
			$idKsiazki = (int)$_POST['id_ksiazki'];
			if($idKsiazki > 0) 
			{
				$koszyk->dodajDoKoszyka($idKsiazki, session_id());
				header("Location: index.php?".$_POST['qs']."&komunikat=1");
			}
		}
	}
	if($_GET['akcja'] == 'zmien') 
	{
		if(!empty($_POST['id'])) 
		{
			$idKsiazki = (int)$_POST['id'];
			if($_POST['ilosc'] > 0) 
			{
				$koszyk->dodajDoKoszykaIlosc($idKsiazki, session_id(), $_POST['ilosc']);
			}
		}


	}
	
}

?>

<h1>Koszyk</h1>

<table class="tabela" cellspacing="0" style="width: 90%; margin: 10px;">
	<tr>
		<th>Lp</th>
		<th>Tytu³</th>
		<th>Autor</th>
		<th>Cena</th>
		<th>Warto¶æ</th>
		<th>Ilo¶æ</th>
	</tr>
	
	<?
		echo $koszyk->zwrocKoszyk(session_id());
		$_SESSION['wkoszyku'] = $koszyk->ileWKoszyku(session_id());
	?>
</table>

