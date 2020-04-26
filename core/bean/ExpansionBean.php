<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe ExpansionBean
 * @author Hugues
 * @since 1.04.24
 * @version 1.04.26
 */
class ExpansionBean extends LocalBean
{
  /**
   * Class Constructor
   * @param Expansion $Expansion
   */
  public function __construct($Expansion='')
  {
    parent::__construct();
    $this->Expansion = ($Expansion=='' ? new Expansion() : $Expansion);
  }

  /**
   * @return string
   */
  public function getButton()
  {
    $str  = '<div type="button" class="btn btn-dark btn-expansion" data-expansion-id="'.$this->Expansion->getId();
    return $str. '"><span><i class="far fa-square"></i></span> '.$this->Expansion->getName().'</div>';
  }

























  /**
   * @param string $tBodyButtons Template des Boutons de fin de ligne
   * @return string
   *
  public function getRowForAdminPage($tBodyButtons)
  {
    $Expansion = $this->Expansion;
    $arrF = array(self::CST_EXPANSIONID=>$Expansion->getId());
    $MissionExpansions = $this->MissionExpansionServices->getMissionExpansionsWithFilters(__FILE__, __LINE__, $arrF);
    $nb = count($MissionExpansions);
    $queryArg = array(
      self::CST_ONGLET=>'parametre',
      self::CST_POSTACTION=>'edit',
      'table'=>'expansion',
      'id'=>$Expansion->getId()
    );
    $urlEdit = $this->getQueryArg($queryArg);
    $queryArg[self::CST_POSTACTION] = self::CST_TRASH;
    $urlTrash = $this->getQueryArg($queryArg);
    $args = array(
      $nb.' Mission'.($nb>1?'s':''),
      $urlEdit,
      $urlTrash
    );
    $tBody  = '<tr><td>'.$Expansion->getId().self::CST_TD_SEP.$Expansion->getCode().self::CST_TD_SEP.$Expansion->getName();
    $tBody .= self::CST_TD_SEP.$Expansion->getDisplayRank().'</td>';
    return $tBody.vsprintf($tBodyButtons, $args).'</tr>';
  }
  /**
   * @param int $id
   * @return string
   *
  public function getMenuButtonLive($id)
  {
    $Expansion = $this->Expansion;
    $str  = '<div type="button" class="btn btn-dark btn-expansion" data-expansion-id="'.$id.'"><span class="';
    return $str.'"><i class="far fa-square"></i></span> '.$Expansion->getName().'</div>';
  }
  /**
   * @param string $id
   * @param string $spawnSpan
   * @return string
   *
  public function getSpawnMenuButtonLive($id, $spawnSpan)
  {
    $Expansion = $this->Expansion;
    $str  = '<div type="button" class="btn btn-dark btn-expansion" data-expansion-id="'.$id.'"><span data-spawnspan="'.$spawnSpan;
    return $str.'"><i class="far fa-square"></i></span> '.$Expansion->getName().$spawnSpan.'</div>';
  }
  * */
}
