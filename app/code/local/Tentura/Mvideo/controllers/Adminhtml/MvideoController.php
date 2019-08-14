<?php

class Tentura_Mvideo_Adminhtml_MvideoController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('mvideo/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
	public function checkStatusAction()
	{
		$yt = $this->mvideoAuth();
		if (isset($_SESSION['mvideo_id'])){
		    $videoIdOfPrivateVideo = $_SESSION['mvideo_id']; 
			$location = 'http://gdata.youtube.com/feeds/api/users/default/uploads/' . $videoIdOfPrivateVideo; 
			$privateEntry = $yt->getVideoEntry(null, $location);
			$videoEntry = $privateEntry;
			$state = $videoEntry->getVideoState();
			if ($state) {
			  echo '<br>Upload status for video is - <b>' . $state->getText() . "</b>\n";
			  } else {
			    echo "Not able to retrieve the video status information yet. " . 
			      "Please try again later.\n";
			}
		}
	}
    public function getFormAction()
    {
        if ($this->getRequest()->getPost('title') == "" || $this->getRequest()->getPost('description') == ""){
            $data['error'] = true;
            $data['mvideo_error'] = "";
            $data['error_text'] = "All fields are reqiured!";
        }else{
  
            try{

                $yt = $this->mvideoAuth();
                $myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();
                $myVideoEntry->setVideoTitle($this->getRequest()->getPost('title'));
                $myVideoEntry->setVideoDescription($this->getRequest()->getPost('description'));
                
                $myVideoEntry->setVideoCategory($this->getRequest()->getPost('mvideo_category'));
    			$myVideoEntry->SetVideoTags($this->getRequest()->getPost('title'));
                $tokenHandlerUrl = 'http://gdata.youtube.com/action/GetUploadToken';
                $tokenArray = $yt->getFormUploadToken($myVideoEntry, $tokenHandlerUrl);
                $tokenValue = $tokenArray['token'];
                $postUrl = $tokenArray['url'];
                $form = "
                    ";

                $data['error'] = false;
                $data['mvideo_error'] = "";
                $_SESSION['video_title'] = $this->getRequest()->getPost('title');
                $data['action'] = $postUrl."?nexturl=".$this->getUrl('mvideo/adminhtml_mvideo')."";
                $data['tokenValue'] = $tokenValue;

            }
            catch(Exception $e){
//                echo $e;
                //echo "Error: " . $exception->getResponse()->getRawBody();
                $data['error'] = true;
//                echo $e;
                $data['mvideo_error'] = "Mvideo error. Try again later. <br> Cases: <br>1. Wrong login or password <br>2. Exceeded number of connections. Please wait.";

//                if ($this->getRequest()->getParam('set') != "" || $this->getRequest()->getParam('type') != "")
//                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mvideo')->__("Authentication with Google failed. You type wrong login or password"));
            }
        }
        echo json_encode($data);
    }
    public function mvideoAuth()
    {
    	require_once 'Zend/Gdata/ClientLogin.php';
		$clientLogin = new Zend_Gdata_ClientLogin();
		$authenticationURL= 'https://www.google.com/youtube/accounts/ClientLogin';
			$httpClient =
			  $clientLogin->getHttpClient(
			              $username = Mage::getStoreConfig("mvideo/contacts/login"),
			              $password = Mage::getStoreConfig("mvideo/contacts/pass"),
			              $service = 'youtube',
			              $client = null,
			              $source = 'MySource', // a short string identifying your application
			              $loginToken = null,
			              $loginCaptcha = null,
			              $authenticationURL);
		
		require_once 'Zend/Gdata/YouTube.php';
		require_once 'Zend/Gdata/YouTube/VideoEntry.php';
		
		$developerKey = 'AI39si4GHy6APDI0Jjr67sWNRImktClpSjv6FR8aQ1nylDHhRrXZffrrrLIEqq2cFu47WJSNbKOxphlzMyli2MgXhF76yZABhg';
		$applicationId = 'V';
		$clientId = 'Mvideo extension for Magento';
		
		return new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
    }
	public function indexAction() {
		
		$yt = $this->mvideoAuth();
		
		$videoIdOfPrivateVideo = $this->getRequest()->getParam('id'); 
		$location = 'http://gdata.youtube.com/feeds/api/users/default/uploads/' . $videoIdOfPrivateVideo; 
		$privateEntry = $yt->getVideoEntry(null, $location);
		$videoEntry = $privateEntry;
		$state = $videoEntry->getVideoState();
		if ($state) {
		  $message = "<input type='hidden' name='mvideo_uploaded_id' value='".$videoEntry->getVideoId()."'>Upload successfully <br> Upload status for video ID " . $videoEntry->getVideoId() . " is " .
		    $state->getName() . "  " . $state->getText() . "";
		  } else {
		    $message = "Not able to retrieve the video status information yet. " .
		      "Please try again later.";
		}
        $data['title'] = $_SESSION['video_title'];
        $data['key'] = $this->getRequest()->getParam('id');

        $model = Mage::getModel('mvideo/mvideo');
			$model->setData($data)
				->setId($this->getRequest()->getParam('vid'));

        $model->save();
        $_SESSION['mvideo_message'] = $message;
		$_SESSION['mvideo_id'] = $this->getRequest()->getParam('id');
		echo "<script>parent.video_result(\"".$message."\");</script>";
	}
    public function delVideoAction()
    {
        Mage::getModel('mvideo/mvideoForProducts')->setId($this->getRequest()->getParam('id'))->delete();
        echo 'success';
    }
	public function directUploadAction()
    {
        //echo "<script>parent.video_result('gut');</script>";
        //var_dump($_FILES);

        if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
		
        	try {
					/* Starting upload */
					$uploader = new Varien_File_Uploader('filename');

					// Any extention would work
                                        $uploader->setAllowedExtensions(array('avi','mpg','flv','iflv', 'mpeg', 'mp4'));
					$uploader->setAllowRenameFiles(true);

					// Set the file upload mode
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);

					// We set media as the upload dir
					$path = Mage::getBaseDir('media')."/video";

					if (!is_dir($path)){
						mkdir($path);
					}
					
                    if (file_exists($path . "/".str_replace(".", "_", $_FILES['filename']['name']).".flv"))
                        $_FILES['filename']['name'] = rand(10000000, 99999999).$_FILES['filename']['name'];

                    $_FILES['filename']['name'] = str_replace(' ', "_", $_FILES['filename']['name']);

                    $filename = $_FILES['filename']['name'];

					$uploader->save($path, $_FILES['filename']['name']);


                
//                   echo $_FILES['filename']['name'];
                    $src = $path."/".$_FILES['filename']['name'];
                    $dest = $path . "/".str_replace(".", "_", $_FILES['filename']['name']).".flv";

                    try{

                        $command = escapeshellcmd("ffmpeg -i $src $dest");
                        $output = shell_exec($command);
                        unlink($path.$filename);
                        if (file_exists($path . "/".str_replace(".", "_", $_FILES['filename']['name']).".flv")){

                            echo "<script>parent.video_result('Successfully upload <input type=\"hidden\" name=\"direct_upload_filename\" value=\"".str_replace(".", "_", $_FILES['filename']['name'])."\">', '');</script>";
                            $_SESSION['direct_filename'] = $_FILES['filename']['name'].".flv";

                        }
                        else{
                            echo "<script>parent.video_result('Error while file upload', 'error');</script>";
                        }
                        
                    }catch(Exception $e){

                        
                        if (file_exists($path . "/".$_FILES['filename']['name'])){

                            echo "<script>parent.video_result('Successfully upload <input type=\"hidden\" name=\"direct_upload_filename\" value=\"".$_FILES['filename']['name']."\">', '');</script>";
                            $_SESSION['direct_filename'] = $_FILES['filename']['name'];

                        }
                        else{
                            echo "<script>parent.video_result('Error while file upload', 'error');</script>";
                        }

                    }
                        
                
                 
				} catch (Exception $e) {
                    echo $e;
                    echo "<script>parent.video_result('Error while file upload', 'error');</script>";
		        }
        }
    }
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('mvideo/mvideo')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('mvideo_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('mvideo/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('mvideo/adminhtml_mvideo_edit'))
				->_addLeft($this->getLayout()->createBlock('mvideo/adminhtml_mvideo_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mvideo')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
				
			$model = Mage::getModel('mvideo/mvideo');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mvideo')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mvideo')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('mvideo/mvideo');
				 
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
        $mvideoIds = $this->getRequest()->getParam('mvideo');
        if(!is_array($mvideoIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($mvideoIds as $mvideoId) {
                    $mvideo = Mage::getModel('mvideo/mvideo')->load($mvideoId);
                    $mvideo->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($mvideoIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $mvideoIds = $this->getRequest()->getParam('mvideo');
        if(!is_array($mvideoIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($mvideoIds as $mvideoId) {
                    $mvideo = Mage::getSingleton('mvideo/mvideo')
                        ->load($mvideoId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($mvideoIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'mvideo.csv';
        $content    = $this->getLayout()->createBlock('mvideo/adminhtml_mvideo_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'mvideo.xml';
        $content    = $this->getLayout()->createBlock('mvideo/adminhtml_mvideo_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
	public function uploadAction()
	{
		
	
		
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
}