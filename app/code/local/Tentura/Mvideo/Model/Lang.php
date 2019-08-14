<?php
class Tentura_Mvideo_Model_Lang
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'en-US', 'label'=>Mage::helper('mvideo')->__('English (United States and Canada)')),
            array('value'=>'zh-TW', 'label'=>Mage::helper('mvideo')->__('Chinese (Traditional)')),
            array('value'=>'cs-CZ', 'label'=>Mage::helper('mvideo')->__('Czech')),
            array('value'=>'nl-NL', 'label'=>Mage::helper('mvideo')->__('Dutch')),
            array('value'=>'en-GB', 'label'=>Mage::helper('mvideo')->__('English (Great Britain, Ireland, Australia and New Zealand)')),
            array('value'=>'fr-FR', 'label'=>Mage::helper('mvideo')->__('French')),
            array('value'=>'de-DE', 'label'=>Mage::helper('mvideo')->__('German')),
            array('value'=>'it-IT', 'label'=>Mage::helper('mvideo')->__('Italian')),
            array('value'=>'ja-JP', 'label'=>Mage::helper('mvideo')->__('Japanese')),
            array('value'=>'ko-KR', 'label'=>Mage::helper('mvideo')->__('Korean')),
            array('value'=>'pl-PL', 'label'=>Mage::helper('mvideo')->__('Polish')),
            array('value'=>'pt-BR', 'label'=>Mage::helper('mvideo')->__('Portuguese (Brazil)')),
            array('value'=>'ru-RU', 'label'=>Mage::helper('mvideo')->__('Russian')),
            array('value'=>'es-ES', 'label'=>Mage::helper('mvideo')->__('Spanish (Spain)')),
            array('value'=>'es-MX', 'label'=>Mage::helper('mvideo')->__('Spanish (Mexico)')),
            array('value'=>'sv-SE', 'label'=>Mage::helper('mvideo')->__('Swedish')),
        );
    }
}