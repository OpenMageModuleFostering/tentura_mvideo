<?php

class Tentura_Mvideo_Model_Mysql4_Mvideo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the mvideo_id refers to the key field in your database table.
        $this->_init('mvideo/mvideo', 'mvideo_id');
    }
}