<?php

class Application_Form_Query extends Zend_Form
{

    public function init()
    {
    	/**
    	 * Create form to query Flickr
    	 */
        $this->setName('query');
        
        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int');
        
        $queryStr = new Zend_Form_Element_Text('queryStr');
        $queryStr->setLabel('')
               ->setRequired(true)
               ->addFilter('StripTags')
               //->addFilter('StringTrim')
               ->addValidator('NotEmpty');
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton')
        	   ->setLabel('Search Flickr');
        
        $this->addElements(array($id, $queryStr, $submit));

    }


}

