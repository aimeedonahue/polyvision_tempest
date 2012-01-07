<?php

class Polyvision_Tempest_Block_Adminhtml_View extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
    }
    
    protected function _toHtml()
    {
        
		$exported_files = $this->getTempestExportedFiles();
		
        $html="<div class=\"content-header\">
			<table cellspacing=\"0\">
			<tbody>
			<tr>
			<td style=\"width:50%;\">
			<h3 class=\"icon-head head-products\">polyvision  Tempest - Übersicht</h3>
			</td>
			</tr>
			</tbody>
			</table>
			</div>";
		
		$html .= '<p>go to System - Configuration - Polyvision - Tempest to configure this module</p>';
		
		$text = 'test';
		
		
		$html .= '<div class="grid"><table class="data">';
			$html .= '<thead>';
			$html .= '<tr class="headings">';
				$html .= '<th>filename</th>';
				$html .= '<th>last export date</th>';
				$html .= '<th>filesize</th>';
			$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		for($i = 0; $i < count($exported_files); $i++){
			$cfile = $exported_files[$i];
			
			$html .= '<tr>';
			$html .= '<td><a href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'/'.$cfile->filename.'">'.$cfile->filename.'</a></td>';
			$html .= '<td>'.$cfile->modified_date.'</td>';
			$html .= '<td>'.$cfile->filesize.'</td>';
			$html .= '</tr>';
		}
		
		$html .= '</tbody>';
		$html .= '</table></div>';
		//$html .= '<div id="copy_info" style="margin-top:100px">polyvision Tempest &copy by <a href="http://www.polyvision.org">polyvision UG(haftungsbeschr&auml;nkt)</a> 2011</div>';
		
        return $html;
    }
}
?>
