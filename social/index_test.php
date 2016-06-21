<?php
	define('DATABASE', 'Sql282290_1');
	define('HOST', '62.149.150.102');
	define('DB_USER', 'Sql282290');
	define('DB_PASS', 'cc6c6aba');

	include('classes/db.php');

	$db = new Db;
	
	$res = $db->query('
					  SELECT
						  new_qualifiche.Qualifica as qualifica,
						  new_tesserati.Cod_societa as codicesocieta,
						  COUNT(*) as totale,
						  new_tesserati.Categoria_ricreativi as cat_ricreat,
						  new_tesserati.Cod_qualifica as codqualifica
					  FROM new_tesserati
						  INNER JOIN new_societa ON new_societa.Cod_societa = new_tesserati.Cod_societa
						  INNER JOIN new_elenco_regioni ON new_elenco_regioni.Reg_valore = new_tesserati.Cod_reg
						  INNER JOIN new_qualifiche ON new_qualifiche.Codice = new_tesserati.Cod_qualifica
					  WHERE
						  new_tesserati.nome != "Andrea"
					  GROUP BY
						  new_tesserati.Cod_societa,
						  new_tesserati.Cod_qualifica,
						  new_qualifiche.Qualifica,
						  new_tesserati.Categoria_ricreativi
					');
	
	foreach ($res as $i) {
		echo($i->qualifica.'<br>');
	}
?>