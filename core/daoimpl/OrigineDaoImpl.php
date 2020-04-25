<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe OrigineDaoImpl
 * @author Hugues.
 * @since 1.04.00
 * @version 1.04.00
 */
class OrigineDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('Origine'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = Origine::convertElement($row);
      }
    }
    return $Items;
  }
  /**
   * @param string $file
   * @param string $line
   * @param array $arrParams
   * @return Origine
   */
   public function select($file, $line, $arrParams)
   { return parent::localSelect($file, $line, $arrParams, new Origine()); }
}
