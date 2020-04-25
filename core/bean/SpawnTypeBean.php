<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SpawnTypeBean
 * @author Hugues
 * @since 1.0.00
 * @version 1.0.00
 */
class SpawnTypeBean extends LocalBean
{
  /**
   * Class Constructor
   * @param SpawnType $SpawnType
   */
  public function __construct($SpawnType='')
  {
    parent::__construct();
    $this->SpawnType = ($SpawnType=='' ? new SpawnType() : $SpawnType);
  }
  /**
   * @param string $tBodyButtons Template des Boutons de fin de ligne
   * @return string
   */
  public function getRowForAdminPage($tBodyButtons)
  {
    $SpawnType = $this->SpawnType;
    $queryArg = array(
      self::CST_ONGLET=>'parametre',
      self::CST_POSTACTION=>'edit',
      'table'=>'spawntype',
      'id'=>$SpawnType->getId()
    );
    $urlEdit = $this->getQueryArg($queryArg);
    $queryArg[self::CST_POSTACTION] = 'trash';
    $urlTrash = $this->getQueryArg($queryArg);
    $args = array(
      '&nbsp;',
      $urlEdit,
      $urlTrash
    );
    $tBody  = '<tr><td>'.$SpawnType->getId().'</td><td>'.$SpawnType->getName();
    return $tBody.vsprintf($tBodyButtons, $args).'</tr>';
  }
}
