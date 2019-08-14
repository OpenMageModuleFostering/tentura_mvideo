<?php

class Tentura_Mvideo_Block_Adminhtml_Catalog_Product_Edit_Action_Attribute_Tab_Attributes
    extends Mage_Adminhtml_Block_Catalog_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	
	
    public function getCategoryList()
    {


        if (Mage::getStoreConfig("mvideo/contacts/languages")!="")
            $catURL = 'http://gdata.youtube.com/schemas/2007/categories.cat?hl='.Mage::getStoreConfig("mvideo/contacts/languages");
        else
            $catURL = 'http://gdata.youtube.com/schemas/2007/categories.cat';

        $cxml = simplexml_load_file($catURL);
        $cxml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
        $categories = $cxml->xpath('//atom:category');

        $select = "<select id='mvideo_category' style='width:200px' name='mvideo_category'>";
        foreach ($categories as $cat){
            $select .= "<option value='".$cat['term']."'>".$cat['label']."</option>";
        }
        $select .= "</select>";
        
        return $select;
        
    }
    protected function _construct()
    {
        $template = $this->setTemplate("mvideo/upload.phtml");
        $template->videos = Mage::getModel('mvideo/mvideo')->getCollection()->toArray();
        $_SESSION['mvideo_id']      = "";
        $_SESSION['direct_filename'] = "";

        
        if ($this->getRequest()->getParam('id') > 0){

            $template->editInfo = Mage::getModel('mvideo/mvideoForProducts')->getCollection()->addFieldToFilter('product_id', $this->getRequest()->getParam('id'))->toArray();
//           $template->editInfo = array();
           // var_dump($template->editInfo);
            
        }else
            $template->editInfo = array();

        parent::_construct();
        $this->setShowGlobalIcon(true);

    }
    
 	protected function _prepareLayout()
    {
        

        return parent::_prepareLayout();
    }
   
    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('catalog')->__('Video');
    }

    public function getTabTitle()
    {
        return Mage::helper('catalog')->__('Video');
    }

    public function canShowTab()
    {
    	if ($this->getRequest()->getParam('set') != "" || $this->getRequest()->getParam('type') != "" || $this->getRequest()->getParam('id') != "")
       	 	return true;
       	else 
       		return false;  
    }

    public function isHidden()
    {
        return false;
    }
}
