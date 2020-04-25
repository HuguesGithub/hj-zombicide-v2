<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe TileDaoImpl
 * @author Hugues.
 * @since 1.04.07
 * @version 1.04.07
 */
class TileDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('Tile'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = Tile::convertElement($row);
      }
    }
    return $Items;
  }
  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|Tile
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new Tile()); }
}
