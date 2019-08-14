<?php

class Tentura_Mvideo_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getVideoHtml($productId)
    {

        $editInfo = Mage::getModel('mvideo/mvideoForProducts')->getCollection()->addFieldToFilter('product_id', $productId)->toArray();
        $returnString = '<script src="'.Mage::getBaseUrl('js').'flowplayer-3.1.4.min.js"></script>';
        foreach ($editInfo['items'] as $video){
           if ($video['video_type'] == '1' || $video['video_type'] == '2' || $video['video_type'] == '4'){
               $returnString .= '<object width="350" height="283"><param name="movie" value="http://www.youtube.com/v/'.$video['mvideo_code'].'&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$video['mvideo_code'].'&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="350" height="283"></embed></object>';
           }
           if ($video['video_type'] == '3'){
               $returnString .= $video['html_text'];
           }
           if ($video['video_type'] == '5'){

           $returnString .= '<a
                href="'.str_replace("index.php", "", str_replace("index.php/", "", Mage::getBaseUrl()))."media/video/".$video['file_name'].'"
                style="display:block;width:350px;height:283px;"
                id="player">
            <script language="JavaScript">
                flowplayer("player", "'.Mage::getBaseUrl('js').'flowplayer-3.1.3.swf");
           </script>

           </a>';

           }
         
       }
       return $returnString;

    }
    
}