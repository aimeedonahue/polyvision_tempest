<?php

class Polyvision_Tempest_Block_Adminhtml_Export extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
    }
    
    protected function _toHtml()
    {
        
		
		
        $html="<div class=\"content-header\">
			<table cellspacing=\"0\">
			<tbody>
			<tr>
			<td style=\"width:50%;\">
			<h3 class=\"icon-head head-products\">polyvision Tempest - manueller Export</h3>
			</td>
			</tr>
			</tbody>
			</table>
			</div>";
		
		$html .= '<p>export finished</p>';
		
		//$html .= '<div id="copy_info" style="margin-top:100px">polyvision Tempest &copy by <a href="http://www.polyvision.org">polyvision UG(haftungsbeschr&auml;nkt)</a> 2011</div>';
		
        return $html;
    }
}
?>
