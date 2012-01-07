<?php

require (Mage::getBaseDir('app').'/code/local/Polyvision/Tempest/Model/Observer.php');
require_once(getcwd() . '/app/code/local/Polyvision/Tempest/lib/pv_util.php');

class Polyvision_Tempest_Adminhtml_Tempest_ExportController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
		
    	// "Fetch" display
        $this->loadLayout();
        
		$block = $this->getLayout()->createBlock('tempest/adminhtml_export');
        $this->_addContent($block);
        
		$ob = new Polyvision_Tempest_Model_Observer();
		$ob->scheduledExport(NULL);
		
        // "Output" display
        $this->renderLayout();
    }
	
	public function exportAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}