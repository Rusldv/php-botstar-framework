<?php 
    /**
     * Работа с базами данных
     */
    namespace botstar;

     class DataWelcome {
        private $dsn            =   "";
        private $user           =   "";
        private $password       =   "";

        private $db             =   null;

        function __construct($dsn, $user, $password = "") {
            $this->dsn =  $dsn;
            $this->user = $user;
            $this->password = $password;
            /*
            print '<br />dsn: '.$this->dsn;
            print '<br />user: '.$this->user;
            print '<br />password: '.$this->password;
            */
            try {
                $this->db = new \PDO($dsn, $this->user, $this->password);
                $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                //var_dump($this->db);
            } catch(\PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function getConnection() {
            return $this->db;
        }

        public function execute($sql_execute, $prepare = null) {
            try {
                if($prepare && is_array($prepare)) {
                    $prep = $this->db->prepare($sql_execute);
                    return $prep->execute($prepare);
                } else {
                    return $this->db->exec($sql_execute);
                }
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function last($colname = false) {
            try {
                if($colname) return $this->db->lastInsertId($colname);
                return $this->db->lastInsertId();
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function select($sql, $prepare = null, $callback = null) {
            try {
                if(!$prepare) {
                    $stmt = $this->db->query($sql);
                    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                } else {
                    $prep = $this->db->prepare($sql);
                    $prep->execute($prepare);
                    $rows = $prep->fetchAll(\PDO::FETCH_ASSOC);
                }
                if(!$callback) return $rows;
                if(!is_callable($callback)) return $rows;
                $callback($rows);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }

        // ! Example function with Reflection
        public function here($callback = null) {
            if($callback) {
                print 'Callback';
                $rcb = new \ReflectionFunction($callback);
                //var_dump($rcb);
                $fnm = $rcb->getFileName();
                print '<br />File Name: '.$fnm;
                $sln = $rcb->getStartLine();
                print '<br />Start Line: '.$sln;

                $lines = file($fnm);
                print '<br />'.$lines[$sln-2];
            }
        }
     }