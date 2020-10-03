<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageToolsBean
 * @author Hugues
 * @since 1.04.24
 * @version 1.05.09
 */
class WpPageToolsBean extends WpPageBean
{
  protected $urlTemplatePriorityOrder = 'web/pages/public/wppage-ordre-de-priorite.php';
  protected $urlTemplatePisteDes      = 'web/pages/public/wppage-pistededes.php';
  protected $urlTemplateRandomMap     = 'web/pages/public/wppage-generationmap.php';
  protected $urlTemplateSelSurv       = 'web/pages/public/wppage-selectsurvivors.php';
  protected $urlTemplateRandomMapV2   = 'web/pages/public/wppage-generationmap-v2.php';
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
      'Saison 2' => array(24, 11, 12),
      'Saison 3' => array(25, 16, 17),
      'Extensions' => array(4, 14, 19, 26),
    );
    $str = '';
    $arrOfficiels = array();
    foreach ($arrExpansions as $parent=>$arrChildren) {
      $str .= '<div type="button" class="btn btn-dark btn-expansion-group"><span><i class="fa fa-chevron-circle-down"></i></span> '.$parent.'</div>';
      while (!empty($arrChildren)) {
        $childId = array_shift($arrChildren);
        array_push($arrOfficiels, $childId);
        $Expansion = $this->ExpansionServices->selectExpansion($childId);
        $str .= $Expansion->getBean()->getButton(' btn-secondary hidden');
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
      $strFanMade .= $Expansion->getBean()->getButton(' btn-secondary hidden');
    }

    // Gestion du menu des Survivants officiels
    $Survivors = $this->SurvivorServices->getSurvivorsWithFilters();
    $strSurvivors = '';
    while (!empty($Survivors)) {
      $Survivor = array_shift($Survivors);
      $strSurvivors .= $Survivor->getBean()->getButton();
    }


    $args = array(
      // Liste des Extensions Officielles et ses Survivants - 1
      $str,
      // Liste des cartouches de tous les Survivants - 2
      $strSurvivors,
      // Liste des extensions Fan-Made - 3
      $strFanMade,
    );
    return $this->getRender($this->urlTemplateSelSurv, $args);
  }

  /**
   * @return string
   */
  public function getThrowSomeDiceContent()
  {
    $args = array(
      '','','',
    );
    return $this->getRender($this->urlTemplatePisteDes, $args);
  }

  public function getPriorityOrderContent()
  {
    $args = array(
      // Le titre de la page - 1
      $this->WpPage->getPostTitle(),
      // Le contenu de la page - 2
      $this->WpPage->getPostContent(),
    );
    return $this->getRender($this->urlTemplatePriorityOrder, $args);
  }

  public function getTravaux()
  {
  }

  public function getRandomMapV2()
  {
    /////////////////////////////////////////////////////////////////////////////
    // On récupère la liste de toutes les Extensions
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(), self::FIELD_DISPLAYRANK);
    $strExpansions = '';
    $strIcon = $this->getBalise(self::TAG_SPAN, $this->getBalise(self::TAG_I, '', array(self::ATTR_CLASS=>'far fa-square')));
    while (!empty($Expansions)) {
      $Expansion = array_shift($Expansions);
      // Si l'extension n'a pas de dalles, on passe à l'extension suivante.
      if ($Expansion->getNbDalles()==0) {
        continue;
      }
      // On en profite aussi pour construire le bloc de filtres.
      $attributes = array(
        self::ATTR_TYPE             => 'button',
        self::ATTR_CLASS            => 'btn btn-expansion btn-dark',
        self::ATTR_DATA_EXPANSIONID => $Expansion->getId(),
        'data-nb-dalles'            => $Expansion->getNbDalles(),
      );
      $strExpansions .= $this->getBalise(self::TAG_DIV, $strIcon.' '.$Expansion->getName(), $attributes);
    }

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // La liste des extensions - 1
      $strExpansions
    );
    return $this->getRender($this->urlTemplateRandomMapV2, $args);
  }
  public function getRandomMap()
  {
    /////////////////////////////////////////////////////////////////////////////
    // On récupère la liste de toutes les Extensions
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(), self::FIELD_DISPLAYRANK);
    $strFilters = '';
    $strSpawns = '';
    while (!empty($Expansions)) {
      $Expansion = array_shift($Expansions);
      // Si l'extension n'a pas de dalles, on passe à l'extension suivante.
      if ($Expansion->getNbDalles()==0) {
        continue;
      }
      // On en profite aussi pour construire le bloc de filtres.
      $strFilters .= $this->getBalise(self::TAG_OPTION, $Expansion->getName(), array(self::ATTR_VALUE => 'set-'.$Expansion->getId()));
    }

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // La liste des extensions - 1
      $strFilters
    );
    return $this->getRender($this->urlTemplateRandomMap, $args);
  }
}
