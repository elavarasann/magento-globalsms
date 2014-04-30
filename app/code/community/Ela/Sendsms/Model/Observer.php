<?php
class Ela_Sendsms_Model_Observer
{
	public function sendSmsOnOrderCreated(Varien_Event_Observer $observer)
	{
		if($this->getHelper()->isOrdersEnabled()) {
			$orders = $observer->getEvent()->getOrderIds();
			$order = Mage::getModel('sales/order')->load($orders['0']);
			if ($order instanceof Mage_Sales_Model_Order) {
				$host = "http://bulksms.vsms.net/";
				$path = "eapi/submission/send_sms/2/2.0";
				$username = $this->getHelper()->getUsername();
				$password = $this->getHelper()->getPassword();
				$smsto = $this->getHelper()->getTelephoneFromOrder($order);
				$smsfrom = $this->getHelper()->getSender();
				$smsmsg = $this->getHelper()->getMessage($order);
				$data  = '?username=' . urlencode($username);
				$data .= '&password=' . urlencode($password);
				$data .= '&message=' . urlencode($smsmsg);
				$data .= '&msisdn=' . urlencode($smsto);
				//$data .= '&smsfrom=' . urlencode($smsfrom);
				
				$url = $host.$path.$data;
				$sendSms = $this->getHelper()->sendSms($url);
				try {
					Mage::getModel('sendsms/sendsms')
						->setOrderId($order->getIncrementId())
						->setFrom($smsfrom)
						->setTo($smsto)
						->setSmsMessage($url)
						->setStatus($sendSms['status'])
						->setStatusMessage($sendSms['status_message'])
						->setCreatedTime(now())
						->save();
				}
                catch (Exception $e) {}
				
				if($this->getHelper()->isOrdersNotify() and $this->getHelper()->getAdminTelephone()) {
					$smsto = $this->getHelper()->getAdminTelephone();
					$smsmsg = Mage::helper('sendsms')->__('A new order has been placed: %s',$order->getIncrementId());
					$data  = '?user=' . urlencode($username);
					$data .= '&password=' . urlencode($password);
					$data .= '&smsto=' . urlencode($smsto);
					$data .= '&smsfrom=' . urlencode($smsfrom);
					$data .= '&smsmsg=' . urlencode($smsmsg);
					$url = $host.$path.$data;
					$sendSms = $this->getHelper()->sendSms($url);
					try {
						Mage::getModel('sendsms/sendsms')
							->setOrderId($order->getIncrementId())
							->setFrom($smsfrom)
							->setTo($smsto)
							->setSmsMessage($smsmsg)
							->setStatus($sendSms['status'])
							->setStatusMessage($sendSms['status_message'])
							->setCreatedTime(now())
							->save();
					}
					catch (Exception $e) {}
				}
			}
		}
	}
	
	public function sendSmsOnOrderHold(Varien_Event_Observer $observer)
	{
		if($this->getHelper()->isOrderHoldEnabled()) {
			$order = $observer->getOrder();
			if ($order instanceof Mage_Sales_Model_Order) {
				if ($order->getState() !== $order->getOrigData('state') && $order->getState() === Mage_Sales_Model_Order::STATE_HOLDED) {
					$host = "http://www.auto-cz.ro/";
					$path = "Ela/sendsms.php";
					$username = $this->getHelper()->getUsername();
					$password = $this->getHelper()->getPassword();
					$smsto = $this->getHelper()->getTelephoneFromOrder($order);
					$smsfrom = $this->getHelper()->getSenderForOrderHold();
					$smsmsg = $this->getHelper()->getMessageForOrderHold($order);
					$data  = '?user=' . urlencode($username);
					$data .= '&password=' . urlencode($password);
					$data .= '&smsto=' . urlencode($smsto);
					$data .= '&smsfrom=' . urlencode($smsfrom);
					$data .= '&smsmsg=' . urlencode($smsmsg);
					$url = $host.$path.$data;
					$sendSms = $this->getHelper()->sendSms($url);
					try {
						Mage::getModel('sendsms/sendsms')
							->setOrderId($order->getIncrementId())
							->setFrom($smsfrom)
							->setTo($smsto)
							->setSmsMessage($smsmsg)
							->setStatus($sendSms['status'])
							->setStatusMessage($sendSms['status_message'])
							->setCreatedTime(now())
							->save();
					}
					catch (Exception $e) {}
				}
			}
		}
	}
	
	public function sendSmsOnOrderUnhold(Varien_Event_Observer $observer)
	{
		if($this->getHelper()->isOrderUnholdEnabled()) {
			$order = $observer->getOrder();
			if ($order instanceof Mage_Sales_Model_Order) {
				if ($order->getState() !== $order->getOrigData('state') && $order->getOrigData('state') === Mage_Sales_Model_Order::STATE_HOLDED) {
					$host = "http://www.auto-cz.ro/";
					$path = "MN/sendsms.php";
					$username = $this->getHelper()->getUsername();
					$password = $this->getHelper()->getPassword();
					$smsto = $this->getHelper()->getTelephoneFromOrder($order);
					$smsfrom = $this->getHelper()->getSenderForOrderUnhold();
					$smsmsg = $this->getHelper()->getMessageForOrderUnhold($order);
					$data  = '?user=' . urlencode($username);
					$data .= '&password=' . urlencode($password);
					$data .= '&smsto=' . urlencode($smsto);
					$data .= '&smsfrom=' . urlencode($smsfrom);
					$data .= '&smsmsg=' . urlencode($smsmsg);
					$url = $host.$path.$data;
					$sendSms = $this->getHelper()->sendSms($url);
					try {
						Mage::getModel('sendsms/sendsms')
							->setOrderId($order->getIncrementId())
							->setFrom($smsfrom)
							->setTo($smsto)
							->setSmsMessage($smsmsg)
							->setStatus($sendSms['status'])
							->setStatusMessage($sendSms['status_message'])
							->setCreatedTime(now())
							->save();
					}
					catch (Exception $e) {}
				}
			}
		}
	}
	
	public function sendSmsOnOrderCanceled(Varien_Event_Observer $observer)
	{
		if($this->getHelper()->isOrderCanceledEnabled()) {
			$order = $observer->getOrder();
			if ($order instanceof Mage_Sales_Model_Order) {
				if ($order->getState() !== $order->getOrigData('state') && $order->getState() === Mage_Sales_Model_Order::STATE_CANCELED) {
					$host = "http://www.auto-cz.ro/";
					$path = "MN/sendsms.php";
					$username = $this->getHelper()->getUsername();
					$password = $this->getHelper()->getPassword();
					$smsto = $this->getHelper()->getTelephoneFromOrder($order);
					$smsfrom = $this->getHelper()->getSenderForOrderCanceled();
					$smsmsg = $this->getHelper()->getMessageForOrderCanceled($order);
					$data  = '?user=' . urlencode($username);
					$data .= '&password=' . urlencode($password);
					$data .= '&smsto=' . urlencode($smsto);
					$data .= '&smsfrom=' . urlencode($smsfrom);
					$data .= '&smsmsg=' . urlencode($smsmsg);
					$url = $host.$path.$data;
					$sendSms = $this->getHelper()->sendSms($url);
					try {
						Mage::getModel('sendsms/sendsms')
							->setOrderId($order->getIncrementId())
							->setFrom($smsfrom)
							->setTo($smsto)
							->setSmsMessage($smsmsg)
							->setStatus($sendSms['status'])
							->setStatusMessage($sendSms['status_message'])
							->setCreatedTime(now())
							->save();
					}
					catch (Exception $e) {}
				}
			}
		}
	}
	
	public function sendSmsOnShipmentCreated(Varien_Event_Observer $observer)
	{
		if($this->getHelper()->isShipmentsEnabled()) {
			$shipment = $observer->getEvent()->getShipment();
			$order = $shipment->getOrder();
			if ($order instanceof Mage_Sales_Model_Order) {
				$host = "http://www.auto-cz.ro/";
				$path = "MN/sendsms.php";
				$username = $this->getHelper()->getUsername();
				$password = $this->getHelper()->getPassword();
				$smsto = $this->getHelper()->getTelephoneFromOrder($order);
				$smsfrom = $this->getHelper()->getSenderForShipment();
				$smsmsg = $this->getHelper()->getMessageForShipment($order);
				$data  = '?user=' . urlencode($username);
				$data .= '&password=' . urlencode($password);
				$data .= '&smsto=' . urlencode($smsto);
				$data .= '&smsfrom=' . urlencode($smsfrom);
				$data .= '&smsmsg=' . urlencode($smsmsg);
				$url = $host.$path.$data;
				$sendSms = $this->getHelper()->sendSms($url);
				try {
					Mage::getModel('sendsms/sendsms')
						->setOrderId($order->getIncrementId())
						->setFrom($smsfrom)
						->setTo($smsto)
						->setSmsMessage($smsmsg)
						->setStatus($sendSms['status'])
						->setStatusMessage($sendSms['status_message'])
						->setCreatedTime(now())
						->save();
				}
                catch (Exception $e) {}
			}
		}
	}

	public function getHelper()
    {
        return Mage::helper('sendsms/Data');
    }
}