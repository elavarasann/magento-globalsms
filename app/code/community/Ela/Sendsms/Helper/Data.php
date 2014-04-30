<?php

class Ela_Sendsms_Helper_Data extends Mage_Core_Helper_Abstract
{
	const CONFIG_PATH = 'sendsms/';
	
	public function isOrdersEnabled()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'orders/enabled');
    }
	
	public function isOrderHoldEnabled()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'order_hold/enabled');
    }
	
	public function isOrderUnholdEnabled()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'order_unhold/enabled');
    }
	
	public function isOrderCanceledEnabled()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'order_canceled/enabled');
    }
	
	public function isShipmentsEnabled()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'shipments/enabled');
    }
	
	public function getUsername()
	{
		return Mage::getStoreConfig(self::CONFIG_PATH.'general/username');
	}
	
	public function getPassword()
	{
		return Mage::getStoreConfig(self::CONFIG_PATH.'general/password');
	}
	
	public function getSender()
	{
		return Mage::getStoreConfig(self::CONFIG_PATH.'orders/sender');
	}
	
	public function getSenderForOrderHold()
	{
		return Mage::getStoreConfig(self::CONFIG_PATH.'order_hold/sender');
	}
	
	public function getSenderForOrderUnhold()
	{
		return Mage::getStoreConfig(self::CONFIG_PATH.'order_unhold/sender');
	}
	
	public function getSenderForOrderCanceled()
	{
		return Mage::getStoreConfig(self::CONFIG_PATH.'order_canceled/sender');
	}
	
	public function getSenderForShipment()
	{
		return Mage::getStoreConfig(self::CONFIG_PATH.'shipments/sender');
	}
	
	public function getMessage(Mage_Sales_Model_Order $order)
	{
		$billingAddress = $order->getBillingAddress();
		$whatArray = array('{{firstname}}');
		$withWhatArray = array($billingAddress->getFirstname());
		
		return str_replace($whatArray,$withWhatArray,Mage::getStoreConfig(self::CONFIG_PATH.'orders/message'));
	}
	
	public function getMessageForOrderHold(Mage_Sales_Model_Order $order)
	{
		$billingAddress = $order->getBillingAddress();
		$whatArray = array('{{firstname}}','{{order_id}}');
		$withWhatArray = array($billingAddress->getFirstname(),$order->getIncrementId());
		
		return str_replace($whatArray,$withWhatArray,Mage::getStoreConfig(self::CONFIG_PATH.'order_hold/message'));
	}
	
	public function getMessageForOrderUnhold(Mage_Sales_Model_Order $order)
	{
		$billingAddress = $order->getBillingAddress();
		$whatArray = array('{{firstname}}','{{order_id}}');
		$withWhatArray = array($billingAddress->getFirstname(),$order->getIncrementId());
		
		return str_replace($whatArray,$withWhatArray,Mage::getStoreConfig(self::CONFIG_PATH.'order_unhold/message'));
	}
	
	public function getMessageForOrderCanceled(Mage_Sales_Model_Order $order)
	{
		$billingAddress = $order->getBillingAddress();
		$whatArray = array('{{firstname}}','{{order_id}}');
		$withWhatArray = array($billingAddress->getFirstname(),$order->getIncrementId());
		
		return str_replace($whatArray,$withWhatArray,Mage::getStoreConfig(self::CONFIG_PATH.'order_canceled/message'));
	}
	
	public function getMessageForShipment(Mage_Sales_Model_Order $order)
	{
		$billingAddress = $order->getBillingAddress();
		$whatArray = array('{{firstname}}','{{order_id}}');
		$withWhatArray = array($billingAddress->getFirstname(),$order->getIncrementId());
		
		return str_replace($whatArray,$withWhatArray,Mage::getStoreConfig(self::CONFIG_PATH.'shipments/message'));
	}
	
	public function getTelephoneFromOrder(Mage_Sales_Model_Order $order)
    {
        $billingAddress = $order->getBillingAddress();

        $number = $billingAddress->getTelephone();
        $number = preg_replace('#[^\+\d]#', '', trim($number));

        if (substr($number, 0, 1) === '+') {
            $number = substr($number, 1);
        } elseif (substr($number, 0, 2) === '00') {
            $number = substr($number, 2);
        } else {
            if (substr($number, 0, 1) === '0') {
                $number = substr($number, 1);
            }
            $expectedPrefix = Zend_Locale_Data::getContent(Mage::app()->getLocale()->getLocale(), 'phonetoterritory', $billingAddress->getCountry());
            if (!empty($expectedPrefix)) {
                $prefix = substr($number, 0, strlen($expectedPrefix));
                if ($prefix !== $expectedPrefix) {
                    $number = $expectedPrefix . $number;
                }
            }
        }
        $number = preg_replace('#[^\d]#', '', trim($number));

        return $number;
    }
	
	public function isOrdersNotify()
    {
		return Mage::getStoreConfig(self::CONFIG_PATH.'orders/notify');
    }
	
	public function getAdminTelephone()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH.'orders/receiver');
    }
		
	public function sendSms($url) {
		try {
			$sendSms = $this->file_get_contents_curl($url);
		}
		catch(Exception $e) {
			$sendSms = '';
		}
		//print_r($sendSms);
		//exit;
		$sendSms = explode("|", $sendSms);
		//echo $sendSms[0];
		//exit;
		$sendSms = $sendSms[0];
		if($sendSms) {
			switch($sendSms) {
				case '23':
					$status_message = Mage::helper('sendsms')->__('Username or password incorrect.');
					$status = Mage::helper('sendsms')->__('Not sent');
					break;
				case '24':
					$status_message = Mage::helper('sendsms')->__('Receiver no missing.');
					$status = Mage::helper('sendsms')->__('Not sent');
					break;
					/*
				case '03':
					$status_message = Mage::helper('sendsms')->__('SmsFrom missing.');
					$status = Mage::helper('sendsms')->__('Not sent');
					break;
				case '04':
					$status_message = Mage::helper('sendsms')->__('SmsMsg missing.');
					$status = Mage::helper('sendsms')->__('Not sent');
					break;
					*/
				case '25':
					$status_message = Mage::helper('sendsms')->__('Insufficient credits.');
					$status = Mage::helper('sendsms')->__('Not sent');
					break;
				default:
					$status_message = Mage::helper('sendsms')->__('Sms successfully sent.');
					$status = Mage::helper('sendsms')->__('Sent');
					break;
			}
		}
		else {
			$status_message = Mage::helper('sendsms')->__('Not able to send the sms. Please contact the developer.');
			$status = 'Not sent';
		}
		$ret['status_message'] = $status_message;
		$ret['status'] = $status;
		return $ret;
	}
	
	public function file_get_contents_curl($url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url);

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}
}