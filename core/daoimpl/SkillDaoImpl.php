<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SkillDaoImpl
 * @author Hugues.
 * @since 1.00.00
 * @version 1.02.00
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
   * @param string $file
   * @param int $line
   * @param array $arrParams
   * @return array|Skill
   */
  public function select($file, $line, $arrParams)
  { return parent::localSelect($file, $line, $arrParams, new Skill()); }
}
