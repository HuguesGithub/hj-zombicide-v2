<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionExpansionDaoImpl
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class MissionExpansionDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('MissionExpansion'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = MissionExpansion::convertElement($row);
      }
    }
    return $Items;
  }
  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|MissionExpansion
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new MissionExpansion()); }
}
