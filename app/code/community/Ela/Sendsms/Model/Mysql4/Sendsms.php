<?php

class Ela_Sendsms_Model_Mysql4_Sendsms extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the sendsms_id refers to the key field in your database table.
        $this->_init('sendsms/sendsms', 'sendsms_id');
    }
}