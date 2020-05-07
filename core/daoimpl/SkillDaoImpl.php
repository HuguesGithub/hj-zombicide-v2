<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SkillDaoImpl
 * @author Hugues.
 * @since 1.00.00
 * @version 1.05.06
 */
class SkillDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('Skill'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = Skill::convertElement($row);
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
    // On doit faire une jointure externe pour lier la table mission_expansion si on cherche sur ce critère
    if (isset($filters[self::FIELD_TAGLEVELID])) {
      $requete .= 'INNER JOIN wp_11_zombicide_survivor_skill ss ON s.id=ss.skillId ';
      // On passe ensuite aux critères de sélection.
      $requete .= 'WHERE 1=1 ';
      // Contrainte sur le niveau
      $requete .= 'AND tagLevelId IN ('.$filters[self::FIELD_TAGLEVELID].') ';
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
   * @return array|Skill
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new Skill()); }
}
