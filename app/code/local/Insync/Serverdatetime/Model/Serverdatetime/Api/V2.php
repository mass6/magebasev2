<?php
/**
 * Insync Development Team
 * @category Server Date Time
 * @package Insync_Serverdatetime
 * @copyright InSync Tech-Fin Solutions Ltd.
 */
class Insync_Serverdatetime_Model_Serverdatetime_Api_V2 extends Insync_Serverdatetime_Model_Serverdatetime_Api
{
	const DATE_FORMAT = 'm-d-Y';
	const TIME_FORMAT = 'H:i:s';
	
	/**
	 * Retrieve server date & time
	 * 
	 * Insync Development Team
	 * @return array:
	 */
	public function getServerDateTime()
	{
		date_default_timezone_set('UTC');
		$serverDateTime = array();
		$serverDateTimeArray = array();
		$serverDateTime['server_date'] = date(Insync_Serverdatetime_Model_Serverdatetime_Api::DATE_FORMAT);
		$serverDateTime['server_time'] = date(Insync_Serverdatetime_Model_Serverdatetime_Api::TIME_FORMAT);
		$serverDateTimeArray[] = $serverDateTime;
		return $serverDateTimeArray;
	}
}