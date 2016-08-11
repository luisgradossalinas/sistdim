<?php

/**
 * Description of Hace
 *
 * @author eanaya
 */
class App_View_Helper_CustomFormatDate extends Zend_View_Helper_Abstract
{
    public function CustomFormatDate($diahora, $formatOutput = 'YYYY-MM-dd', $formatInput = 'YYYY-MM-dd')
    {
        if (empty($diahora)) {
            return '';
        }

        $fh = new Zend_Date($diahora, $formatInput, Zend_Locale::ZFDEFAULT);

        return $fh->get($formatOutput, new Zend_Locale(Zend_Locale::ZFDEFAULT));
    }

    public function AddDate($diahora, $value, $unit, $formatInput = 'YYYY-MM-dd')
    {
        if (empty($diahora)) {
            return '';
        }
        $fh = new Zend_Date($diahora, $formatInput, Zend_Locale::ZFDEFAULT);
        switch ($unit) {
            case Zend_Date::DAY:
                $fh->addDate($value, Zend_Locale::ZFDEFAULT);
                break;
            case Zend_Date::HOUR:
                $fh->addHour($value, Zend_Locale::ZFDEFAULT);
                break;
        }
        return $fh->get('YYYY-MM-dd H:i:s', new Zend_Locale(Zend_Locale::ZFDEFAULT));
    }

}