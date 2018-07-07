<?php

class Vijaystore_Emailnotify_Adminhtml_EmailnotifyController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
		$this->loadLayout()->_setActiveMenu('emailnotify/items')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Contact Form Fields '), Mage::helper('adminhtml')->__('Email Notify'));
		return $this;
    }  
	
   //** Email Notify Grid
    public function indexAction() 
	{
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('emailnotify/adminhtml_emailnotify'));
        $this->renderLayout();
    }
   //** Re-Send Email Action
    public function resendAction() 
	{
		$emailnotify_id = $this->getRequest()->getParam('id');
		if( $emailnotify_id > 0 ) {
			$sent_notify = Mage::getModel('emailnotify/notify')->resend_mail($emailnotify_id);
			if($sent_notify) {
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Successfully Sent Mail'));
			}
			else {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Mail Not Send Check the Stock'));
			}
			
			$this->_redirect('*/*/index');
		}
	}
	
   //** Re-Send Email Action
    public function massResendAction() 
	{
		$emailnotify_ids = $this->getRequest()->getParam('emailnotify');
		$notify_succ = array();
		foreach($emailnotify_ids as $key => $emailnotify_id)
		{
			$sent_notify = Mage::getModel('emailnotify/notify')->resend_mail($emailnotify_id);
			if($sent_notify) {
				$notify_succ[] = $emailnotify_id;
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(
			Mage::helper('adminhtml')->__('Total of %d record(s) were successfully Sent Mail', count($notify_succ)));
		$this->_redirect('*/*/index');
    }
		
	//** Email Notify View Record.
	public function viewAction()
	{
		$emailnotifyId     = $this->getRequest()->getParam('id');
		$emailnotifyModel  = Mage::getModel('emailnotify/emailnotify')->load($emailnotifyId); 
        if ($emailnotifyModel->getId() || $emailnotifyId == 0) 
		{
             Mage::register('emailnotify_data', $emailnotifyModel); 
            $this->loadLayout();
            $this->_setActiveMenu('emailnotify/items');           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Contact Record Manager'), Mage::helper('adminhtml')->__('Record Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Record Details'), Mage::helper('adminhtml')->__('Record Details'));           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);           
            $this->_addContent($this->getLayout()->createBlock('emailnotify/adminhtml_emailnotify_edit'));
            $this->renderLayout();
        }
		else 
		{
            Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('emailnotify')->__('Record does not exist')
			);
            $this->_redirect('*/*/');
        }
	}
	
	//** Email Notify Delete Record
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try
			{
				$model = Mage::getModel('emailnotify/emailnotify');				 
				$model->setId($this->getRequest()->getParam('id'))->delete();					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Record was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/view', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	/**
     * Email Notify Mass Delete Record.
     */
    public function massDeleteAction() 
	{
        $emailnotifys = $this->getRequest()->getParam('emailnotify');
        if(!is_array($emailnotifys)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } 
		else 
		{
            try 
			{
                foreach ($emailnotifys as $emailnotifyId) 
				{
                    $emailNotify_del = Mage::getModel('emailnotify/emailnotify')->load($emailnotifyId);
                    $emailNotify_del->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($emailnotifys)
                    )
                );
            } 
			catch (Exception $error) 
			{
                Mage::getSingleton('adminhtml/session')->addError($error->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	/**
     * Email Notify grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('emailnotify/adminhtml_emailnotify_grid')->toHtml()
        );
    }
	
	//** Export in CSV
	public function exportCsvAction()
    {
		$fileName   = 'email_notify.csv';
		$content    = $this->getLayout()->createBlock('emailnotify/adminhtml_emailnotify_grid')->getCsv(); 
		$this->_prepareDownloadResponse($fileName, $content);
    }
	
	//** Export in XML
    public function exportXmlAction()
    {
        $fileName   = 'email_notify.xml';
        $content    = $this->getLayout()->createBlock('emailnotify/adminhtml_emailnotify_grid')->getXml(); 
        $this->_prepareDownloadResponse($fileName, $content);
    }
	
	//** Export in XLS
    public function exportXlsAction()
	{
		$fileName   = "email_notify.xls";
		$worksheet_name = "Email Notify";
		$content    = $this->getLayout()->createBlock('emailnotify/adminhtml_emailnotify_grid')->getXL();
		
		include Mage::getBaseDir("lib") . DS . "xl-reader" . DS . "PHPExcel.php";
		$objPHPExcel = new PHPExcel();
		$rowCount = 1;  		
		foreach($content as $value){
			$column = 'A';
			foreach($value as $val){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($column.$rowCount, $val);
				$column++;
			}
			$rowCount++;
		}
		$objPHPExcel->getActiveSheet()->setTitle($worksheet_name);
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel'); 
		header("Content-Disposition: attachment;filename=$fileName"); 
		header('Cache-Control: max-age=0');			
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
	}
	
	//** Export in XLSX
    public function exportXlsxAction()
    {
		$fileName   = 'email_notify.xlsx';
		$worksheet_name = "Email Notify";
		$content    = $this->getLayout()->createBlock('emailnotify/adminhtml_emailnotify_grid')->getXL();
		// include Mage::getBaseDir("lib") . DS . "xl-reader" . DS . "PHPExcel.php";
		// $objPHPExcel = new PHPExcel();
	    
	    	$includePath = Mage::getBaseDir("lib") . DS . "xl-reader";
		set_include_path(get_include_path() . PS . $includePath); 
		$objPHPExcel = new PHPExcel();
	    
		$rowCount = 1;  		
		foreach($content as $value){
			$column = 'A';
			foreach($value as $val){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($column.$rowCount, $val);
				$column++;
			}
			$rowCount++;
		}
		$objPHPExcel->getActiveSheet()->setTitle($worksheet_name);
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=$fileName");
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
    }
	
	public function exportExcelAction()  
	{
		$fileName   = 'email_notify.xls';
		$content    = $this->getLayout()->createBlock('emailnotify/adminhtml_emailnotify_grid')->getExcelFile();
		//$content    = $this->getLayout()->createBlock('emailnotify/adminhtml_emailnotify_grid')->getExcel();
		$this->_prepareDownloadResponse($fileName, $content);  
	}
}
