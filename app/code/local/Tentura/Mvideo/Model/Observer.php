<?php
class Tentura_Mvideo_Model_Observer extends Mage_Core_Model_Abstract
{

	public function productSaveAfter($observer){
	
//		exit;
		$product = $observer->getProduct();
		$data = Mage::app()->getRequest()->getPost();
        
		$mvideoData['product_id']   = $product->getId();
        $mvideoData['video_type']   = Mage::app()->getRequest()->getParam('video_type');

        $uploadFlag = 0;
        if ($mvideoData['video_type'] == '1' && Mage::app()->getRequest()->getParam('mvideo_uploaded_id') != ""){
        	$mvideoData['mvideo_code'] = Mage::app()->getRequest()->getParam('mvideo_uploaded_id');
            $uploadFlag = 1;
        }
        if ($mvideoData['video_type'] == '2' && Mage::app()->getRequest()->getParam('key_text') != ""){
        	$mvideoData['mvideo_code'] = Mage::app()->getRequest()->getParam('key_text');
            $uploadFlag = 1;
        }
        if ($mvideoData['video_type'] == '3' && Mage::app()->getRequest()->getParam('html_text') != ""){
            $mvideoData['html_text'] = Mage::app()->getRequest()->getParam('html_text');
            $uploadFlag = 1;
        }
        if ($mvideoData['video_type'] == '4' && Mage::app()->getRequest()->getParam('already_uploaded')!="0" && Mage::app()->getRequest()->getParam('already_uploaded')!=""){
            $mvideoData['mvideo_code'] = Mage::app()->getRequest()->getParam('already_uploaded');
            $uploadFlag = 1;
        }
        if ($mvideoData['video_type'] == '5' && Mage::app()->getRequest()->getParam('direct_upload_filename')!=""){
            $mvideoData['file_name'] = Mage::app()->getRequest()->getParam('direct_upload_filename');
            $uploadFlag = 1;
        }

        if ($uploadFlag == 1){
	        Mage::getModel('mvideo/mvideoForProducts')->setData($mvideoData)->setId(Mage::app()->getRequest()->getParam('mvideo'))->save();
        }
		
	
	}
    
}