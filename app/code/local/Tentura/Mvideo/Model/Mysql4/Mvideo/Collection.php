<?php

class Tentura_Mvideo_Model_Mysql4_Mvideo_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mvideo/mvideo');
    }
}