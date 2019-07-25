<?php 
    /**
     * Конфигурация
     */
    namespace botstar;
    
    class Config {
        // response
        private $encoding           =   ''; // Отправляет header при измениении значения: должно устанавливаться до любого вывода
        private $timeZone           =   '';
        private $contentType        =   '';
        // pdo
        private $pdoDriver          =   '';
        private $pdoHost            =   '';
        private $pdoPort            =   '';
        private $pdoCharset         =   '';
        private $pdoUser            =   '';
        private $pdoPassword        =   '';
        private $pdoSchema          =   '';
        // logs
        private $logsDir            =   '';

        public function __get($name) {
            switch($name) {
                case "dsn":
                    if($this->pdoDriver == "mysql") { // mysql
                        if(empty($this->pdoHost)) {
                            $this->pdoHost = "localhost"; 
                        }
                        if(empty($this->pdoPort)) {
                            $this->pdoPort = "3306"; 
                        }
                        if(empty($this->pdoCharset)) {
                            $this->pdoCharset = "utf8"; 
                        }
                        if(empty($this->pdoSchema)) {
                            $this->pdoSchema = "test"; 
                        }
                        return 'mysql:host='.$this->pdoHost.';port='.$this->pdoPort.';dbname='.$this->pdoSchema.';charset='.$this->pdoCharset;
                    } else if($this->pdoDriver == "pgsql") { // pgsql
                        if(empty($this->pdoHost)) {
                            $this->pdoHost = "localhost"; 
                        }
                        if(empty($this->pdoPort)) {
                            $this->pdoPort = "5432"; 
                        }
                        if(empty($this->pdoSchema)) {
                            $this->pdoSchema = "test"; 
                        }
                        return 'pgsql:host='.$this->pdoHost.';port='.$this->pdoPort.';dbname='.$this->pdoSchema;
                    } else if($this->pdoDriver == "sqlite") { // sqlite
                        if(empty($this->pdoDBFile)) {
                            $this->pdoDBFile = 'sqlite.db';
                        }
                        return 'sqlite:'.$this->pdoDBFile;
                    } else { // no driver
                        return false;
                    }
                    break;
                case "logsDir":
                    if(empty($this->logsDir)) {
                        $this->logsDir = "logs";
                    }
                    return $this->logsDir;
                    break;
                case "logsFiles":
                    return scandir($_SERVER['DOCUMENT_ROOT'].'/'.$this->logsDir);
                    break;
                default:
                    return $this->$name;
            }
        }

        public function __set($name, $value) {
            switch($name) {
                case "contentType":
                    $this->contentType = $value;
                    break;
                case "timeZone":
                    $this->timeZone = $value;
					// Могут потребоваться дополнительные расширения
                    // date_default_timezone_set($this->timeZone);
                    break;
                    case "encoding":
                    if(empty($this->contentType)) {
                        $this->contentType = "text/html"; // Для отправки header требуется $this->contentType 
                    }
                    $this->encoding = $value;
                    mb_internal_encoding($this->encoding);
                    header('Content-Type: '.$this->contentType.'; charset='.$this->encoding);
                    break;
                case "cors":
                    $this->cors = $value;
                    header('Access-Control-Allow-Origin: '.$this->cors);
                    break;
                case "logsDir":
                    if(!empty($value)) {
                        $this->logsDir = $value;
                        if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$this->logsDir)) {
                            mkdir($_SERVER['DOCUMENT_ROOT'].'/'.$this->logsDir);
                        }
                    }
                    break;
                default:
                    $this->$name = $value;
            }
        }

        /* 
        Для упрощения инициализации в конструктор можно передать ассоциативный массив значений
        с ключами указывающими на свойства конфигурации 
        */
        function __construct($config = false) {
            if($config || is_array($config)) {
                $this->timeZone = $config['time_zone'];
                $this->contentType = $config['content_type'];
                $this->encoding = $config['encoding'];

                $this->pdoDriver = $config['pdo_driver'];
                $this->pdoHost = $config['pdo_host'];
                $this->pdoUser = $config['pdo_user'];
                $this->pdoPassword = $config['pdo_password'];
                $this->pdoSchema = $config['pdo_schema'];
                $this->pdoEngine = $config['pdo_engine'];

                $this->logsDir = $config['logs_dir'];
            }
        }

        public function log($text, $file = "default") {
            $dest = $_SERVER['DOCUMENT_ROOT'].'/'.$this->logsDir.'/'.$file.'.log';
            if(!$fd = fopen($dest, "a+")) {
                echo __METHOD__.': ошибка открытия файла: '.$dest;
                return false;
            }
            $dt = date('d-m-y H:i:s');
            fwrite($fd, $dt.'|'.htmlspecialchars($text)."\n");
            fclose($fd);
            return true;
        }

        public function readLogs($file = "default") {
            $dest = $_SERVER['DOCUMENT_ROOT'].'/'.$this->logsDir.'/'.$file.'.log';
            return array_reverse(file($dest));
        }

        public function showLogs($file = "default", $count = 50) {
            $logs = $this->readLogs($file);
            $logsCount = count($logs);
            echo '<table>';
            for($i = 0; $i < $logsCount && $i < $count; $i++) {
                $log = explode('|', $logs[$i]);
                echo '<tr><td style="background: #AAA; padding: 2px;">'.($i+1).'</td><td style="background: yellow; padding: 2px;"><i>'.$log[0].'</i></td><td style="background: #BBB; padding: 2px;">'.$log[1].'</td></tr>';
            }
            echo '</table>';

        }
    }