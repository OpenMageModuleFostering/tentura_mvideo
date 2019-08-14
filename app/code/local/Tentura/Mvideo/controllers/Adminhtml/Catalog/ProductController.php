<?php
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class Tentura_Youtube_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    
    /**
     * Save product action
     */
    public function saveAction()
    {
//        var_dump($this->getRequest()->getParam('youtube_uploaded_id'));
//        exit;
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();
        if ($data) {
            if (!isset($data['product']['stock_data']['use_config_manage_stock'])) {
                $data['product']['stock_data']['use_config_manage_stock'] = 0;
            }
            $product = $this->_initProductSave();

            try {
                $product->save();
                $productId = $product->getId();

                $youtubeData['product_id']   = $productId;
                $youtubeData['video_type']   = $this->getRequest()->getParam('video_type');

                $uploadFlag = 0;
                if ($youtubeData['video_type'] == '1' && $this->getRequest()->getParam('youtube_uploaded_id') != ""){
                    $youtubeData['youtube_code'] = $this->getRequest()->getParam('youtube_uploaded_id');
                    $uploadFlag = 1;
                }
                if ($youtubeData['video_type'] == '2' && $this->getRequest()->getParam('key_text') != ""){
                    $youtubeData['youtube_code'] = $this->getRequest()->getParam('key_text');
                    $uploadFlag = 1;
                }
                if ($youtubeData['video_type'] == '3' && $this->getRequest()->getParam('html_text') != ""){
                    $youtubeData['html_text'] = $this->getRequest()->getParam('html_text');
                    $uploadFlag = 1;
                }
                if ($youtubeData['video_type'] == '4' && $this->getRequest()->getParam('already_uploaded')!="0" && $this->getRequest()->getParam('already_uploaded')!=""){
                    $youtubeData['youtube_code'] = $this->getRequest()->getParam('already_uploaded');
                    $uploadFlag = 1;
                }
                if ($youtubeData['video_type'] == '5' && $this->getRequest()->getParam('direct_upload_filename')!=""){
                    $youtubeData['file_name'] = $this->getRequest()->getParam('direct_upload_filename');
                    $uploadFlag = 1;
                }
	
                if ($uploadFlag == 1){
  //                  echo "a";
                	Mage::getModel('youtube/youtubeForProducts')->setData($youtubeData)->setId($this->getRequest()->getParam('youtube'))->save();
                }
//                                exit;

                
                /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newProduct = Mage::getModel('catalog/product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess($this->__('Product was successfully saved.'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            }
            catch (Exception $e) {
//                $this->_getSession()->addException($e, $this->__('Product saving error.'));
                $this->_getSession()->addException($e, $e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $productId,
                '_current'=>true
            ));
        }
        else if($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current'   => true,
                'id'         => $productId,
                'edit'       => $isEdit
            ));
        }
        else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }


}
