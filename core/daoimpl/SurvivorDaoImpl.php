<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorDaoImpl
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class SurvivorDaoImpl extends LocalDaoImpl
{
  protected $whereFiltersExpansionIn;
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('Survivor'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = Survivor::convertElement($row);
      }
    }
    return $Items;
  }
  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|Survivor
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new Survivor()); }
  /**
   * @param array $filters
   * @return array
   */
  public function selectEntriesInExpansions($filters) {
    $requete  = $this->selectRequest.$this->fromRequest;
    $requete .= "WHERE name LIKE '%s' AND zombivor LIKE '%s' AND ultimate LIKE '%s' AND (expansionId LIKE '%s' ";
    $requete .= "OR expansionId IN (".$filters[SQL_PARAMS_WHERE][3].")) ";
    $requete .= "AND background LIKE '%s' AND liveAble LIKE '%s' ";
    $requete .= $this->orderBy.$this->limit;
    return $this->convertToArray($this->selectEntriesAndLogQuery(__FILE__, __LINE__, $requete, $filters));
  }
}
