<?php

class Vijaystore_Emailnotify_Block_Adminhtml_Emailnotify_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('emailnotifyGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('emailnotify/emailnotify')->getCollection();
        $this->setCollection($collection);		
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('emailnotify')->__('ID'),
            'align'     => 'right',
			'type'		=> 'number',
            'index'     => 'id',
        ));
		
        $this->addColumn('product_id', array(
            'header'    => Mage::helper('emailnotify')->__('Product Id'),
            'align'     => 'right',
			'width'		=> '120px',
            'index'     => 'product_id',
        )); 
 
        $this->addColumn('product_name', array(
            'header'    => Mage::helper('emailnotify')->__('Product Name'),
            'align'     => 'left',
			'width'		=> '200px',
            'index'     => 'product_id',
			'filter' 	=> false,
			'sortable'  => false,
			'renderer'	=> 'Vijaystore_Emailnotify_Block_Adminhtml_Renderer_Productname'
        ));
		
        $this->addColumn('product_sku', array(
            'header'    => Mage::helper('emailnotify')->__('Product SKU'),
            'align'     => 'left',
			'width'		=> '120px',
            'index'     => 'product_id',
			'filter' 	=> false,
			'sortable'  => false,
			'renderer'	=> 'Vijaystore_Emailnotify_Block_Adminhtml_Renderer_Productsku'
        ));
		
		$this->addColumn('email', array(
			'header'    => Mage::helper('emailnotify')->__('Email'),
			'width'     => '150',
			'index'     => 'email'
		));
 
        $this->addColumn('send_email', array(
            'header'    => Mage::helper('emailnotify')->__('Send Email'),
            'align'     => 'left',
			'width'		=> '100px',
            'index'     => 'send_email',
			'type'      => 'options',
			'options' 	=> $this->send_mail_options(),
        )); 
 
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('emailnotify')->__('Created At'),
            'index' 	=> 'created_at',
            'type' 		=> 'datetime',
            'width' 	=> '150px',
        ));
		
 
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('emailnotify')->__('Send Mail At'),
            'index' 	=> 'updated_at',
            'type'		=> 'datetime',
			//'default'   => '--',
            'width' 	=> '150px',
			'renderer'	=> 'Vijaystore_Emailnotify_Block_Adminhtml_Renderer_Sendmailat'
        ));
	
		$this->addColumn('action', array(
			'header'	=> Mage::helper('emailnotify')->__('Action'),
			'width'		=> '100px',
			'type'		=> 'action',
			'getter'	=> 'getId',
			'actions'	=> array(
				array(
					'caption' => Mage::helper('emailnotify')->__('Resend Mail'),
					'url'     => array(
						'base'=>'*/*/resend',
						'params'=>array('store'=>$this->getRequest()->getParam('store'))
					),
					'field'   => 'id'
				)
			),
			'filter'    => false,
			'sortable'  => false,
			'index'     => 'stores',
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('emailnotify')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('emailnotify')->__('XML'));
		$this->addExportType('*/*/exportXls', Mage::helper('emailnotify')->__('XLS'));
		$this->addExportType('*/*/exportXlsx', Mage::helper('emailnotify')->__('XLSX'));
		$this->addExportType('*/*/exportExcel', Mage::helper('emailnotify')->__('Excel'));
		
        return parent::_prepareColumns();
    }
	
	public function send_mail_options() {
		return array(0 => "Not Send", 1 => "Sent" );
	}
	
	// public function customer_active_options() {
		// return array(0 => "Not Active", 1 => "Active" );
	// }
	
	
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('emailnotify');
		
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('emailnotify')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('emailnotify')->__('Are you sure Want to Delete?')
        ));
		
        $this->getMassactionBlock()->addItem('resend', array(
             'label'    => Mage::helper('emailnotify')->__('Resend Email'),
             'url'      => $this->getUrl('*/*/massResend'),
             'confirm'  => Mage::helper('emailnotify')->__('Are you sure Want to Resend Mail?')
        ));
        return $this;
    }
	
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
      return $this->getUrl('*/*/grid', array('_current'=>true));
    }

	public function getXL()
	{
		$this->_isExport = true;
		$this->_prepareGrid();
		$this->getCollection()->getSelect()->limit();
		$this->getCollection()->setPageSize(0);
		$this->getCollection()->load();
		$this->_afterLoadCollection();
		
		$data = array();
		$xl_data = array();
		foreach ($this->_columns as $column) {
			if (!$column->getIsSystem()) {
				 $data[] = ''.$column->getExportHeader().'';
			}
		}
		// $xl.= implode(' \t ', $data)."\n";
		$xl_data[] = $data;
		
		foreach ($this->getCollection() as $item) {
			$data = array();
			foreach ($this->_columns as $column) {
				if (!$column->getIsSystem()) {
					$data[] = $column->getRowFieldExport($item);
				}
			}
			// $xl.= implode(' \t ', $data)." \n ";
			$xl_data[] = $data;
		}
		
		if ($this->getCountTotals())
		{
			$data = array();
			foreach ($this->_columns as $column) {
				if (!$column->getIsSystem()) {
					$data[] = $column->getRowFieldExport($this->getTotals());
				}
			}
			// $xl.= implode('\t', $data)."\n";
		}
		return $xl_data;
	}
}