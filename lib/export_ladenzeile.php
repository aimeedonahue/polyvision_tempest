<?php

require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/pv_util.php');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of export_ladenzeile
 *
 * @author abierbrauer
 */
class export_ladenzeile {

    private $output_directory = NULL;
    private $export_file = null;
    private $export_path = NULL;
	private $num_file = 1;

    public function __construct($directory) {
        $this->output_directory = $directory;
        $this->export_path = $this->output_directory . '/' . 'ladenzeile_'.$this->num_file.'.csv';
        $this->export_file = fopen($this->export_path, "w");
    }

	public function next_file(){
		$this->num_file += 1;
		$this->export_path = $this->output_directory . '/' . 'ladenzeile_'.$this->num_file.'.csv';
        $this->export_file = fopen($this->export_path, "w");
	}
	
    public function close() {
        fclose($this->export_file);
    }

    public function write_header() {
        $line = "Artikelnummer /SKU;Produktname;Hauptkategorie;Unterkategorie;Geschlecht;Farbe;Marke;Material;Beschreibung; Preis ;Alter Preis;Grundpreis;Währung;Verfügbarkeit;Versand Konditionen;Größe;Bild URL;Deep URL;Datum;EAN;Aux Bild URL 1;Aux Bild URL 2;Aux Bild URL 3\n";
        fwrite($this->export_file, $line);
    }

    public function write_row(&$prepared_product,&$product) {

        $gender = NULL;
        $cat_1 = $prepared_product->category1;
        $cat_2 = NULL;
        $color = $prepared_product->color;
        $marke = NULL;
        $material = NULL;
        $description = $prepared_product->description;
        $price = $prepared_product->price;
        $old_price = NULL;
        $base_price = NULL;
        $currency = NULL;
        $availability = NULL;
        $send_conditions = NULL;
        $measure = $prepared_product->width.' '.$prepared_product->length;
        $image_url = $prepared_product->image_url;
        $deep_url =  $prepared_product->product_url;
        $product_date = NULL;
        $ean =  $prepared_product->ean;
        $aux_image_url_1 = NULL;
        $aux_image_url_2 = NULL;
        $aux_image_url_3 = NULL;

        $line = $prepared_product->sku. ';';
        $line = $line . $prepared_product->name . ';';
        $line = $line . $cat_1 . ';';
        $line = $line . $cat_2 . ';';
        $line = $line . $gender . ";";
        $line = $line . $color . ";";
        $line = $line .  "liedeco;";
        $line = $line ."\"". $material . "\";";
        $line = $line ."\"". $description . "\";";
        $line = $line . $price . ";";
        $line = $line . $old_price . ";";
        $line = $line . $base_price . ";";
        $line = $line . $currency . ";";
        $line = $line . $availability . ";";
        $line = $line . $send_conditions . ";";
        $line = $line . $measure . ";";
        $line = $line . $image_url . ";";
        $line = $line . $deep_url . ";";
        $line = $line . $product_date . ";";
        $line = $line . $ean . ";";
        $line = $line . $aux_image_url_1 . ";";
        $line = $line . $aux_image_url_2 . ";";
        $line = $line . $aux_image_url_3 . ";";
        $line = $line . "\n";

        fwrite($this->export_file, $line);
    }

		public function write_footer(){
		}
}

?>
