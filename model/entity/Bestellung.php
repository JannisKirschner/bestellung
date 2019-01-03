<?php
require_once __DIR__ . '/../../connection.php';
require_once __DIR__ . '/../Populate.php';

class Bestellung {
    private $id;
    private $kunde_id;
    private $date;

    public static function checkEmail($email) {
        $db = Db::instantiate();
        $query = 'SELECT id FROM kunde where deleted_at is null and email COLLATE UTF8_GENERAL_CI like "?";';
        $stmt = $db->prepare($query);
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result || $result->num_rows == 0) {
            return false;
        } else {
            return $result->fetch_assoc()['id'];
        }
    }

    public static function create($kunde_id, $targetDate) {
//        var_dump($targetDate);
        $db = Db::instantiate();
        $query = 'INSERT INTO bestellung (kunde_id,datum,ziel_datum) VALUES ("?", now(),"?")';
        $stmt = $db->prepare($query);
        $stmt->bind_param("is",$kunde_id,$targetDate);
        $stmt->execute();
        $result = $stmt->get_result();
        Db::checkConnection($result, $query);
        $last_id = $db->insert_id;
        return $last_id;
    }

    public static function find($id) {
        $db = Db::instantiate();
        $query=('SELECT * FROM bestellung WHERE deleted_at is null and id=?');
        $stmt = $db->prepare($query);
        $stmt->bind_param("is",$kunde_id,$targetDate);
        $stmt->execute();
        $result = $stmt->get_result();
        //if the result is empty the $result will be an object with numrows == 0 but if its an invalid statement like "nummer="
        if (!$result || $result->num_rows == 0) {
            return false;
        } else {
            $bestellungArr = $result->fetch_assoc();
            $bestellung = populate::populateBestellung($bestellungArr);
            return $bestellung;
        }
    }

    /*
    public static function findByClient($client_id) {
        $db = Db::instantiate();
        $result = $db->query('SELECT * FROM rechnung WHERE deleted_at is null and kunde_id=' . $client_id);
        //if the result is empty the $result will be an object with numrows == 0 but if its an invalid statement like "nummer="
        if (!$result || $result->num_rows == 0) {
            return false;
        } else {
            $rechnungArr = $result->fetch_assoc();
            $rechnung = populate::populateRechnung($rechnungArr);
            return $rechnung;
        }
    }*/

    public static function allFrom($datum) {
        $rechnungen = [];
        $db = Db::instantiate();
        $query=('SELECT * FROM rechnung WHERE deleted_at is null and datum="?" order by id desc');
        $stmt = $db->prepare($query);
        $stmt->bind_param("s",$datum);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($rechnungArr = $result->fetch_assoc()) {
            $rechnung = Populate::populateRechnung($rechnungArr);
            $rechnungen[] = $rechnung;
        }
        if (!empty($rechnungen)) {
            return $rechnungen;
        }
        return false;
    }

    /*
    public static function getSearchResult($inputVal) {
        $db = Db::instantiate();
//        echo "SELECT * FROM rechnung r INNER JOIN kunde k ON r.id = k.id WHERE k.name LIKE '%".$inputVal."%' OR k.vorname LIKE '%".$inputVal."%'";
        $result = $db->query("SELECT r.id,r.bezahlt,r.kunde_id FROM rechnung r INNER JOIN kunde k ON r.kunde_id = k.id WHERE 
        deleted_at is null and k.name LIKE '%" . $inputVal . "%' OR k.vorname LIKE '%" . $inputVal . "%'");
        $rechnungen = null;

        while ($rechnungArr = $result->fetch_assoc()) {
            $rechnung = Populate::populateRechnung($rechnungArr);
            $rechnungen[] = $rechnung;
        }
        if (!empty($rechnungen)) {
            return $rechnungen;
        }
        return false;
    }*/

    /*
    public static function getRechnungBetrag($rechnung_id) {
        $db = Db::instantiate();
        $result = $db->query('SELECT sum(preis) as total FROM `position` WHERE deleted_at is null and rechnung_id=' . $rechnung_id);
        //if the result is empty the $result will be an object with numrows == 0 but if its an invalid statement like "nummer="
        if (!$result || $result->num_rows == 0) {
            return 0;
        } else {
            $rechnungBetrag = $result->fetch_assoc();
            return $rechnungBetrag['total'];
        }
    }*/

    public static function del($id) {
        $db = Db::instantiate();
        $query = 'UPDATE `bestell_position` SET deleted_at=now() WHERE bestellung_id=?';
        $stmt = $db->prepare($query);
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result = $stmt->get_result();
        Db::checkConnection($result, $query);


        $query = 'UPDATE bestellung SET deleted_at=now() WHERE id=?';
        $stmt = $db->prepare($query);
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result = $stmt->get_result();

        Db::checkConnection($result, $query);
    }

    /**
     * set payed
     * @param $id
     * @param $value
     */
    /*
    public static function check($id, $value) {
        $db = Db::instantiate();
        $query = 'UPDATE rechnung SET bezahlt=' . $value . ' WHERE id=' . $id;
        $sql = $db->query($query);
        Db::checkConnection($sql, $query);
    }*/

    public static function updComment($id, $value) {
        $db = Db::instantiate();
        $query = 'UPDATE rechnung SET kommentar="?" WHERE id=?';
        $stmt = $db->prepare($query);
        $stmt->bind_param("si",$value,$id);
        $stmt->execute();
        $result = $stmt->get_result();
        Db::checkConnection($result, $query);
    }

    /**
     * @return array|null
     */
    /*
    public static function getDates() {
        $db = Db::instantiate();
        $result = $db->query('SELECT DISTINCT datum FROM rechnung where deleted_at is null;');
        //if the result is empty the $result will be an object with numrows == 0 but if its an invalid statement like "nummer="
        if (!$result || $result->num_rows == 0) {
            return null;
        } else {
            $rowDates = [];
            while ($row = $result->fetch_assoc()) {
                $rowDates[] = $row['datum'];
            }
            $dates = [];
            foreach (array_reverse($rowDates) as $datum) {
                $datum = strtotime($datum);
                $datum = date('d.m.Y', $datum);
                $dates[] = $datum;
            }
//            $dates = $result->fetch_assoc();
            return $dates;
        }
    }*/

    /*
    public static function lastDate() {
        $db = Db::instantiate();
        $result = $db->query('SELECT datum FROM rechnung where deleted_at is null order by datum desc limit 1;');
        //if the result is empty the $result will be an object with numrows == 0 but if its an invalid statement like "nummer="
        if (!$result || $result->num_rows == 0) {
            return null;
        } else {
            return date('d-m-Y', strtotime($result->fetch_assoc()['datum']));;
        }
    }
*/
    /**
     * return true if there are multiple orders
     *
     * @param $kunde_id
     * @param $datum
     * @return bool|mixed
     */
    public static function checkMultipleOrdersAndGetOlder($kunde_id, $datum) {
        $db = Db::instantiate();
        $query = 'select id from bestellung where kunde_id=? and ziel_datum="?" and deleted_at is null;';
        $stmt = $db->prepare($query);
        $stmt->bind_param("is",$kunde_id,$datum);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result || $result->num_rows == 0) {
            return false;
        }
        $ids=[];
        while ($row = $result->fetch_assoc()) {
            $ids[] = $row['id'];
        }
        if (count($ids) > 1) {
            return min($ids);
        }
            return false;


    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getKundeId() {
        return $this->kunde_id;
    }

    /**
     * @param mixed $kunde_id
     */
    public function setKundeId($kunde_id) {
        $this->kunde_id = $kunde_id;
    }


    /**
     * @return mixed
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date) {
        $this->date = $date;
    }

}
