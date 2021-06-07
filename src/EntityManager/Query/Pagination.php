<?php
/*
* © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
* pagination class
*/ 
namespace Infinite\DataMapper;
class Pagination {

    public $totalNumOfPages;
    public $totalResults;
    public $resultsPerPage;
    public $pageNo;
    public $offset;


    /**
    * constructor
    * @param Object $props
    * @param function $func
    * @return Object
    */
    public function __construct () { 
    }

    public function initialize ($props,$resultsPerPage,$func) {
        if (!isset($props) || $props == null) {
            $count =  count(
              $func()
            );
            $this->totalResults = $count;           
        }
        else {
            $this->totalResults = $props->result;            
        }        
        $this->resultsPerPage = $resultsPerPage;        
        $this->totalNumOfPages = $this->setTotalNumOfPages();
        $this->pageNo = $props->pageNo ?? 1;
        $this->offset = $this->setOffset();
        return $this;
    }
    

    /**
     * get current page no 
     */
    public function getCurrentPage () {
        return $this->pageNo;
    }

    /**
     * get offest
     *
     * @return number
     */
    public function getOffset () {
        return $this->offset;
    }

    /**
    * set total pages
    * @return Integer
    */
    public function setTotalNumOfPages () {
      return  ceil($this->totalResults / $this->resultsPerPage);        
    }

    public function setOffset () {
        if ($this->pageNo > $this->totalNumOfPages) {
            return false;
        }
        if (($this->pageNo - 1) === 0) {
            $this->offset =  0;
            return $this->offset;
        }
        else {            
            $this->offset = ($this->pageNo - 1) * $this->resultsPerPage;
            return $this->offset;
        }       
        
    }

    public function next () {
        $this->pageNo++;
        return $this->setOffset();
    }
    
    public function prev () {
        $this->pageNo--;
        return $this->setOffset();
    }

    public function getPageResult () {
       return (object) [                
            "result" => $this->totalResults,
            "pages" => $this->totalNumOfPages,
            "pageNo" => $this->getCurrentPage(),            
       ];
    }


    
}
?>