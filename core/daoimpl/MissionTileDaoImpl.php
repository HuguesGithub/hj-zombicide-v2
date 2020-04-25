<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionTileDaoImpl
 * @author Hugues.
 * @since 1.04.07
 * @version 1.04.07
 */
class MissionTileDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('MissionTile'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = MissionTile::convertElement($row);
      }
    }
    return $Items;
  }
  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|MissionTile
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new MissionTile()); }
}
