<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
    	//Connect to Flickr and load to sesion
    	$flickr = new Zend_Service_Flickr('25190f391caa4b90f4e5f4266faa0826');
    	
    	$flickrSesion = new Zend_Session_Namespace('flickrSesion');
    	
    	$flickrSesion->flickr = $flickr;
    }

    /**
     * 
     * Redirects to query form
     */
    public function indexAction()
    {
		$this->_helper->redirector('query');
    }

    /**
     * 
     * Get results, and paginates it
     */
    public function resultAction()
    {
    	$resultsPerPage = 5;
    	
        //Get Flickr handler 
        $flickrSesion = new Zend_Session_Namespace('flickrSesion');
        if(!isset($flickrSesion->flickr))
        {
        	//Error
        }
        
        $flickr = $flickrSesion->flickr;
        
        //First page, coming from query page=1
        $page = $this->_getParam('page', 1);
        
		//Search options
		$options = array(
					    'per_page' => $resultsPerPage, 
					    'page'     => $page,
					    'tag_mode' => 'all',
					    'extras'   => 'license, date_upload, date_taken, owner_name, icon_server'
					    );
		    
		//Get query string from form
		$query = $this->_getParam('query');
		    
		//Lookup using tagSearch
		$results = $flickr->tagSearch(urldecode($query),$options);
    	
		//Pass to paginator total pages
    	$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($results->totalResultsAvailable));
 
    	//Sets the results per page
    	$paginator->setItemCountPerPage($resultsPerPage);
    	
    	//Number of page to show
		$paginator->setCurrentPageNumber($page);
		
		//Saving 5 results on sesion to display the full sized version
		$flickrSesion->results = $results;
		
		$total = $results->totalResultsAvailable;
		
		//Passing variables to view
		$this->view->results = $results;
		$this->view->paginator = $paginator;
		$this->view->columns = $resultsPerPage;
		$this->view->query = $query;
		$this->view->total = $total;
		
    }

    /**
     * 
     * Display or process the form
     */
    public function queryAction()
    {
	    $form = new Application_Form_Query();
	    $form->submit->setLabel('Query');
	    $this->view->form = $form;
	    
	    if ($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->getPost();
	        if ($form->isValid($formData)) {
	            $queryStr = $form->getValue('queryStr');
	            
	            //Redirect to index controller, result action, query param, $queryStr value
	            $this->_redirect("index/result/query/".$queryStr, array('prependBase' => true));
	        }
	        else
	        {
	        	$form->populate($formData);
	        }
	    }
    	else {
	    	$queryStr = $this->_getParam('query');
	    	$form->populate(array('queryStr' => $queryStr));
	    }
    }

    /**
     * 
     * Display full-size image page
     */
    public function fullAction()
    {
        //Get Flickr handler 
        $flickrSesion = new Zend_Session_Namespace('flickrSesion');
        if(!isset($flickrSesion->flickr))
        {
        	//Error
        }
        
        $flickr = $flickrSesion->flickr;
        
        $imgId = $this->_getParam('imgId');
        $ownername = $this->_getParam('ownername');
        $datetaken = $this->_getParam('datetaken');
        
        $result = $flickr->getImageDetails($imgId);
        
        $this->view->result = $result;
        $this->view->ownername = $ownername;
        $this->view->datetaken = $datetaken;
        
        
    }


}



