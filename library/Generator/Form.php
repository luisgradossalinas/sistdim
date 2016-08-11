<?php

class Generator_Form extends Zend_Db_Table
{
 
    public function cuerpoFormulario($tabla)
    {
        
        $db = $this->getAdapter();
        $dataTabla = $db->describeTable($tabla);
        
        $cuerpo = '$this->setAttrib(\'id\', \'form\');'. "\n\n";
        
        foreach ($dataTabla as $key => $value) {
            $campo = $key;
            $label = ucfirst($campo);
            $primary = $value['PRIMARY'];
            $identity = $value['IDENTITY'];
            $tipo = $value['DATA_TYPE'];
            $length = $value['LENGTH'];
            $null = $value['NULLABLE'];
            
            if ($primary != 1) {
                $cuerpo .= '$'.$campo.' = new Zend_Form_Element_Text(\''.$campo.'\');'. "\n";
                $cuerpo .= '$'.$campo.'->setLabel(\''.$label.':\');'."\n";
                
                if ($null != 1){
                    $cuerpo .=  '$'.$campo.'->setRequired();'. "\n";
                }
                
                //Tipos de datos Enteros 
                /*tinyint 3
                smallint 5
                mediumint 7
                int 9
                bigint 17*/
                
                if ($tipo == 'tinyint') {
                    $cuerpo .=  '$'.$campo.'->addValidator(new Zend_Validate_Int());'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'maxlength\',3);'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'size\',5);'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'class\',\'v_numeric\');'. "\n";
                    
                } else if ($tipo == 'smallint' ){
                    $cuerpo .=  '$'.$campo.'->addValidator(new Zend_Validate_Int());'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'maxlength\',5);'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'class\',\'v_numeric\');'. "\n";
                    
                } else if ($tipo == 'mediumint' ){
                    $cuerpo .=  '$'.$campo.'->addValidator(new Zend_Validate_Int());'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'maxlength\',7);'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'class\',\'v_numeric\');'. "\n";
                    
                } else if ($tipo == 'int' ){
                    $cuerpo .=  '$'.$campo.'->addValidator(new Zend_Validate_Int());'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'maxlength\',9);'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'class\',\'v_numeric\');'. "\n";
                    
                } else if ($tipo == 'bigint' ){
                    $cuerpo .=  '$'.$campo.'->addValidator(new Zend_Validate_Int());'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'maxlength\',17);'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'class\',\'v_numeric\');'. "\n";
                    
                } else if ($tipo == 'float' ){
                    $cuerpo .=  '$'.$campo.'->addValidator(new Zend_Validate_Float());'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'maxlength\',10);'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'class\',\'v_decimal\');'. "\n";
                    
                } else if ($tipo == 'date' or $tipo == 'datetime' ){
                    $cuerpo .=  '$'.$campo.'->addValidator(new Zend_Validate_Date(\'DD-MM-YYYY\'));'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'maxlength\',10);'. "\n";
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'class\',\'v_datepicker\');'. "\n";
                    
                }
                
                else if ($tipo == 'varchar' || $tipo == 'char') {
                    $cuerpo .=  '$'.$campo.'->setAttrib(\'maxlength\','.$length.');'. "\n";
                }
                
                $cuerpo .= '$'. $campo.'->addFilter(\'StripTags\');'. "\n";
                $cuerpo .= '$this->addElement($'.$campo.');'. "\n\n";
              
            }
        }
        
        return $cuerpo;
    }
    
    public function populate($tabla) {
        
        $db = $this->getAdapter();
        $dataTabla = $db->describeTable($tabla);
        
        $populate = '';
        
        foreach ($dataTabla as $key => $value) {
            $campo = $key;
            //$label = ucfirst($campo);
            $tipo = $value['DATA_TYPE'];
   
                if ($tipo == 'date' or $tipo == 'datetime' ){
                    $populate .=  "    ".'if (isset($data[\''.$campo.'\']) && ($data[\''.$campo.'\'] == App_View_Helper_FechaMostrar::DEFAULT_DATE || $data[\''.$campo.'\'] == App_View_Helper_FechaMostrar::DEFAULT_DATETIME)) {' . "\n";
                    $populate .=  "    ".'unset($data[\''.$campo.'\']);'. "\n";
                    $populate .=  '} else {'. "\n";
                    $populate .=  "    ".'$data[\''.$campo.'\'] = new Zend_Date($data[\''.$campo.'\'],\'yyyy-mm-dd\');'. "\n";
                    $populate .=  "    ".'$data[\''.$campo.'\'] = $data[\''.$campo.'\']->get(\'dd/mm/yyyy\');'. "\n";
                    $populate .=  "    ".'} '. "\n";
                }             
            
        }
        
        $populate .= 'return $this->setDefaults($data);'. "\n";
        
        return $populate;
        
    }

   
}
