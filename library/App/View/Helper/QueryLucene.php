<?php

/**
 * Description of Util
 *
 * @author svaisman
 */
class App_View_Helper_QueryLucene extends Zend_View_Helper_HtmlElement
{
    // las palabras de 2 caracteres ya son filtradas
    protected $_denyWordsList = array(
        'para', 'los', 'las', 'por'
    );

    public function QueryLucene($query)
    {
        $luceneQuery = "";
        
//        if ($this->HasSpecialChars($query)) {
//           return $query;
//        }
        
        $query = trim($query);
        if (mb_substr($query, 0, 1) == '"' && mb_substr($query, mb_strlen($query) - 1, 1) == '"' ) {
            return $query;
        }
        
        $f = new Zend_Filter_Alnum(true);
        $query = $f->filter($query);
        //var_dump($query);
        //$query = utf8_decode($query);  
        //var_dump($query);
        //$query = utf8_encode($query);
        
        $palabras = explode(" ", $query);
        //var_dump($palabras);
        //
        
        for ($i = 0; $i < count($palabras); $i++) {
            if ($palabras[$i] != null 
                && mb_strlen($palabras[$i]) > 2 
                && !$this->SeDeniega($palabras[$i])
            ) {
                if (mb_strlen($palabras[$i]) >= 3) {
                    $luceneQuery .= $palabras[$i]."* OR ";
                } else {
                    $luceneQuery .= $palabras[$i]." OR ";
                }
            }
        }
        //var_dump($luceneQuery);
        
        if ($luceneQuery != "") {
           $luceneQuery = substr($luceneQuery, 0, strlen($luceneQuery) - 4);
        }
        //var_dump($luceneQuery);
        return $luceneQuery;
    }
    
    protected $_specialSearchChars = '"';
    
    public function SeDeniega($cadena)
    {
        for ($i = 0 ; $i < count($this->_denyWordsList); $i++) {
            if (mb_strtolower($cadena) == strtolower($this->_denyWordsList[$i])) {
                return true;
            }
        }
        
        return false;
    }
}