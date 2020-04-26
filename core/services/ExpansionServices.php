<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe ExpansionServices
 * @author Hugues.
 * @since 1.04.00
 * @version 1.04.27
 */
class ExpansionServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var ExpansionDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new ExpansionDaoImpl();
  }

  /**
   * @param array $arrFilters
   */
  private function buildFilters($arrFilters)
  {
    $this->arrParams[self::SQL_WHERE] = array();
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_CODE));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_NBMISSIONS, 0));
    array_push($this->arrParams[self::SQL_WHERE], $this->addNonArrayFilter($arrFilters, self::FIELD_NBSURVIVANTS, 0));
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getExpansionsWithFilters($arrFilters=array(), $orderby=self::FIELD_NAME, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }
  /**
   * @param int $id
   * @return Expansion
   */
  public function selectExpansion($id)
  { return $this->select(__FILE__, __LINE__, $id); }


  /**
   * Met à jour les donnée de la table Expansion... Obsolète pour l'heure
   *
  public function cleanAndUpdateExpansionData() {
    $requete  = 'UPDATE wp_3_z_expansion SET nbSurvivants = 0, nbDalles = 0, nbEquipmentCards=0, nbInvasionCards=0;';
    $requete .= 'UPDATE wp_3_z_expansion AS e1 ';
    $requete .= 'INNER JOIN (SELECT COUNT(s.id) AS nbSurvivants, e2.id AS e2Id FROM wp_3_z_survivor AS s ';
    $requete .= 'INNER JOIN wp_3_z_expansion AS e2 ON e2.id=s.expansionId GROUP BY s.expansionId) AS t2 ';
    $requete .= 'SET e1.nbSurvivants = t2.nbSurvivants ';
    $requete .= 'WHERE e1.id=t2.e2Id;';
    $requete .= 'UPDATE wp_3_z_expansion AS e1 ';
    $requete .= 'INNER JOIN (SELECT COUNT(t.id) AS nbDalles, e2.id AS e2Id FROM wp_3_z_tile AS t ';
    $requete .= 'INNER JOIN wp_3_z_expansion AS e2 ON e2.id=t.expansionId GROUP BY t.expansionId) AS t2 ';
    $requete .= 'SET e1.nbDalles = t2.nbDalles ';
    $requete .= 'WHERE e1.id=t2.e2Id;';
    $requete .= 'UPDATE wp_3_z_expansion AS e1 ';
    $requete .= 'INNER JOIN (SELECT COUNT(ee.id) AS nbEquipmentCards, e2.id AS e2Id FROM wp_3_z_equipment_expansion AS ee ';
    $requete .= 'INNER JOIN wp_3_z_expansion AS e2 ON e2.id=ee.expansionId GROUP BY ee.expansionId) AS t2 ';
    $requete .= 'SET e1.nbEquipmentCards = t2.nbEquipmentCards ';
    $requete .= 'WHERE e1.id=t2.e2Id;';
    $requete .= 'UPDATE wp_3_z_expansion AS e1 ';
    $requete .= 'INNER JOIN (SELECT COUNT(i.id) AS nbInvasionCards, e2.id AS e2Id FROM wp_3_z_invasion AS i ';
    $requete .= 'INNER JOIN wp_3_z_expansion AS e2 ON e2.id=i.expansionId GROUP BY i.expansionId) AS t2 ';
    $requete .= 'SET e1.nbInvasionCards = t2.nbInvasionCards ';
    $requete .= 'WHERE e1.id=t2.e2Id;';
  }
  */
}
