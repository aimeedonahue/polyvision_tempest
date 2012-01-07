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
class export_gm {

	private $output_directory = NULL;
	private $export_file = null;
	private $export_path = NULL;
	private $num_file = 1;

	public function __construct($directory) {
		$this->output_directory = $directory;
		$this->export_path = $this->output_directory . '/' . 'googlemerchant_'.Mage::app()->getStore()->getCode().'_'.$this->num_file.'.xml';
		$this->export_file = fopen($this->export_path, "w");
	}

	public function next_file(){
		$this->num_file += 1;
		$this->export_path = $this->output_directory . '/' . 'googlemerchant_'.Mage::app()->getStore()->getCode().'_'.$this->num_file.'.xml';
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
		$o = '<?xml version="1.0"?>';
		$o .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
		$o .= '<channel>';
		$o .= '<title>'.Mage::app()->getWebsite()->getName().'</title>';
		$o .= '<link>'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'</link>';
		$o .= '<description>Beschreibung des Inhalts</description>';

		fwrite($this->export_file, $o);
	}

	public function write_footer(){
		$o = '</channel></rss>';
		fwrite($this->export_file, $o);
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
		if (count($cats) > 0) {
			$cat_1 = $cats[0]->getName();
		}


		// title
		$name = "";
		if($prepared_product->parent_product){
			$name = $prepared_product->parent_product->getName();
		}else{
			$name = $t['name'];
		}
		echo 'product name:'.$name."\n";
		
		$o = '<item>';

		// farbe
		if($color){
			$o .= '<title>'.$name.' - '.$color.'</title>';
		}else{
			$o .= '<title>'.$name.'</title>';
		}

		$o .= '<link>'.$deep_url.'</link>';
		$o .= '<description><![CDATA['.$description.']]></description>';

		if(isset($image_url) && $image_url != NULL){
			$o .= '<g:image_link>'.$image_url.'</g:image_link>';
		}

		if($length != NULL){
			$o .= '<g:size><![CDATA[L&auml;nge '.$length.']]></g:size>';
		}

		if($width != NULL){
			$o .= '<g:size>Breite '.$width.'</g:size>';
		}

		$o .= '<g:price>'.$price.'</g:price>';
		//sonderangebot ?
		if($prepared_product->has_special_price){
			$o .= '<g:sale_price>'.$prepared_product->special_price.'</g:sale_price>';
			$o .= '<g:sale_price_effective_date>'.$prepared_product->special_price_range.'</g:sale_price_effective_date>';
			print "gm set special range\n";
		}
		
		$o .= '<g:condition>neu</g:condition>';
		$o .= '<g:id>'.$t['sku'].'</g:id>';
		$o .= '<g:ean>'.$prepared_product->ean.'</g:ean>';
		$o .= '<g:mpn>MPN-'.$prepared_product->ean.'</g:mpn>';
		$o .= '<g:brand>'.$prepared_product->brand.'</g:brand>';
		$o .= '<g:google_product_category><![CDATA[Möbel]]></g:google_product_category>';
		$o .= '<g:product_type>'.$prepared_product->category1.'</g:product_type>';
		if($prepared_product->out_of_stock == false){
			$o .= '<g:availability>in stock</g:availability>';
		}else{
			$o .= '<g:availability>out of stock</g:availability>';
			print "gm export marked: marked out of stock\n";
		}
		//$o .= '<g:marke>'.$prepared_product->brand.'</g:marke>';
		//$o .= '<g:zustand>neu</g:zustand>';
		$o .= '</item>'."\n";

		fwrite($this->export_file, $o);
	}

}

?>
