<?

class katalog {
	/**
	 * Po³±czenie do bazy.
	 *
	 * @var object
	 */
	private $_connection;
	
	/**
	 * Ile rekordów ma byæ wy¶wietlanych na stronie.
	 *
	 * @var int
	 */
	private $_rekordowNaStronie = 3;
	
	/**
	 * Aktualnie wybrana strona wyników wyszukiwania.
	 *
	 * @var int
	 */
	private $_strona;
	
	/**
	 * Przechowuje zapytanie pobieraj±ce okre¶lone dane bez klauzuli LIMIT (wszystkie rekordy).
	 *
	 * @var string
	 */
	private $_zapytanieBezLimit;
	
	public function __construct() {
		$link = mysql_connect(dbHost, dbLogin, dbPassword) or die("Nie uda³o siê po³±czyæ z baz± danych.");
		mysql_select_db(dbName, $link) or die("Nie uda³o siê wybraæ bazy danych.");
		mysql_query("SET NAMES latin2");
		$this->_connection = $link;
		
		if(!empty($_GET['strona']))
			$this->_strona = (int)$_GET['strona'];
		else 
			$this->_strona = 0;
	}
	
	/**
	 * Buduje SQL z zapytaniem do bazy. Uwzglêdnia wszystkie warunki oraz wybran± stronê.
	 *
	 * @return string
	 */
	public function zbudujZapytanie() {
		$query = "SELECT *, k.id AS id_ksiazki FROM ksiazki k JOIN autorzy a ON k.id_autora = a.id WHERE 1=1 ";
		
		// dodawanie warunków z formularza wyszukiwania
		if(!empty($_GET['tytul']))
			$query .= "AND k.tytul LIKE '%$_GET[tytul]%' ";
		if(!empty($_GET['autor']))
			$query .= "AND a.nazwisko LIKE '%$_GET[autor]%' ";
		if(!empty($_GET['rodzaj_ksiazki']))
			$query .= "AND k.id_rodzaju = '$_GET[rodzaj_ksiazki]' ";
		if(!empty($_GET['cena_od'])) {
			$cena_od = (int)$_GET['cena_od'];
			$query .= "AND k.cena >= $cena_od ";
		}
		if(!empty($_GET['cena_do'])) {
			$cena_do = (int)$_GET['cena_do'];
			$query .= "AND k.cena <= $cena_do ";
		}
		
		$this->_zapytanieBezLimit = $query;
			
		// dodanie warunkow sortowania
		if(!empty($_GET['sortuj_po'])) {
			if($_GET['sortuj_po'] == 'tytul')
				$query .= " ORDER BY k.tytul";
			if($_GET['sortuj_po'] == 'autor')
				$query .= " ORDER BY a.nazwisko";
			if($_GET['sortuj_po'] == 'cena')
				$query .= " ORDER BY k.cena";
				
			if(!empty($_GET['sortuj_kierunek'])) {
				if($_GET['sortuj_kierunek'] == 'desc')
					$query .= " DESC";
				if($_GET['sortuj_kierunek'] == 'asc')
					$query .= " ASC";
			}
		}
			
		$query .= " LIMIT ".($this->_strona*$this->_rekordowNaStronie).", ".$this->_rekordowNaStronie;
		
		return $query;
	}
	
	/**
	 * Zwraca HTML z linkami do wszystkich stron wyników. Zachowuje informacje o parametrach wyszukiwania.
	 *
	 * @return string
	 */
	public function zwrocLinkiStron() {
		$result = mysql_query($this->_zapytanieBezLimit);
		
		if($result) {
			$liczbaRekordow = mysql_num_rows($result);
			$liczbaStron = ceil($liczbaRekordow / $this->_rekordowNaStronie);
			$queryString = $_SERVER['QUERY_STRING'];
			if(isset($_GET['strona']))
				$queryString = str_replace("&strona=".$_GET['strona'], '', $queryString);
			if(isset($_GET['komunikat']))
				$queryString = str_replace("&komunikat=".$_GET['komunikat'], '', $queryString);
			$html = '';
			
			for($i=0; $i<$liczbaStron; $i++)
				$html .= "<a href='index.php?$queryString&strona=$i'>".($i+1)."</a>";
			
			return $html;
		}
		
		return '';
	}
	
	/**
	 * Zwraca tabelê HTML z danymi.
	 *
	 * @param string $query Zapytanie, które nazle¿y wykonaæ
	 * @return string
	 */
	public function zwrocTabele($query) {
		$result = mysql_query($query);
		$tabela = '';
		$i = $this->_strona*$this->_rekordowNaStronie+1;
		while($row = mysql_fetch_array($result)) {
			if($i%2 == 0)
				$tabela .= "<tr class='kolorowy'>";
			else
				$tabela .= "<tr>";
			$tabela .= "<td>$i</td>";
			$tabela .= "<td>".$row['tytul']."</td>";
			$tabela .= "<td>".$row['id_autora']."</td>";
			$tabela .= "<td>".$row['id_rodzaju']."</td>";
			$tabela .= "<td>".$row['cena']."</td>";
			$tabela .= "<td>
						<a href='index.php?dzial=szczegoly&amp;id=".$row['id_ksiazki']."'>szczegó³y</a>
						<form method='post' action='index.php?dzial=koszyk&akcja=dodaj' name='form_$row[id_ksiazki]'>
						<input type='hidden' name='qs' value='$_SERVER[QUERY_STRING]' />
						<input type='hidden' name='id_ksiazki' value='$row[id_ksiazki]' />
						<a href='#' onclick='document.forms.form_$row[id_ksiazki].submit()'>Dodaj do koszyka</a>
						</form>
						</td>";
			$tabela .= "</tr>";
			$i++;
		}
		
		return $tabela;
	}
}

// obs³uga wy¶wietlania komunikatów
if(isset($_GET['komunikat'])) {
	if($_GET['komunikat'] == 1)
		echo "<div id='msg'>Ksi±¿ka zosta³a dodana do koszyka.</div>";
}

?>

<table class="tabela" cellspacing="0" style="width: 90%; margin: 10px;">
	<tr>
		<th>Lp</th>
		<th>Tytu³</th>
		<th>Autor</th>
		<th>Rodzaj</th>
		<th>Cena</th>
		<th>&nbsp;</th>
	</tr>


<?

$katalog = new katalog();
$query = $katalog->zbudujZapytanie();
echo $katalog->zwrocTabele($query);

?>
	<tfoot>
		<tr>
			<td colspan="6">
<?

echo $katalog->zwrocLinkiStron();

?>

			</td>
		</tr>
	</tfoot>
</table>