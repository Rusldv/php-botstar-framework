<?php 
    /**
     * Работа с базами данных
     * Помощьник
     */
    namespace botstar;

    class DataHelper extends DataWelcome {

        function __construct($dsn, $user, $password = "") {
            parent::__construct($dsn, $user, $password);
        }

        // Создание таблицы в базе данных
        public function create($table, $array, $if_not_exists = false) {
            if(empty($table)) throw new \Exception('в '.__METHOD__.'('.$array.')[public]: имя таблицы не может быть пустой строкой');
            if(!is_array($array)) throw new \Exception('в '.__METHOD__.'('.$array.')[public]: параметр [array] должен быть массивом');
            if(empty($array)) throw new \Exception('в '.__METHOD__.'('.$array.')[public]: массив не должен быть пустым');
            if(!is_array($array['cols'])) throw new \Exception('в '.__METHOD__.'('.$array.')[public]: отсутствует "cols" => array(...)');
            if(empty($array['cols'])) throw new \Exception('в '.__METHOD__.'('.$array.')[public]: пустой [cols]');
            // TODO проверка на пригодность имени табл. print 'table: '.$table.'<br />';
            $sql_arr = array();
            foreach($array['cols'] as $key => $item) {
                array_push($sql_arr, $key.' '.$item);
            }

            if(!empty($array['keys'])) {
                foreach($array['keys'] as $key => $item) {
                    array_push($sql_arr, $key.'('.$item.')');
                }
            }

            $sql_str = implode(', ', $sql_arr);

            if(!empty($array['exts'])) {
               $sql_str = '('.$sql_str.') '.$array['exts'];
            } else {
               $sql_str = '('.$sql_str.')';
            }

            if($if_not_exists) {
                    $sql_str = 'CREATE TABLE IF NOT EXISTS '.$table.' '.$sql_str;
            } else {
                    $sql_str = 'CREATE TABLE '.$table.' '.$sql_str;
            }

            $this->execute($sql_str);
            return true;
        }

        // Удаление таблицы из базы данных
        public function drop($table) {
            if(empty($table)) throw new \Exception('в '.__METHOD__.'('.$array.')[public]: имя таблицы не может быть пустой строкой');
            $sql = 'DROP TABLE '.$table;
            $this->execute($sql);
            return true;
        }

        // Добавление записи в таблицу
        public function insert($table, $values) {
            if(empty($table)) throw new \Exception('в '.__METHOD__.'()[public]: имя таблицы не может быть пустой строкой');
            if(!is_array($values)) throw new \Exception('в '.__METHOD__.'()[public]: параметр [values] должен быть массивом');
            if(empty($values)) throw new \Exception('в '.__METHOD__.'()[public]: параметр [values] не может быть пустым');
            
            // Подготавливаем
            $keys = array();
            $preps = array();
            $vals = array();
            // Запалняем массивы
            foreach($values as $key => $value) {
                array_push($keys, $key);
                array_push($preps, ':'.$key);
                array_push($vals, $value);
            }

            $args = array_combine($keys, $vals);
            //print_r($args);

            $keys = implode(', ', $keys);
            $preps = implode(', ', $preps);
        
            $sql = 'INSERT INTO '.$table.' ('.$keys.') VALUES ('.$preps.')';
            //print $sql;

            return $this->execute($sql, $args);
        }

        // Обновление данных в таблице
        public function updateById($table, $id, $values) {
            if(empty($table)) throw new \Exception('в '.__METHOD__.'()[public]: имя таблицы не может быть пустой строкой');
            if(empty($id)) throw new \Exception('в '.__METHOD__.'()[public]: должен быть указан id записи');
            if(!is_array($values)) throw new \Exception('в '.__METHOD__.'()[public]: параметр [values] должен быть массивом');
            if(empty($values)) throw new \Exception('в '.__METHOD__.'()[public]: параметр [values] не может быть пустым');
            
            $sets = array();
            foreach($values as $key => $value) {
                array_push($sets, $key.'=:'.$key);
            }
            $values['id'] = $id;
            $sets = implode(', ', $sets);
            $sql = 'UPDATE '.$table.' SET '.$sets.' WHERE id=:id';
            
            return $this->execute($sql, $values);
        }

        // Удадение записи из таблицы
        public function deleteById($table, $id) {
            if(empty($table)) throw new \Exception('в '.__METHOD__.'()[public]: имя таблицы не может быть пустой строкой');
            if(empty($id)) throw new \Exception('в '.__METHOD__.'()[public]: должно быть указан id записи');
            $sql = 'DELETE FROM '.$table.' WHERE id=:id';

            return $this->execute($sql, array("id" => $id));
        }

        // Получение записи по id
        public function selectById($table, $id) {
            if(empty($table)) throw new \Exception('в '.__METHOD__.'()[public]: имя таблицы не может быть пустой строкой');
            if(empty($id)) throw new \Exception('в '.__METHOD__.'()[public]: должно быть указан id записи');
            $sql_select = 'SELECT * FROM '.$table.' WHERE id=:id';

            return $this->select($sql_select, array("id" => $id));
        }
    }