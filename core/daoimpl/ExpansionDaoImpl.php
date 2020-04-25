<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe ExpansionDaoImpl
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class ExpansionDaoImpl extends LocalDaoImpl
{
  /**
   * Class constructor
   */
  public function __construct()
  { parent::__construct('Expansion'); }
  /**
   * @param array $rows
   * @return array
   */
  protected function convertToArray($rows)
  {
    $Items = array();
    if (!empty($rows)) {
      foreach ($rows as $row) {
        $Items[] = Expansion::convertElement($row);
      }
    }
    return $Items;
  }
  /**
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|Expansion
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new Expansion()); }
  /**
   * @param string $file
   * @param string $line
   */
  protected function updateNbMissions($file, $line)
  {
    $subRequest = 'SELECT COUNT(*) FROM wp_11_zombicide_mission_expansion me WHERE me.expansionId=e.id';
    $requete = 'UPDATE wp_11_zombicide_expansion e SET nbMissions = ('.$subRequest.');';
    $this->createEditDeleteEntry($file, $line, $requete);
  }
}
