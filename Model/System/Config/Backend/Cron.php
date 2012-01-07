<?php

class Polyvision_Tempest_Model_System_Config_Backend_Cron extends Mage_Core_Model_Config_Data
{
	/**
	 * @const string CRON_STRING_PATH Config-Pfad unter dem der Cron Ausdruck gespeichert wird (in der Tabelle core_config_data)
	 */
	const CRON_STRING_PATH = 'crontab/jobs/tempest_export/schedule/cron_expr';

	/**
	 * Speichern des konfigurierten Zeitpunktes für den Cron-Job.
	 */
	protected function _afterSave()
	{
		/*
		 * Holen der unter System > Konfiguration eingestellten Parameter
		 */
		$time = $this->getData('groups/general_options/fields/cron_time/value');
		$frequncy = $this->getData('groups/general_options/fields/cron_frequncy/value');

		/*
		 * Aufbau eines Cron-Ausdrucks
		 */
		$frequencyDaily = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
		$frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
		$frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

		$cronExprArray = array(
			intval($time[1]),                                   # Minute
			intval($time[0]),                                   # Stunde
			($frequncy == $frequencyMonthly) ? '1' : '*',       # Tag des Monats
			'*',                                                # Monat des Jahres
			($frequncy == $frequencyWeekly) ? '1' : '*',        # Tag der Woche
		);

		$cronExprString = join(' ', $cronExprArray);
                
		try {
			Mage::getModel('core/config_data')
			->load(self::CRON_STRING_PATH, 'path')
			->setValue($cronExprString)
			->setPath(self::CRON_STRING_PATH)
			->save();
		} catch (Exception $e) {
			throw new Exception(Mage::helper('cron')->__('Unable to save Cron expression'));
		}
	}

}
?>