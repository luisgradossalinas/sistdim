<?php


/**
 * Description of Util
 *
 * @author svaisman
 */
class App_View_Helper_AvisosBlog extends Zend_View_Helper_HtmlElement
{

    public function AvisosBlog($numero)
    {
        $config = Zend_Registry::get('config');
        if (isset($config->app->debug)) {
            return null;
        }

        $cache = Zend_Registry::get('cache');
        $cacheId = $numero . __FUNCTION__;
        if ($cache->test($cacheId)) {
            return $cache->load($cacheId);
        }

        $url = $config->urlsExternas->empresa->blogPortada->url;
        $channels = new Zend_Feed_Rss($url);
        $arrayRss = NULL;
        $i = 0;
        foreach ($channels as $item) {
            if ($i > $numero) break;
            $arrayRss[$i][0] = $item->pubDate();
            $arrayRss[$i][1] = $item->link();
            $arrayRss[$i][2] = $item->title();
            $i++;
        }
        $cache->save($arrayRss, $cacheId, array(), 3600);

        return $arrayRss;
    }

}