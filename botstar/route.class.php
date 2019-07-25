<?php 
    /**
     * Принимает запросы к серверу
     */
    namespace botstar;

     class Route {
        // properties
        private $url = '';
        private $clearUrl = '';
        private $defaultPathFlag = false;

        // private methods
        private function clearUrl($url) {
            if($pos = strpos($url, '?')) {
                $url = substr($url, 0, $pos);
            }

            $chr = $url{strlen($url)-1};
            if(($chr == '/') || ($chr == '#')) {
                $url = substr($url, 0, -1);
            }

            return $url;
        }

        // constructor
        function __construct($url = false) {
            if($url) {
				$this->url = $url;
			} else {
				$this->url = $_SERVER['REQUEST_URI'];
			}
            $this->clearUrl = $this->clearUrl($this->url);
            $this->defaultPathFlag = true;
         }

        public function path($path, $callback) {
            $items = explode('/', $path);
            $matches = explode('/', $this->clearUrl);
            $args = array();
            //var_dump($matches);
            foreach($items as $key => $item) {
                //print '<br />- '.$item.': '.$key.' = '.$matches[$key];
                if($item == '*') {
                    array_push($args, $matches[$key]);
                    $items[$key] = $matches[$key];
                } elseif(preg_match('/^\{([A-Za-z][A-Za-z0-9_]{0,31})\}$/', $item, $match)) {
                    //var_dump($match);
                    $match = $match[1];
                    $args[$match] = $matches[$key];
                    $items[$key] = $matches[$key];
                }
            }

            //var_dump($args);
            //var_dump($items);
            if(!empty($args)) $path = implode('/', $items);
            //print '<br />tty: '.$path;
            if($path == $this->clearUrl) {
                $this->defaultPathFlag = false;
                if(is_callable($callback)) {
                    $callback($args);
                } else {
                    throw new Exception('Function in path don\'t Closure');
                    //print 'path err 1';
                }
             }
         }

        public function defaultPath($callback) {
            if(!$this->defaultPathFlag) return;
            if(is_callable($callback)) {
                $callback();
            } else {
                throw new Exception('Function in default don\'t Closure');
                //print 'path err 1';
            }
        }

        public function test() {
            print '<br />URL: '.$this->url;
            print '<br />Clear URL: '.$this->clearUrl;
         }
     }