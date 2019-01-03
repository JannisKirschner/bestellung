<?php
require_once __DIR__ . '/../../connection.php';
require_once __DIR__ . '/../Populate.php';

class Artikel {

    private $id;
    private $nummer;
    private $name;
    private $kg_price;
    private $stueck_gewicht;


    public static function findArtikelByBestellArtikel($artikel_id) {
        $db = Db::instantiate();
        $query = 'SELECT a.* FROM bestell_artikel bp left join artikel a on bp.artikel_id = a.id WHERE bp.id=? and a.deleted_at is null;';
        $stmt = $db->prepare($query);
        $stmt->bind_param("i",$artikel_id);
        $stmt->execute();
        $result = $db->query($query);
        if (!$result || $result->num_rows == 0) {
            return false;
        } else {
            $artikelArr = $result->fetch_assoc();
            $artikel = populate::populateArtikel($artikelArr);
            return $artikel;
        }
    }

    /**
     * @return null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getNummer() {
        return $this->nummer;
    }

    /**
     * @param null $nummer
     */
    public function setNummer($nummer) {
        $this->nummer = $nummer;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getKgPrice() {
        return $this->kg_price;
    }

    /**
     * @param mixed $kg_price
     */
    public function setKgPrice($kg_price) {
        $this->kg_price = $kg_price;
    }

    /**
     * @return mixed
     */
    public function getStueckGewicht() {
        return $this->stueck_gewicht;
    }

    /**
     * @param mixed $stueck_gewicht
     */
    public function setStueckGewicht($stueck_gewicht) {
        $this->stueck_gewicht = $stueck_gewicht;
    }

    /*    public function jsonSerialize(){
            return [
                'artikel' => [
                    'artikel' => $this->artikel,
                    'PLZ' => $this->PLZ,
                    'id' => $this->id
                ]
            ];
        }*/

}
