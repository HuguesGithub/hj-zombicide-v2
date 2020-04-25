<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorServices
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class SurvivorServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var SurvivorDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new SurvivorDaoImpl();
  }
  /**
   * Construit le tableau des filtres pour la requête dédiée.
   * @param array $arrF
   * @return array
   */
  private function buildFilters($arrF)
  {
    $arrParams = array();
    array_push($arrParams, (!empty($arrF[self::FIELD_NAME]) && !is_array($arrF[self::FIELD_NAME])) ? '%'.$arrF[self::FIELD_NAME].'%' : '%');
    array_push($arrParams, ($this->isNonEmptyAndNoArray($arrF, self::FIELD_ZOMBIVOR) ? $arrF[self::FIELD_ZOMBIVOR] : '%'));
    array_push($arrParams, ($this->isNonEmptyAndNoArray($arrF, self::FIELD_ULTIMATE) ? '%'.$arrF[self::FIELD_ULTIMATE].'%' : '%'));
    array_push($arrParams, ($this->isNonEmptyAndNoArray($arrF, self::FIELD_EXPANSIONID) ? $arrF[self::FIELD_EXPANSIONID] : '%'));
    array_push($arrParams, ($this->isNonEmptyAndNoArray($arrF, self::FIELD_BACKGROUND) ? $arrF[self::FIELD_BACKGROUND] : '%'));
    array_push($arrParams, ($this->isNonEmptyAndNoArray($arrF, self::FIELD_LIVEABLE) ? $arrF[self::FIELD_LIVEABLE] : '%'));
    return $arrParams;
  }
  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getSurvivorsWithFilters($arrFilters=array(), $orderby='name', $order='asc')
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    if (isset($arrFilters[self::FIELD_EXPANSIONID]) && strpos($arrFilters[self::FIELD_EXPANSIONID], ',')!==false) {
      return $this->Dao->selectEntriesInExpansions($arrParams);
    } else {
      return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $arrParams);
    }
  }

  public function selectSurvivor($id)
  { return $this->select(__FILE__, __LINE__, $id); }
}
