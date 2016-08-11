<?php

/**
 * Description of Hace
 *
 * @author eanaya
 * @author fcondori
 */
class App_View_Helper_FriendlyDate extends Zend_View_Helper_Abstract
{
    public function FriendlyDate($diahora, $format = 'YYYY-MM-dd')
    {
        if (empty($diahora)) {
            return '';
        }

        $fh = new Zend_Date($diahora, $format, Zend_Locale::ZFDEFAULT);

        return $diahora == '' ? '' : $fh->get(
            sprintf("%s %s %s", Zend_Date::DAY, Zend_Date::MONTH_NAME_SHORT, Zend_Date::YEAR), 
            new Zend_Locale(Zend_Locale::ZFDEFAULT)
        );
    }

}