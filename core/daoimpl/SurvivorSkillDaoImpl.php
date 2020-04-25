<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorSkillDaoImpl
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class SurvivorSkillDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('SurvivorSkill'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = SurvivorSkill::convertElement($row);
      }
    }
    return $Items;
  }
  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|SurvivorSkill
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new SurvivorSkill()); }
}
