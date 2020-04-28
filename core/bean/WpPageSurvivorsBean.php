<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageSurvivorsBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.04.28
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
    // On récupère l'éventuel paramètre FIELD_SURVIVORID
    $survivorId = $this->initVar(self::FIELD_SURVIVORID, -1);
    if ($survivorId==-1) {
      // S'il n'est pas défini, on affiche la liste des Survivants
      $this->setFilters();
      return $this->getListContentPage();
    } else {
      // S'il est défini, on affiche le Survivant associé.
      $Bean = new WpPostSurvivorBean($survivorId);
      return $Bean->getContentPage();
    }
  }
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
   * @return string
   */
  public function getListContentPage()
  {

    /////////////////////////////////////////////////////////////////////////////
    // On récupère la liste des Survivants puis les éléments nécessaires à la pagination.
    $Survivors = $this->SurvivorServices->getSurvivorsWithFilters($this->arrFilters, $this->colSort, $this->colOrder);
    $nbElements = count($Survivors);
    $nbPages = ceil($nbElements/$this->nbperpage);
    // On slice la liste pour n'avoir que ceux à afficher
    $displayedSurvivors = array_slice($Survivors, $this->nbperpage*($this->paged-1), $this->nbperpage);
    // On construit le corps du tableau
    $strBody = '';
    if (!empty($displayedSurvivors)) {
      foreach ($displayedSurvivors as $Survivor) {
        $strBody .= $Survivor->getBean()->getRowForSurvivorsPage();
      }
    }

    // On construit les liens de la pagination.
    $strPagination = $this->getPaginateLis($this->paged, $nbPages);

    // Affiche-t-on le filtre ?
    $showFilters = isset($this->arrFilters[self::FIELD_NAME])&&$this->arrFilters[self::FIELD_NAME]!='' || isset($this->arrFilters[self::FIELD_EXPANSIONID])&&$this->arrFilters[self::FIELD_EXPANSIONID]!='';

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      ($this->nbperpage==10 ? self::CST_SELECTED : ''),
      ($this->nbperpage==25 ? self::CST_SELECTED : ''),
      ($this->nbperpage==50 ? self::CST_SELECTED : ''),
      // Les lignes du tableau - 4
      $strBody,
      // N° du premier élément - 5
      $this->nbperpage*($this->paged-1)+1,
      // Nb par page - 6
      min($this->nbperpage*$this->paged, $nbElements),
      // Nb Total - 7
      $nbElements,
      // Si page 1, on peut pas revenir à la première - 8
      ($this->paged==1 ? ' '.self::CST_DISABLED : ''),
      // Liste des éléments de la Pagination - 9
      $strPagination,
      // Si page $nbPages, on peut pas aller à la dernière - 10
      ($this->paged==$this->nbperpage ? ' '.self::CST_DISABLED : ''),
      // Nombre de pages - 11
      $this->nbperpage,
      // Liste des Extensions (TODO éventuellement sélectionnées) - 12
      $this->getExpansionFilters($this->arrFilters[self::FIELD_EXPANSIONID]),
      '', // - 13
      // Si le Nom est renseigné - 14
      $this->arrFilters[self::FIELD_NAME],
      // Affiche ou non le bloc filtre - 15
      ($showFilters ? 'block' : 'none'),
      '', '', '', '', '', '', '', '','', '', '', '', '', '', '', '','', '', '', '', '', '', '', '','', '', '', '', '', '', '', '','', '', '', '', '', '', '', '',
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  /**
   * @return string
   */
  public function getExpansionFilters($expansionId='')
  {
    $selExpansionsId = explode(',', $expansionId);
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters();
    $strReturned = '';
    while (!empty($Expansions)) {
      $Expansion = array_shift($Expansions);
      if ($Expansion->getNbSurvivants()==0)
      { continue; }
      $strReturned .= '<option value="'.$Expansion->getId().'"';
      if (in_array($Expansion->getId(), $selExpansionsId)) {
        $strReturned .= ' selected';
      }
      $strReturned .= '>'.$Expansion->getName().'</option>';
    }
    return $strReturned;
  }
  /**
   * @param array $post
   */
  public function setFilters($post=null)
  { parent::setBeanFilters($post, self::FIELD_NAME); }

}
