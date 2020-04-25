<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SpawnBean
 * @author Hugues
 * @since 1.02.00
 * @version 1.04.26
 */
class SpawnBean extends LocalBean
{
  /**
   * Class Constructor
   * @param Spawn $Spawn
   */
  public function __construct($Spawn=null)
  {
    parent::__construct();
    $this->Spawn = ($Spawn==null ? new Spawn() : $Spawn);
  }

  public function displayCard()
  {
    $attrImg = array(
      self::ATTR_WIDTH  => 320,
      self::ATTR_HEIGHT => 440,
      self::ATTR_SRC    => $this->Spawn->getImgUrl(),
      self::ATTR_ALT    => '#'.$this->Spawn->getSpawnNumber(),
    );
    $strImg = $this->getBalise(self::TAG_IMG, '', $attrImg);
    return $this->getBalise(self::TAG_DIV, $strImg, array(self::ATTR_CLASS => 'card spawn set-'.$this->Spawn->getExpansionId()));
  }
}
