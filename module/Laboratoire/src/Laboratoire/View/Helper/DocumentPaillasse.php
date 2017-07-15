<?php
namespace Laboratoire\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZendPdf\PdfDocument;
use ZendPdf\Page;

class DocumentPaillasse extends AbstractHelper{
	
	protected $_pdf;
	
	public function __construct()
	{
		$this->_pdf = new PdfDocument();;
	}
	
	public function addPage(Page $page)
	{
		$this->_pdf->pages[] = $page;
	}
	
	public function getDocument()
	{
		header('Content-Type: application/pdf; charset=UTF-8') ;
		echo $this->_pdf->render();
	}
}