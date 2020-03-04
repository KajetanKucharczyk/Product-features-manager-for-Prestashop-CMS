<?php

function cmp($a, $b) {
	return strcasecmp($a['value'], $b['value']);
}

$host = 'xxx';
$user = 'xxx';
$passwd = 'xxx';
$db = "xxx";

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $passwd);
$conn->exec("set names utf8");
include 'cechyTabela.php';

if (!conn) {
	die("Error SQL: " . mysql_error());
}

date_default_timezone_set("Europe/Warsaw");

if(isset($_POST["f"]) && $_POST["f"] == 1) {
	//ID
	$id = $_POST['id'];
	//TABLICA NA ID CECHY
	$id_feature = array();		
	for($i = 0; $i < count($CECHY); $i++){			
		if($CECHY[$i]['id_category'] == 0 || $CECHY[$i]['id_category'] == $id){
			array_push($id_feature, $CECHY[$i]['id_feature']);
		}
	}		
	//TABLICA NA CECHY
	$features = array();
	//PETLA PO TABLICY ID CECHY
	for($i = 0; $i < count($id_feature); $i++){	
		
		$idd = $id_feature[$i];
		$q = $conn->prepare("SELECT * FROM feature_lang WHERE id_feature=$idd AND id_lang=1");
		$q->execute(); 	
		$result = $q->fetchAll();
		$q -> closeCursor();
		
		$temp = array(	
			"NAME"=>$result[0]['name'],
			"ID"=>$result[0]['id_feature'],
			"FEATURES"=>0
		);		
		array_push($features, $temp);		
	}
	//PETLA PO TABLICY CECHYH
	for($j = 0; $j < count($features); $j++){
		$idd = $features[$j]['ID'];		
		$nazwy = array();
		//SQL
		$q = $conn->prepare("SELECT value, id_feature_value FROM feature_value_lang WHERE id_lang=1 AND id_feature_value IN (SELECT id_feature_value FROM feature_value WHERE id_feature=$idd)");
		$q->execute(); 	
		$result = $q->fetchAll();
		$q -> closeCursor();
		//PETLA PO WYNIKACH
		for($k = 0; $k < count($result); $k++){
			//WKLEJENIE WYNIKÓW
			$temp = array(
				"id_feature_value" => $result[$k]['id_feature_value'],
				"value" => $result[$k]['value']
			);
			array_push($nazwy, $temp);
		}	
		//SORTOWANIE CECH
		usort($nazwy, 'cmp');
		$features[$j]['FEATURES'] = $nazwy;		
	}
	//WYPLUCIE JSONA
	print_r(json_encode($features));	
}if(isset($_POST["f"]) && $_POST["f"] == 2) {
	//dodawanie cech
	$id_feature_value = $_POST['id_feature_value'];
	$id_product = $_POST['id_product'];
	$id_feature = $_POST['id_feature'];
	
	$q = "INSERT INTO feature_product (id_feature, id_feature_value, id_product) VALUES ('$id_feature', '$id_feature_value', '$id_product')";
	$conn->exec($q);
	
	print_r('dodano');
}if(isset($_POST["f"]) && $_POST["f"] == 3) {
	//usuwanie cech
	$id_feature_value = $_POST['id_feature_value'];
	$id_product = $_POST['id_product'];
	$id_feature = $_POST['id_feature'];
	
	$q = "DELETE from feature_product WHERE id_feature=$id_feature AND id_feature_value=$id_feature_value AND id_product=$id_product";
	$conn->exec($q);
	
	print_r('usunieto');
}if(isset($_POST["f"]) && $_POST["f"] == 4) {
	
	$q = $conn->prepare("SELECT * FROM feature_product");
	$q->execute(); 	
	$result = $q->fetchAll();
	$q -> closeCursor();
	
	print_r(json_encode($result));
}

$conn = null;
?>