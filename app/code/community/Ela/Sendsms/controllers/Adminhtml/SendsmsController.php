<?php

class Ela_Sendsms_Adminhtml_SendsmsController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('sendsms/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('sendsms/sendsms')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('sendsms_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('sendsms/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('sendsms/adminhtml_sendsms_edit'))
				->_addLeft($this->getLayout()->createBlock('sendsms/adminhtml_sendsms_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sendsms')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
		
			//check if destination input is an order
			$order = Mage::getModel('sales/order')->loadByIncrementId($data['to']);
			if($order instanceof Mage_Sales_Model_Order and $order->getData()) {
				$data['to'] = $this->getHelper()->getTelephoneFromOrder($order);
			}
		
			//send the SMS
			$host = "http://bulksms.vsms.net/";
			$path = "eapi/submission/send_sms/2/2.0";
			$username = $this->getHelper()->getUsername();
			$password = $this->getHelper()->getPassword();
			$smsto = $data['to'];
			$smsfrom = $data['from'];
			$smsmsg = $data['sms_message'];
			$_data  = '?username=' . urlencode($username);
			$_data .= '&password=' . urlencode($password);
			$_data .= '&message=' . urlencode($smsmsg);
			$_data .= '&msisdn=' . urlencode($smsto);
			//$_data .= '&smsfrom=' . urlencode($smsfrom);
			
			$url = $host.$path.$_data;
			//print_r($url);
			//exit;
			$sendSms = $this->getHelper()->sendSms($url);
			$data['status'] = $sendSms['status'];
			$data['status_message'] = $sendSms['status_message'];

			$model = Mage::getModel('sendsms/sendsms');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				$model->setCreatedTime(now());
				$model->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sendsms')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sendsms')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('sendsms/sendsms');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $sendsmsIds = $this->getRequest()->getParam('sendsms');
        if(!is_array($sendsmsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($sendsmsIds as $sendsmsId) {
                    $sendsms = Mage::getModel('sendsms/sendsms')->load($sendsmsId);
                    $sendsms->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($sendsmsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	
	public function getHelper()
    {
        return Mage::helper('sendsms/Data');
    }
}