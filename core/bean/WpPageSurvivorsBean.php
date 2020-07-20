<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageSurvivorsBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.07.19
 */
class WpPageSurvivorsBean extends WpPageBean
{
  protected $urlTemplate = 'web/pages/public/wppage-survivors.php';
  /**
   * Class Constructor
   * @param WpPage $WpPage
   */
  public function __construct($WpPage='')
  {
    parent::__construct($WpPage);
    $this->SkillServices = new SkillServices();
    $this->SurvivorServices = new SurvivorServices();
    $this->ExpansionServices = new ExpansionServices();
  }
  /**
   * On vérifie si on est ici pour traiter la page des Survivants, ou un Survivant en particulier.
   * Pour le cas d'un Survivant, on retourne une WpPostSurvivorBean.
   * @return string
   */
  public function getContentPage()
  {
    $this->setFilters();
    return $this->getListContentPage();
  }
  /**
   * @return string
   */
  public function getListContentPage()
  {
    /////////////////////////////////////////////////////////////////////////////
    // On récupère la liste des Survivants puis les éléments nécessaires à la pagination.
    if (!$this->isSkillSearched()) {
      $Survivors = $this->SurvivorServices->getSurvivorsWithFilters($this->arrFilters, $this->colSort, $this->colOrder);
    } else {
      $Survivors = $this->SurvivorServices->getSurvivorsWithFiltersIn($this->arrFilters, $this->colSort, $this->colOrder);
    }
    $this->nbElements = count($Survivors);
    $this->nbPages = ceil($this->nbElements/$this->nbperpage);
    // On slice la liste pour n'avoir que ceux à afficher
    $displayedSurvivors = array_slice($Survivors, $this->nbperpage*($this->paged-1), $this->nbperpage);
    // On construit le corps du tableau
    $strBody = '';
    if (!empty($displayedSurvivors)) {
      foreach ($displayedSurvivors as $Survivor) {
        $strBody .= $Survivor->getBean()->getRowForPublicPage();
      }
    }
    /////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////
    // Affiche-t-on le filtre ?
    $showFilters = isset($this->arrFilters[self::FIELD_NAME])&&$this->arrFilters[self::FIELD_NAME]!=''
      || isset($this->arrFilters[self::FIELD_EXPANSIONID])&&$this->arrFilters[self::FIELD_EXPANSIONID]!=''
      || $this->isSkillSearched();
    /////////////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // Les lignes du tableau - 1
      $strBody,
      // On affiche le dropdown par pages - 2
      $this->getDropdownNbPerPages(),
      // On affiche la pagination - 3
      $this->getNavPagination(),
      // Affiche ou non le bloc filtre - 4
      $showFilters ? 'block' : 'none',
      // Si le Nom est renseigné - 5
      $this->arrFilters[self::FIELD_NAME],
      // Liste des Extensions - 6
      $this->getExpansionFilters($this->arrFilters[self::FIELD_EXPANSIONID]),
      // Liste des Compétences bleues - 7
      $this->getBeanSkillFilters(self::COLOR_BLUE, $this->arrFilters['blue-skillId']),
      // Liste des Compétences jaunes - 8
      $this->getBeanSkillFilters(self::COLOR_YELLOW, $this->arrFilters['yellow-skillId']),
      // Liste des Compétences oranges - 9
      $this->getBeanSkillFilters(self::COLOR_ORANGE, $this->arrFilters['orange-skillId']),
      // Liste des Compétences rouges - 10
      $this->getBeanSkillFilters(self::COLOR_RED, $this->arrFilters['red-skillId']),
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  private function getExpansionFilters($expansionId='')
  { return parent::getBeanExpansionFilters($expansionId, self::FIELD_EXPANSIONID); }

  /**
   * @return string
   */
  public function getRandomTeam($post)
  {
    // On récupère les paramètres nécessaires
    $nbMax = $post['nbSurvSel'];
    $arrValues = explode(',', $post['value']);
    // On mélange les valeurs possibles
    shuffle($arrValues);
    $nb = 0;
    $strReturned  = '';
    while (!empty($arrValues) && $nb<$nbMax) {
      $value = array_shift($arrValues);
      $Survivor = $this->SurvivorServices->selectSurvivor($value);
      $strReturned .= $Survivor->getBean()->getRowForPublicPage();
      $nb++;
    }
    return '<div id="page-selection-survivants">'.$strReturned.'</div>';
  }
  /**
   * @param array $post
   */
  public function setFilters($post=null)
  { parent::setBeanFilters($post, self::FIELD_NAME); }

  private function isSkillSearched()
  {
    return isset($this->arrFilters[self::COLOR_BLUE.'-'.self::FIELD_SKILLID])&&$this->arrFilters[self::COLOR_BLUE.'-'.self::FIELD_SKILLID]!=''
    || isset($this->arrFilters[self::COLOR_YELLOW.'-'.self::FIELD_SKILLID])&&$this->arrFilters[self::COLOR_YELLOW.'-'.self::FIELD_SKILLID]!=''
    || isset($this->arrFilters[self::COLOR_ORANGE.'-'.self::FIELD_SKILLID])&&$this->arrFilters[self::COLOR_ORANGE.'-'.self::FIELD_SKILLID]!=''
    || isset($this->arrFilters[self::COLOR_RED.'-'.self::FIELD_SKILLID])&&$this->arrFilters[self::COLOR_RED.'-'.self::FIELD_SKILLID]!='';
  }
}
