<?php

require_once(getcwd().'/app/code/local/Polyvision/Tempest/lib/export_ladenzeile.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/export_billiger.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/export_nextag.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/export_pangora.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/export_preisroboter.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/export_preissuchmaschine.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/export_shopping.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/export_shopzilla.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/export_gm.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/pv_util.php');

class Polyvision_Tempest_Model_Observer {

    /**
     * Der Magento-Cron-Prozess ruft diese Observer-Methode zum konfigrierten Zeitpunkt auf.
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledExport($schedule) {
	if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"startign scheduled export", 0);
	}
        
        $num_exported_products = 0;

        $export_directory = Mage::getStoreConfig('tempest_options/general_options/export_directory');
        
        // google merchant initialization
        $exporter_gm = new export_gm($export_directory);
		if($exporter_gm->isWritable() == false){
			if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
		        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"google merchantfile is not writable", 0);
			}
			return;
		}

		// billiger.de initialization
		$exporter_billiger = new export_billiger($export_directory);
		if($exporter_billiger->isWritable() == false){
			if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
		        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"billiger.de csv file is not writable", 0);
			}
			return;
		}

		// nexttag initialization
		$export_nextag = new export_nextag($export_directory);
		if($export_nextag->isWritable() == false){
			if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
		        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"nextag csv file is not writable", 0);
			}
			return;
		}

		// pangora initialization
		$export_pangora = new export_pangora($export_directory);
		if($export_nextag->isWritable() == false){
			if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
		        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"pangora csv file is not writable", 0);
			}
			return;
		}

		// preisroboter initialization
		$export_preisroboter = new export_preisroboter($export_directory);
		if($export_preisroboter->isWritable() == false){
			if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
		        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"preisroboter csv file is not writable", 0);
			}
			return;
		}

		// preissuchmaschine initialization
		$export_preissuchmaschine = new export_preissuchmaschine($export_directory);
		if($export_preissuchmaschine->isWritable() == false){
			if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
		        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"export_preissuchmaschine csv file is not writable", 0);
			}
			return;
		}

		// shopping initialization
		$export_shopping = new export_shopping($export_directory);
		if($export_shopping->isWritable() == false){
			if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
		        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"export_shopping csv file is not writable", 0);
			}
			return;
		}

		// shopping initialization
		$export_shopzilla = new export_shopzilla($export_directory);
		if($export_shopping->isWritable() == false){
			if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
		        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"export_shopzilla csv file is not writable", 0);
			}
			return;
		}

        $exporter_gm->write_header();
        $exporter_billiger->write_header();
        $export_nextag->write_header();
        $export_pangora->write_header();
        $export_preisroboter->write_header();
        $export_preissuchmaschine->write_header();
        $export_shopping->write_header();
        $export_shopzilla->write_header();

		//$exporter_ladenzeile = new export_ladenzeile($export_directory);
        //$exporter_ladenzeile->write_header();

        $product = Mage::getModel('catalog/product');

        //$outfile = fopen($export_directory."/test.csv", "w");
        
		// getting out of stocks
		$out_of_stock_items = array();
		$stockCollection = Mage::getModel('cataloginventory/stock_item')->getCollection()->addFieldToFilter('is_in_stock', 0);
		foreach ($stockCollection as $item) {
		        array_push($out_of_stock_items,$item->getOrigData('product_id'));
		    }
		
        // getting the products
        $visibility = array();

		$data = $product->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('type_id', 'simple'); //->addAttributeToFilter('visibility', $visibility);
		//$data[0] = Mage::getModel('catalog/product')->load(385);
		$num_products = $product->getCollection()->count();
		echo 'exporting '.$num_products."\n";
		
		if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
	        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"exporting ~".$num_products." products", 0);
		}
		
        $max = 0;
		print "starting export\n";
		$start_time = time();
		var_dump($start_time);
        foreach ($data as $tProduct) {
			$step_time = time();
            if ($tProduct->getTypeID() == "simple" && $tProduct->getStatus() == "1") { // export only simple products
				print 'exporting product: '.$tProduct->getID()."\n";
				
                $max++;
                if ($max > 10000) {
					$exporter_gm->write_footer();
					$exporter_billiger->write_footer();
					$export_nextag->write_footer();
					$export_pangora->write_footer();
					$export_preisroboter->write_footer();
					$export_preissuchmaschine->write_footer();
					$export_shopping->write_footer();
					$export_shopzilla->write_footer();
					//$exporter_ladenzeile->write_footer();
					
			        $exporter_gm->close();
			        $exporter_billiger->close();
			        $export_nextag->close();
			        $export_pangora->close();
			        $export_preisroboter->close();
			        $export_preissuchmaschine->close();
			        $export_shopping->close();
			        $export_shopzilla->close();
					//$exporter_ladenzeile->close();
					
					//$exporter_ladenzeile->next_file();
					$exporter_gm->next_file();
					$exporter_billiger->next_file();
					$export_nextag->next_file();
					$export_pangora->next_file();
					$export_preisroboter->next_file();
					$export_preissuchmaschine->next_file();
					$export_shopping->next_file();
					$export_shopzilla->next_file();
					
					//$exporter_ladenzeile->write_header();
					$exporter_gm->write_header();
					$exporter_billiger->write_header();
					$export_nextag->write_header();
					$export_pangora->write_header();
					$export_preisroboter->write_header();
					$export_preissuchmaschine->write_header();
					$export_shopping->write_header();
					$export_shopzilla->write_header();
					
                   $max = 0;
                }
                
				// exporting one line
				$prepared_product = $this->prepareProduct($tProduct,$out_of_stock_items);
                $exporter_gm->write_row($prepared_product,$tProduct);
                $exporter_billiger->write_row($prepared_product,$tProduct);
                $export_nextag->write_row($prepared_product,$tProduct);
                $export_pangora->write_row($prepared_product,$tProduct);
                $export_preisroboter->write_row($prepared_product,$tProduct);
                $export_preissuchmaschine->write_row($prepared_product,$tProduct);
                $export_shopping->write_row($prepared_product,$tProduct);
                $export_shopzilla->write_row($prepared_product,$tProduct);
				//$exporter_ladenzeile->write_row($prepared_product,$tProduct);
				
                $num_exported_products++;

                //fwrite($outfile, print_r($t, true));
                
            }else{
			print 'ignored product: '.$tProduct->getID()."type:".$tProduct->getTypeID()." vis:".$tProduct->getVisibility()." state:".$tProduct->getStatus()."\n";
			}
			echo 'elapsed time: '.(time() - $start_time). ' step took: '.(time() - $step_time)."\n";
        }

        $exporter_gm->write_footer();
        $exporter_billiger->write_footer();
        $export_nextag->write_footer();
        $export_pangora->write_footer();
        $export_preisroboter->write_footer();
        $export_preissuchmaschine->write_footer();
        $export_shopping->write_footer();
        $export_shopzilla->write_footer();
		//$exporter_ladenzeile->write_footer();
        $exporter_gm->close();
        $exporter_billiger->close();
        $export_nextag->close();
        $export_pangora->close();
        $export_preisroboter->close();
        $export_preissuchmaschine->close();
        $export_shopping->close();
        $export_shopzilla->close();
		//$exporter_ladenzeile->close();

	if(strlen(Mage::getStoreConfig('tempest_options/general_options/export_email')) > 1){
        	pv_util::debugmail(Mage::getStoreConfig('tempest_options/general_options/export_email'),"num exported products", $num_exported_products);
	}
        print getcwd();
    }

	/**
	* preparing the product data for exporting
	**/
	public function prepareProduct(&$product,&$out_of_stock_items){
		$preparedProduct = new StdClass();
		
		$preparedProduct->product_array = $product->toArray();
		$preparedProduct->parent_product_array = NULL;
		$preparedProduct->parent_product = NULL;
		
		// defaults
		$preparedProduct->color = NULL;
		$preparedProduct->product_url = NULL;
		$preparedProduct->width = NULL;
		$preparedProduct->length = NULL;
		$preparedProduct->ean = NULL;
		$preparedProduct->price = NULL;
		$preparedProduct->description = NULL;
		$preparedProduct->image_url = NULL;
		$preparedProduct->category1 = NULL;
		$preparedProduct->brand = NULL;
		//$preparedProduct->mpn = 'WG-'.$product->id;

		// out of stock ??
		$preparedProduct->out_of_stock = false;
		for($i = 0; $i < count($out_of_stock_items); $i++){
			if($out_of_stock_items[$i] == strval($product->getId())){
				$preparedProduct->out_of_stock = true;
				print "found out of stock item\n";
			}
		}
		
		// getting the parent product
		$parentId = $product->loadParentProductIds()->getData('parent_product_ids');
		if(isset($parentId[0]))
		{
			$preparedProduct->parent_product = Mage::getModel('catalog/product')->load($parentId[0]);
			$preparedProduct->parent_product_array = $preparedProduct->parent_product->toArray();
		}
		
		// product_url
		if($preparedProduct->parent_product){
			$preparedProduct->product_url = $preparedProduct->parent_product->getProductUrl(false);
		}else{
			$preparedProduct->product_url = $product->getProductUrl(false);
		}
		
		// product_image
		if(isset($preparedProduct->parent_product_array['image']) 
			&& strlen($preparedProduct->parent_product_array['image']) > 0 
			&& $preparedProduct->parent_product_array['image'] != 'no_selection'){
				$path = Mage::getBaseDir('media').'/catalog/product/'.$preparedProduct->parent_product_array['image'];
				//fwrite($this->export_file, "\n".$path."\n");
				// this sucks so much !
				if(file_exists($path)){
					$preparedProduct->image_url = $preparedProduct->parent_product->getImageUrl();	
				}
		}

		// dimensions
		if(isset($preparedProduct->product_array['component_value_width']) && strlen($preparedProduct->product_array['component_value_width']) > 0){
			$preparedProduct->width = $product->getAttributeText('component_value_width');
		}

		if(isset($preparedProduct->product_array['component_value_length']) && strlen($preparedProduct->product_array['component_value_length']) > 0){
			$preparedProduct->length = $product->getAttributeText('component_value_length');
		}

		// ean
		if (isset($preparedProduct->product_array['a_ean'])){
			$preparedProduct->ean = $preparedProduct->product_array['a_ean'];
		}

		// color
		if (isset($preparedProduct->product_array['component_value_color'])){
			$preparedProduct->color = $product->getAttributeText('component_value_color');
		}

		// description
		if(isset($preparedProduct->product_array['description']) && strlen($preparedProduct->product_array['description']) > 0){
			$description = $preparedProduct->product_array['description'];
		}else{

			if($preparedProduct->parent_product != NULL)
			{
				$preparedProduct->description = $preparedProduct->parent_product->getDescription();
			}else{
				$preparedProduct->description = $preparedProduct->product_array['name'];
			}

		}

		$preparedProduct->description = str_replace("\n","<br/>",$preparedProduct->description);

		// price
		$preparedProduct->price = $preparedProduct->product_array['price'];

		// special price
		$preparedProduct->has_special_price = false;
		if($product->getSpecialPrice() != NULL){
			$preparedProduct->has_special_price = true;
			print 'special price:'.$product->getSpecialPrice()."\n";
			print 'special price from:'.$product->getSpecialFromDate()."\n";
			print 'special price to:'.$product->getSpecialToDate()."\n";
			$preparedProduct->special_price = $product->getSpecialPrice();
			$preparedProduct->special_price_range = pv_util::gm_date($product->getSpecialFromDate())."/".pv_util::gm_date($product->getSpecialToDate());
			print 'special price range:'.$preparedProduct->special_price_range."\n";
		}
		
		// Kategorien
		$cats = pv_util::get_category($product);
		if (count($cats) > 0) {
			$preparedProduct->category1 = $cats[0]->getName();
		}else{
			$cats = pv_util::get_category($preparedProduct->parent_product);
			if (count($cats) > 1) {
				/*
				$x = array();
				for($i = 1; $i < count($cats); $i++){
					array_push($x,$cats[$i]->getName());
				}*
				//array_shift($cats); // eins kürzer machen, von vorne, da steht wohn-guide drin
				$preparedProduct->category1 = implode("/",$x);*/
				$preparedProduct->category1 = str_replace("&","&#x26;",$cats[1]->getName());
			}
		}
				
		// sku
		$preparedProduct->sku = $preparedProduct->product_array['sku'];
		
		//  name
		if($preparedProduct->color != NULL){
			$preparedProduct->name = $preparedProduct->product_array['name']." - ".$preparedProduct->color;
		}else{
			$preparedProduct->name = $preparedProduct->product_array['name'];
		}
		
		// brand
		// custom for bioethanolshop
		try{
			if($preparedProduct->product_array['p_component_value_manufacturer'] != NULL){
				$preparedProduct->brand = $preparedProduct->product_array['p_component_value_manufacturer'];
			}else{
				echo "p_component_value_manufacturer not available for product\n";
				$preparedProduct->brand = "unknown";
			}
		}catch(Exception $e){
			echo "p_component_value_manufacturer not available for product\n";
			$preparedProduct->brand = "Höfer Chemie GmbH";
		}
		echo 'brand: '.$preparedProduct->brand."\n";
		 

		return $preparedProduct;
	}
}
