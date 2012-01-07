<?php

require (Mage::getBaseDir('app').'/code/local/Polyvision/Tempest/Model/Observer.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/pv_util.php');

class Polyvision_Tempest_Adminhtml_Tempest_MainController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction()
	{

		// "Fetch" display
		$this->loadLayout();

		$export_directory = Mage::getStoreConfig('tempest_options/general_options/export_directory');

		$exported_files = array();
		if ($handle = opendir($export_directory)) {

			/* This is the correct way to loop over the directory. */
			while (false !== ($file = readdir($handle))) {

				// google merchant
				$r = stripos($file,'googlemerchant_');
				if(gettype($r) != 'boolean'){
					$t = new StdClass();
					$t->filename = $file;
					$t->modified_date = date ("F d Y H:i:s.", filemtime($export_directory.'/'.$file));
					$t->filesize = pv_util::format_bytes(filesize($export_directory.'/'.$file));
					array_push($exported_files,$t);
				}

				// ladenzeile

				$r = stripos($file,'ladenzeile_');
				if(gettype($r) != 'boolean'){
					$t = new StdClass();
					$t->filename = $file;
					$t->modified_date = date ("F d Y H:i:s.", filemtime($export_directory.'/'.$file));
					$t->filesize = pv_util::format_bytes(filesize($export_directory.'/'.$file));
					array_push($exported_files,$t);
				}
			}
			closedir($handle);
		}

		$block = $this->getLayout()->createBlock('tempest/adminhtml_view');
		$block->setTempestExportedFiles($exported_files);
		$this->_addContent($block);

		// "Output" display
		$this->renderLayout();
	}

	public function exportAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}