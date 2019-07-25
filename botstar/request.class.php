<?php 
    /**
     * Обработка запросов
     */
    namespace botstar;
    
    class Request {
        // requires
        static public function requireGet($name) {
            if(!is_array($_GET)) return false;
            if(!isset($_GET[$name])) return false;
            if(empty($_GET[$name])) return false;
            return $name;
        }

        static public function requirePost($name) {
            if(!is_array($_POST)) return false;
            if(!isset($_POST[$name])) return false;
            if(empty($_POST[$name])) return false;
            return $name;
        }

        // public get
        static public function valGetInt($name) {
            if(!self::requireGet($name)) return false;
            return abs((int)$_GET[$name]);
        }

        static public function valGetStr($name) {
            if(!self::requireGet($name)) return false;
            return trim(strip_tags($_GET[$name]));
        }

        static public function valGetHtml($name) {
            if(!self::requireGet($name)) return false;
            return trim(htmlspecialchars($_GET[$name]));
        }

        // public post
        static public function valPostInt($name) {
            if(!self::requirePost($name)) return false;
            return abs((int)$_POST[$name]);
        }

        static public function valPostStr($name) {
            if(!self::requirePost($name)) return false;
            return trim(strip_tags($_POST[$name]));
        }

        static public function valPostHtml($name) {
            if(!self::requirePost($name)) return false;
            return trim(htmlspecialchars($_POST[$name]));
        }
    }