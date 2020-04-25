<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageToolsBean
 * @author Hugues
 * @since 1.04.24
 * @version 1.04.24
 */
class WpPageToolsBean extends WpPageBean
{
  protected $urlTemplateSelSurv = 'web/pages/public/wppage-selectsurvivors.php';
  /**
   * Class Constructor
   * @param WpPage $WpPage
   */
  public function __construct($WpPage='')
  {
    parent::__construct($WpPage);
    $this->ExpansionServices = new ExpansionServices();
    $this->SurvivorServices = new SurvivorServices();
  }

  /**
   * @return string
   */
  public function getSelectSurvivorsContent()
  {
    // Gestion du menu des Stand Alone et Extensions officielles
    $arrExpansions = array(
      'Saison 1' => array(23, 9, 10),
      'Saison 2' => array(24, 11, 12, 13),
      'Saison 3' => array(25, 16, 17, 18),
      'Extensions' => array(4, 14),
    );
    $str = '';
    $arrOfficiels = array();
    foreach ($arrExpansions as $parent=>$arrChildren) {
      $str .= '<div type="button" class="btn btn-dark btn-expansion-group"><span><i class="fa fa-chevron-circle-down"></i></span> '.$parent.'</div>';
      while (!empty($arrChildren)) {
        $childId = array_shift($arrChildren);
        array_push($arrOfficiels, $childId);
        $Expansion = $this->ExpansionServices->selectExpansion($childId);
        $str .= '<div type="button" class="btn btn-secondary btn-expansion hidden" data-expansion-id="'.$childId;
        $str .= '" data-nb-survivants="'.$Expansion->getnbSurvivants().'"><span><i class="far fa-square"></i></span> ';
        $str .= $Expansion->getName().'</div>';
      }
    }
    // Gestion des extensions fan-made
    $strFanMade = '';
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters();
    while (!empty($Expansions)) {
      $Expansion = array_shift($Expansions);
      $childId = $Expansion->getId();
      if (in_array($childId, $arrOfficiels) || $Expansion->getNbSurvivants()==0) {
        continue;
      }
      $strFanMade .= '<div type="button" class="btn btn-secondary btn-expansion hidden" data-expansion-id="'.$childId;
      $strFanMade .= '" data-nb-survivants="'.$Expansion->getnbSurvivants().'"><span><i class="far fa-square"></i></span> ';
      $strFanMade .= $Expansion->getName().'</div>';
    }

    // Gestion du menu des Survivants officiels
    $Survivors = $this->SurvivorServices->getSurvivorsWithFilters();
    $strSurvivors = '';
    while (!empty($Survivors)) {
      $Survivor = array_shift($Survivors);
      $strSurvivors .= $Survivor->getBean()->getButton();
    }

    /*
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(self::FIELD_NBSURVIVANTS=>1), self::FIELD_DISPLAYRANK);
    $str = '';
    while (!empty($Expansions)) {
      $Expansion = array_shift($Expansions);
      $id = $Expansion->getId();
      $Survivors = $this->SurvivorServices->getSurvivorsWithFilters(array(self::FIELD_EXPANSIONID=>$id));
      $str .= $Expansion->getBean()->getButton();
      while (!empty($Survivors)) {
        $Survivor = array_shift($Survivors);
        $str .= $Survivor->getBean()->getButton();
      }
    }
    */
    $args = array(
      // Liste des Extensions Officielles et ses Survivants - 1
      $str,
      // Liste des cartouches de tous les Survivants - 2
      $strSurvivors,
      // Liste des extensions Fan-Made - 3
      $strFanMade,
    );
    return $this->getRender($this->urlTemplateSelSurv, $args);

    /*
        <div type="button" class="btn btn-dark btn-expansion-group"><span><i class="fa fa-chevron-circle-down"></i></span> Saison 1</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="23"><span><i class="far fa-square"></i></span> Core</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="9"><span><i class="far fa-square"></i></span> Options</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="10"><span><i class="far fa-square"></i></span> Promos</div>
        <div type="button" class="btn btn-dark btn-expansion-group"><span><i class="fa fa-chevron-circle-down"></i></span> Saison 2</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="24"><span><i class="far fa-square"></i></span> Core</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="11"><span><i class="far fa-square"></i></span> Options</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="12"><span><i class="far fa-square"></i></span> Promos</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="13"><span><i class="far fa-square"></i></span> Guest</div>
        <div type="button" class="btn btn-dark btn-expansion-group"><span><i class="fa fa-chevron-circle-down"></i></span> Saison 3</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="25"><span><i class="far fa-square"></i></span> Core</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="16"><span><i class="far fa-square"></i></span> Options</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="17"><span><i class="far fa-square"></i></span> Promos</div>
        <div type="button" class="btn btn-secondary btn-expansion" data-expansion-id="18"><span><i class="far fa-square"></i></span> Guest</div>
        <div type="button" class="btn btn-dark btn-expansion" data-expansion-id="4"><span><i class="far fa-square"></i></span> Toxic City Mall</div>
        <div type="button" class="btn btn-dark btn-expansion" data-expansion-id="14"><span><i class="far fa-square"></i></span> Angry Neighbors</div>
     */
  }

}
