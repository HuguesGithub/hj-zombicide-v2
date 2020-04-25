<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionDaoImpl
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class MissionDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('Mission'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = Mission::convertElement($row);
      }
    }
    return $Items;
  }
  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|Mission
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new Mission()); }
  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @param array $filters
   * @return array
   */
  public function selectEntriesWithFiltersIn($file, $line, $arrParams, $filters)
  {
    // On s'appuie sur la requête de base.
    $requete  = $this->selectRequest.$this->fromRequest;
    // On doit faire une jointure externe pour lier la table mission_expansion si on cherche sur ce critère
    if (isset($filters[self::FIELD_EXPANSIONID])) {
      $requete .= 'INNER JOIN wp_11_zombicide_mission_expansion me ON m.id=me.missionId ';
    }
    // On passe ensuite aux critères de sélection.
    //$requete .= $this->whereFilters;
    $requete .= 'WHERE 1=1 ';
    // Contrainte sur la difficulté
    if (isset($filters[self::FIELD_LEVELID])) {
      $requete .= 'AND levelId IN ('.$filters[self::FIELD_LEVELID].') ';//('.implode(',', $filters[self::FIELD_LEVELID]).') ';
    }
    // Contrainte sur la durée
    if (isset($filters[self::FIELD_DURATIONID])) {
      $requete .= 'AND durationId IN ('.$filters[self::FIELD_DURATIONID].') ';//'.implode(',', $filters[self::FIELD_DURATIONID]).') ';
    }
    // Contrainte sur le nombre de joueurs
    if (isset($filters[self::FIELD_PLAYERID])) {
      $requete .= 'AND playerId IN ('.implode(',', $filters[self::FIELD_PLAYERID]).') ';
    }
    // Contrainte sur l'origine
    if (isset($filters[self::FIELD_ORIGINEID])) {
      $requete .= 'AND origineId IN ('.implode(',', $filters[self::FIELD_ORIGINEID]).') ';
    }
    // Contrainte sur l'extension
    if (isset($filters[self::FIELD_EXPANSIONID])) {
      if (strpos($filters[self::FIELD_EXPANSIONID], ',')===false) {
        $requete .= 'AND expansionId = '.$filters[self::FIELD_EXPANSIONID].' ';
      } else {
        $requete .= 'AND expansionId IN ('.$filters[self::FIELD_EXPANSIONID].') ';
      }
    }
    // On peut aussi trier
    $requete .= $this->orderBy;
    // Et retourner le tableau de résultats.
    return $this->convertToArray($this->selectEntriesAndLogQuery($file, $line, $requete, $arrParams));
  }
}
