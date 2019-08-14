<?php

class Tentura_Mvideo_Model_MvideoForProducts extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mvideo/mvideoForProducts');
    }
}