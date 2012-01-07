<?php

/*
* To change this template, choose Tools | Templates
* and open the template in the editor.
*/

/**
* Description of util
*
* @author abierbrauer
*/
class pv_util {

	public static function gm_date($v){
		$bla = date_parse_from_format('Y-m-j H:i:s',$v);
		if(strlen(strval($bla['month'])) != 2){
			$bla['month'] = '0'.$bla['month'];
		}
		
		if(strlen(strval($bla['day'])) != 2){
			$bla['day'] = '0'.$bla['day'];
		}
		
		return $bla['year'].'-'.$bla['month'].'-'.$bla['day'].'T00:00:00Z';
	}
	
	public static function get_category(&$product) {
		if($product == NULL){
			return array();
		}
		$data = array();
		$cats = $product->getCategoryIds();
		foreach ($cats as $categoryId) {
			$category = Mage::getModel('catalog/category')->load($categoryId);
			array_push($data,$category);
		}

		return $data;
	}

	public static function get_category_tree(&$category,$tree,$tree_string = "") {
		if($category == NULL){
			return $tree_string;
		}

		
		array_push($tree,$category->getName());
		$parent_category_id = $category->getParentId();
		echo '$parent_category_id:'.$parent_category_id." for ".$category->getName()."\n";
		if($parent_category_id == 0 || $parent_category_id == NULL || $parent_category_id == 1 || $parent_category_id == 2){
			
			$tree_string = "";
			for($i = count($tree)-1; $i >= 0 ; $i--){
				$cat = $tree[$i];
				if($i != count($tree)-1){
					$tree_string = $tree_string.' > '.$cat;	
				}else{
					$tree_string = $cat;
				}
				
			}
			
			return $tree_string;
		}else{
			
			$tree_string = pv_util::get_category_tree(Mage::getModel('catalog/category')->load($parent_category_id),$tree,$tree_string);
		}
		return $tree_string;
	}

	public static function debugmail($to,$subject,$body){
		$empfaenger = $to;
		$betreff = 'pv_util::debugmail '.$subject;
		$nachricht = $body;
		$header = 'From: debugmailer@magento_somewhere.com' . "\r\n" .
		'Reply-To: debugmailer@magento_somewhere.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

		mail($empfaenger, $betreff, $nachricht, $header);
	}
	
	public static function format_bytes($size) {
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}

	public static function calc_price_per_unit(&$product_array){
		try{
			$base_price_amount = $product_array['base_price_amount'];
			$price = $product_array['price'];
			$base_price_base_unit = $product_array['base_price_base_unit'];

			if(floatval($base_price_amount) != NULL || floatval($base_price_amount) != 0){
				$price_per_unit = floatval($price) / floatval($base_price_amount);

				return round($price_per_unit,2).'EUR/1'.$base_price_base_unit;
			}
			else{
				return "";
			}
		}catch (Exception $e) {
			var_dump($e->getMessage());
			return "";
		}
	}

	public static function calc_base_price(&$product_array){
		try{
			$base_price_amount = $product_array['base_price_amount'];
			$price = $product_array['price'];
			$base_price_base_unit = $product_array['base_price_base_unit'];

			if(floatval($base_price_amount) != NULL || floatval($base_price_amount) != 0){
				$price_per_unit = floatval($price) / floatval($base_price_amount);

				return round($price_per_unit,2);
			}
			else{
				return "";
			}
		}catch (Exception $e) {
			var_dump($e->getMessage());
			return "";
		}	
	}

	public static function write_csv_line($file_handle,&$data,$delim,$enclosure){
		$line = "";
		for($i =0; $i < count($data); $i++){
			
			$t = $enclosure.$data[$i].$enclosure;

			$line .= $t;

			if($i < count($data)-1){
				$line .= $delim;
			}else{ // end of line
				$line.="\n";
			}
		}
		fwrite($file_handle,$line);
	}
}

?>
