<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorDaoImpl
 * @author Hugues.
 * @since 1.0.00
 * @version 1.05.06
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
   * @param array $arrParams
   * @param array $filters
   * @return array
   */
  public function selectEntriesWithFiltersIn($arrParams, $filters)
  {
    // On s'appuie sur la requête de base.
    $requete  = $this->selectRequest.$this->fromRequest;
    // Contrainte sur la Compétence Bleue
    if (isset($filters[self::COLOR_BLUE.'-'.self::FIELD_SKILLID])) {
      $requete .= 'INNER JOIN wp_11_zombicide_survivor_skill ss1 ON s.id=ss1.survivorId ';
      $requete .= 'AND ss1.skillId='.$filters[self::COLOR_BLUE.'-'.self::FIELD_SKILLID].' AND ss1.tagLevelId IN (10,11) ';
    }
    // Contrainte sur la Compétence Jaune
    if (isset($filters[self::COLOR_YELLOW.'-'.self::FIELD_SKILLID])) {
      $requete .= 'INNER JOIN wp_11_zombicide_survivor_skill ss2 ON s.id=ss2.survivorId ';
      $requete .= 'AND ss2.skillId='.$filters[self::COLOR_YELLOW.'-'.self::FIELD_SKILLID].' AND ss2.tagLevelId IN (20) ';
    }
    // Contrainte sur la Compétence Orange
    if (isset($filters[self::COLOR_ORANGE.'-'.self::FIELD_SKILLID])) {
      $requete .= 'INNER JOIN wp_11_zombicide_survivor_skill ss3 ON s.id=ss3.survivorId ';
      $requete .= 'AND ss3.skillId='.$filters[self::COLOR_ORANGE.'-'.self::FIELD_SKILLID].' AND ss3.tagLevelId IN (30,31) ';
    }
    // Contrainte sur la Compétence Rouge
    if (isset($filters[self::COLOR_RED.'-'.self::FIELD_SKILLID])) {
      $requete .= 'INNER JOIN wp_11_zombicide_survivor_skill ss4 ON s.id=ss4.survivorId ';
      $requete .= 'AND ss4.skillId='.$filters[self::COLOR_RED.'-'.self::FIELD_SKILLID].' AND ss4.tagLevelId IN (40,41,42) ';
    }
    // On peut aussi trier
    $requete .= $this->orderBy;
    // Et retourner le tableau de résultats.
    return $this->convertToArray($this->selectEntriesAndLogQuery(__FILE__, __LINE__, $requete, $arrParams));
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
