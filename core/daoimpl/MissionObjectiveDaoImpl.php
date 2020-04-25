<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionObjectiveDaoImpl
 * @author Hugues.
 * @since 1.04.08
 * @version 1.04.08
 */
class MissionObjectiveDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('MissionObjective'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = MissionObjective::convertElement($row);
      }
    }
    return $Items;
  }
  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|MissionRule
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new MissionObjective()); }
}
