<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageSurvivorsBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.05.01
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
    $Survivors = $this->SurvivorServices->getSurvivorsWithFilters($this->arrFilters, $this->colSort, $this->colOrder);
    $this->nbElements = count($Survivors);
    $this->nbPages = ceil($this->nbElements/$this->nbperpage);
    // On slice la liste pour n'avoir que ceux à afficher
    $displayedSurvivors = array_slice($Survivors, $this->nbperpage*($this->paged-1), $this->nbperpage);
    // On construit le corps du tableau
    $strBody = '';
    if (!empty($displayedSurvivors)) {
      foreach ($displayedSurvivors as $Survivor) {
        $strBody .= $Survivor->getBean()->getRowForSurvivorsPage();
      }
    }
    /////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////
    // Affiche-t-on le filtre ?
    $showFilters = isset($this->arrFilters[self::FIELD_NAME])&&$this->arrFilters[self::FIELD_NAME]!='' || isset($this->arrFilters[self::FIELD_EXPANSIONID])&&$this->arrFilters[self::FIELD_EXPANSIONID]!='';
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
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  private function getExpansionFilters($expansionId='')
  { return parent::getBeanExpansionFilters($expansionId, self::FIELD_NBSURVIVANTS); }

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
      $strReturned .= $Survivor->getBean()->getRowForSurvivorsPage();
      $nb++;
    }
    return '<div id="page-selection-survivants">'.$strReturned.'</div>';
  }
  /**
   * @param array $post
   */
  public function setFilters($post=null)
  { parent::setBeanFilters($post, self::FIELD_NAME); }

}
