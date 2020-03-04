<?php

$ILOSCI = array();

//SAMOCHODY 3 LUB 4 ???
$ILOSCI[0] = array(
	"id_category" => 36,
	"count" => 3
);
$ILOSCI[1] = array(
	"id_category" => 75,
	"count" => 2
);
$ILOSCI[2] = array(
	"id_category" => 401,
	"count" => 1
);

$host = '127.0.0.1';
$user = 'agtom_1';
$passwd = 'Presta123';
$db = "agtom_1";

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $passwd);
$conn->exec("set names utf8");
date_default_timezone_set("Europe/Warsaw");

if(isset($_POST["f"]) && $_POST["f"] == 1) {	
	$poziom = $_POST['poziom'];

	$q = $conn->prepare("SELECT * FROM category WHERE id_parent=$poziom");
	$q->execute(); 
	$result = $q->fetchAll();
	$q -> closeCursor();
	
	$poziomy = array();	
	
	for($i = 0; $i < count($result); $i++){
		
		$id = $result[$i]['id_category'];
		
		$q = $conn->prepare("SELECT name FROM category_lang WHERE id_category='$id'");
		$q->execute(); 	
		$result1 = $q->fetchAll();
		$q -> closeCursor();
		
		$temp = array(	
			"ID"=>$id,
			"NAME"=>$result1[0]['name']
		);		
		
		array_push($poziomy, $temp);				
	}
	
	print_r(json_encode($poziomy));

}
if(isset($_POST["f"]) && $_POST["f"] == 2) {
	$id = $_POST['id'];
	$nowe_id_id = '';
	
	$ilosci = 0;
	
	for($i = 0; $i < count($ILOSCI); $i++){
		if($id == $ILOSCI[$i]['id_category']){
			$ilosci = $ILOSCI[$i]['count'];
		}
	}
	
	if($ilosci == 0)
		$ilosci = 3;//DOMYSLNIE
	
	//LIMIT- maks produktow do wyswietlenia- domyslnie 10
	$limit = 1;
	$produktyBezCechy = array();

	//$q = $conn->prepare("SELECT id_product FROM category_product WHERE id_product NOT IN (SELECT DISTINCT id_product FROM feature_product WHERE id_feature!=11 AND id_feature!=18) AND id_product IN (SELECT id_product FROM stock_available WHERE quantity>0 OR out_of_stock=1)  AND id_category=$id");
	$q = $conn->prepare("SELECT id_product FROM category_product WHERE id_product IN (SELECT id_product FROM stock_available WHERE quantity>0 OR out_of_stock=1)  AND id_category=$id");
	$q->execute(); 
	$result = $q->fetchAll();
	$q  -> closeCursor();	
	
	
	
	for($k = 0; $k < count($result); $k++){	

		$id = $result[$k]['id_product'];	
	
		$q = $conn->prepare("SELECT id_feature FROM feature_product WHERE id_product=$id");
		$q->execute(); 
		$result1 = $q->fetchAll();
		$q  -> closeCursor();	
	
		$kategorie = array();
	
		for($l = 0; $l < count($result1); $l++){	
			
			if($l == 0){
				
				array_push($kategorie, $result1[$l]['id_feature']);
				
			}else{
			
				$zmienna = 0;
			
				for($m = 0; $m < count($kategorie); $m++){
					
					if($kategorie[$m] == $result1[$l]['id_feature'] || $result1[$l]['id_feature'] == 11)//BEZ CECHY SKALA
						$zmienna = 1;
					
				}
				if($zmienna == 0){
					
					array_push($kategorie, $result1[$l]['id_feature']);
					
				}
			
			}		
		}		
		
		if(count($kategorie) < $ilosci){//POBRANIE MAKS
			
			array_push($produktyBezCechy, $id);
						
		}	
	}	
	
	$produkty1 = array();	
	
	if(count($produktyBezCechy) < $limit)
		$limit = count($produktyBezCechy);
	
	for($i = 0; $i < $limit; $i++){
		
		$idd = $produktyBezCechy[$i];
		
		$q = $conn->prepare("SELECT name FROM product_lang WHERE id_product='$idd'");
		$q->execute(); 	
		$result1 = $q->fetchAll();
		$q = null;
		
		$q = $conn->prepare("SELECT reference FROM product WHERE id_product='$idd'");
		$q->execute(); 	
		$result11 = $q->fetchAll();
		$q = null;
		
		$temp = array(	
			"ID"=>$idd,
			"NAME"=>$result1[0]['name'],
			"REF"=>$result11[0]['reference']
		);		
		
		array_push($produkty1, $temp);				
	}
		
	print_r(json_encode($produkty1));

}if(isset($_POST["f"]) && $_POST["f"] == 3) {
	
	$id = $_POST['id'];
	
	$q = $conn->prepare("SELECT id_category FROM category_product WHERE id_product=$id");
	$q->execute(); 
	$result = $q->fetchAll();
	$q -> closeCursor();
	
	sort($result);
	
	$produkty = array();	
	
	for($i = 0; $i < count($result); $i++){
		
		$id = $result[$i]['id_category'];
		
		$q = $conn->prepare("SELECT name FROM category_lang WHERE id_category='$id'");
		$q->execute(); 	
		$result1 = $q->fetchAll();
		$q -> closeCursor();
		
		$temp = array(	
			"NAME"=>$result1[0]['name'],
			"ID"=>$result[i]['id_category']
		);		
		
		array_push($produkty, $temp);				
	}
	
	print_r(json_encode($produkty));
	
}if(isset($_POST["f"]) && $_POST["f"] == 4) {
	//zdjecia
	
	$id = $_POST['id'];
	
	$q = $conn->prepare("SELECT id_image FROM image WHERE id_product=$id AND cover=1");
	$q->execute(); 
	$result = $q->fetchAll();
	$q -> closeCursor();
	
	$dir = implode('/',str_split($result[0]['id_image'])); 
	$url = "http://agtom.eu/img/p/".$dir."/".$result[0]['id_image'].".jpg";

	
	
	print_r($url);
	
	

}if(isset($_POST["f"]) && $_POST["f"] == 5) {
	
	$id = $_POST['id'];
	
	$q = $conn->prepare("SELECT * FROM feature_product WHERE id_product=$id");
	$q->execute(); 
	$result = $q->fetchAll();
	$q -> closeCursor();

	$cechy = array();	
	
	if(count($result) ==  0){
		
		print_r(count($result));
		
	}else{
		
		for($i = 0; $i < count($result); $i++){
			
			$t = $result[$i]['id_feature_value'];
			
			$q = $conn->prepare("SELECT * FROM feature_value_lang WHERE id_feature_value=$t");
			$q->execute(); 	
			$result1 = $q->fetchAll();
			$q -> closeCursor();
			
			
			$temp = array(	
				"CECHA" => $result1[0]['value'],
				"ID" => $result1[0]['id_feature_value']
			);		
		
			array_push($cechy, $temp);			
		}				
		
		print_r(json_encode($cechy));
		
	}	
}if(isset($_POST["f"]) && $_POST["f"] == 6) {
	
	$idd = $_POST['id'];
	$ilosc = 0;
	
	for($i = 0; $i < count($ILOSCI); $i++){
		if($idd == $ILOSCI[$i]['id_category']){
			$ilosci = $ILOSCI[$i]['count'];
		}
	}

	if($ilosci == 0)
		$ilosci = 3;//DOMYSLNIE
	
	$q = $conn->prepare("SELECT id_product FROM category_product WHERE id_product NOT IN (SELECT id_product FROM feature_product) AND id_category=$idd AND id_product IN (SELECT id_product FROM stock_available WHERE quantity>0 OR out_of_stock=1)");	
	$q->execute(); 
	$result = $q->fetchAll();
	$q  -> closeCursor();	
	
	$q = $conn->prepare("SELECT id_product FROM feature_product WHERE id_product IN (SELECT id_product FROM stock_available WHERE quantity>0 OR out_of_stock=1) AND id_product IN (SELECT id_product FROM category_product WHERE id_category=$idd) GROUP BY id_product HAVING COUNT(*) < $ilosci");
	$q->execute(); 
	$result1 = $q->fetchAll();
	$q  -> closeCursor();	
	
	$bez_cech = count($result);
	$z_cechami = count($result1);
	
	
	print_r($bez_cech+$z_cechami);	
}if(isset($_POST["f"]) && $_POST["f"] == 'wybrane') {
	
	$id = $_POST['iden'];
	$opcja = $_POST['opcja'];
	
	if($opcja == 1){
		//usuniecie konkretnego opisu
		$q = "DELETE from feature_product WHERE id_product=$id";
		$conn->exec($q);			
	}
	if($opcja == 2){		
		//POBRANIE ID MODELU
		$q = $conn->prepare("SELECT id_product FROM product WHERE reference='$id'");
		$q->execute(); 	
		$result1 = $q->fetchAll();
		$q = null;
		//USUWANIE CECH
		$id = $result1[0]['id_product'];
		$q = "DELETE from feature_product WHERE id_product=$id";
		$conn->exec($q);
	}
	if($opcja == 3 && count(str_split($id)) > 4){
		//POBRANIE ID PO NAZWIE
		$q = $conn->prepare("SELECT id_product FROM product WHERE id_product IN (SELECT DISTINCT id_product FROM product_lang WHERE name LIKE '$id')");
		$q->execute(); 	
		$result = $q->fetchAll();
		$q -> closeCursor();	
		//USUWANIE
		for($i = 0; $i < count($result); $i++){
			//USUWANIE OPISU
			$usuwane_id = $result[$i]['id_product'];
			$q = "DELETE from feature_product WHERE id_product=$usuwane_id";
			$conn->exec($q);			
		}
	}
}
$conn = null;
?>