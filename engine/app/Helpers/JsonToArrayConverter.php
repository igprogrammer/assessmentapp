<?php


namespace App\Helpers;


class JsonToArrayConverter
{


    public static function arrayToXml($array, $rootElement = null, $xml = null) {
        $_xml = $xml;

        // If there is no Root Element then insert root
        if ($_xml === null) {
            $_xml = new \SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        }

        // Visit all key value pair
        foreach ($array as $k => $v) {

            // If there is nested array then
            if (is_array($v)) {

                // Call function for nested array
                self::arrayToXml($v, $k, $_xml->addChild($k));
                //self::arrayToXml($v, $k, $_xml->addChild(htmlspecialchars($k)));
            }

            else {

                // Simply add child element.
                //$_xml->addChild($k, $v);//Previous
                $_xml->addChild($k, htmlspecialchars($v));
            }
        }

        return $_xml->asXML();
    }


}
