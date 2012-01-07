<?php

require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/pv_util.php');

/*
* To change this template, choose Tools | Templates
* and open the template in the editor.
*/

/**
* google merchant export
*
* exports xml via simple string functions instead of using dom generating and so on... it's faster this way !
*
* @author abierbrauer
*/
class export_billiger {

	private $output_directory = NULL;
	private $export_file = null;
	private $export_path = NULL;
	private $num_file = 1;

	public function __construct($directory) {
		$this->output_directory = $directory;
		$this->export_path = $this->output_directory . '/' . 'billiger_'.Mage::app()->getStore()->getCode().'_'.$this->num_file.'.csv';
		$this->export_file = fopen($this->export_path, "w");
	}

	public function next_file(){
		$this->num_file += 1;
		$this->export_path = $this->output_directory . '/' . 'billiger_'.Mage::app()->getStore()->getCode().'_'.$this->num_file.'.csv';
        $this->export_file = fopen($this->export_path, "w");
	}
	
	public function isWritable(){
		if($this->export_file == false || is_writable($this->export_path) == false){
			return false;
		}
		
		return true;
	}
	
	public function close() {
		fclose($this->export_file);
	}

	public function write_header() {
		$header_data = array();
		array_push($header_data,"aid");
		array_push($header_data,"name");
		array_push($header_data,"price");
		array_push($header_data,"link");
		array_push($header_data,"pzn");
		array_push($header_data,"brand");
		array_push($header_data,"mpnr");
		array_push($header_data,"ean");
		array_push($header_data,"desc");
		array_push($header_data,"shop_cat");
		array_push($header_data,"image");
		array_push($header_data,"dlv_time");
		array_push($header_data,"dlv_cost");
		array_push($header_data,"ppu");
		array_push($header_data,"category");

		fputcsv($this->export_file,$header_data,';','"');
		
	}

	public function write_footer(){
		
	}

	public function write_row(&$prepared_product,&$product) {
		$t = &$prepared_product->product_array;
		$parent_t = &$prepared_product->parent_product_array;
		$parent_product = &$prepared_product->parent_product;
		
		$gender = NULL;
		$cat_1 = NULL;
		$cat_2 = NULL;
		$color = NULL;
		$marke = NULL;
		$material = NULL;
		$description = NULL;
		$price = NULL;
		$old_price = NULL;
		$base_price = NULL;
		$currency = NULL;
		$availability = NULL;
		$send_conditions = NULL;
		$width = NULL;
		$length = NULL;
		$image_url = NULL;
		$deep_url = NULL;
		$product_date = NULL;
		$ean = NULL;
		$aux_image_url_1 = NULL;
		$aux_image_url_2 = NULL;
		$aux_image_url_3 = NULL;

		$row_data = array(); // put the csv data in this array

		if (isset($t['dimension'])) {
			$measure = $t['dimension'];
		}

		//$color = $product->getAttributeText('color');
		if($parent_product){
			$deep_url = $parent_product->getProductUrl(false);
		}else{
			$deep_url = $product->getProductUrl(false);
		}

		//if(isset($parent_t['image']) && strlen($parent_t['image']) > 0 && $parent_t['image'] != 'no_selection'){
		//	$path = Mage::getBaseDir('media').'/catalog/product/'.$parent_t['image'];
			//fwrite($this->export_file, "\n".$path."\n");
			// this sucks so much !
		//	if(file_exists($path)){
			$image_url = $product->getImageUrl();
			if (strlen($image_url) <=0  && $parent_product){
				$image_url = $parent_product->getImageUrl();
			}
			//}
		//}

		if(isset($t['component_value_width']) && strlen($t['component_value_width']) > 0){
			$width = $product->getAttributeText('component_value_width');
		}

		if(isset($t['component_value_length']) && strlen($t['component_value_length']) > 0){
			$length = $product->getAttributeText('component_value_length');
		}

		if (isset($t['a_ean'])){
			$ean = $t['a_ean'];
		}

		if (isset($t['component_value_color'])){
			$color = $product->getAttributeText('component_value_color');
		}

		if(isset($t['description']) && strlen($description) > 0){
			$description = $t['description'];
		}else{

			if($parent_product != NULL)
			{
				$description = $parent_product->getDescription();
			}else{
				$description = $t['name'];
			}

		}

		$description = str_replace("\n","<br/>",$description);

		$price = $t['price'];

		// Kategorien
		$cats = pv_util::get_category($product);
		$category_tree = "";
		if (count($cats) > 0) {
			$category_tree = pv_util::get_category_tree($cats[0],array());
			$cat_1 = $cats[0]->getName();
		}


		// title
		$name = "";
		if($prepared_product->parent_product){
			$name = $prepared_product->parent_product->getName();
		}else{
			$name = $t['name'];
		}
		echo 'billiger.de: product name:'.$name."\n";
		

		// article id
		array_push($row_data,$t['sku']);

		// article name
		if($color){
			array_push($row_data,$name.' - '.$color);
		}else{
			array_push($row_data,$name);
		}
		
		// article price
		array_push($row_data,$price);

		// article link
		array_push($row_data,$deep_url);

		// pzn will be emtpy
		array_push($row_data,"");

		// article brand
		array_push($row_data,$prepared_product->brand);

		// article mpnr
		array_push($row_data,'MPN-'.$prepared_product->ean);

		// article ean
		array_push($row_data,$prepared_product->ean);

		// article description
		array_push($row_data,$description);

		// article shop category
		echo 'shop category:'.$category_tree."\n";
		array_push($row_data,$category_tree);

		// image
		array_push($row_data,$image_url);

		// delivery time
		array_push($row_data,$t['delivery_time']);

		// delivery costs
		array_push($row_data,"0.00");

		// price per unit
		array_push($row_data,pv_util::calc_price_per_unit($t));
		array_push($row_data,"category");
		

		fputcsv($this->export_file,$row_data,';','"');
	}

}

?>