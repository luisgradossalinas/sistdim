<?php

/**
 * Description of Hace
 *
 * @author fcondori
 */
class App_View_Helper_FechaRangoPublicacionWeb extends Zend_View_Helper_Abstract
{
    public function FechaRangoPublicacionWeb($diahora, $value, $unit, $formatInput = 'YYYY-MM-dd')
    {
        if (empty($diahora)) {
            return '';
        }
        $fh = new Zend_Date($diahora, $formatInput, Zend_Locale::ZFDEFAULT);
        $output = $fh->get("d 'de' ") . ucfirst($fh->get("MMMM"));
        switch ($unit) {
            case Zend_Date::DAY:
                $fh->addDate($value);
            case Zend_Date::HOUR:
                $fh->addHour($value);
        }
        $output .= " al " . $fh->get(" d 'de' ") . ucfirst($fh->get("MMMM 'de' YYYY"));
        return $output;
    }

}