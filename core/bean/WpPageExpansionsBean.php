<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageExpansionsBean
 * @author Hugues
 * @since 1.07.21
 * @version 1.07.21
 */
class WpPageExpansionsBean extends WpPageBean
{
  protected $urlTemplate = 'web/pages/public/wppage-expansions.php';
  /**
   * Class Constructor
   * @param WpPage $WpPage
   */
  public function __construct($WpPage='')
  {
    parent::__construct($WpPage);
    $this->ExpansionServices = new ExpansionServices();
  }
  /**
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
    // On récupère la liste des Extensions puis les éléments nécessaires à la pagination.
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters($this->arrFilters, $this->colSort, $this->colOrder);
    $this->nbElements = count($Expansions);
    $this->nbPages = ceil($this->nbElements/$this->nbperpage);
    // On slice la liste pour n'avoir que ceux à afficher
    $displayedExpansions = array_slice($Expansions, $this->nbperpage*($this->paged-1), $this->nbperpage);
    // On construit le corps du tableau
    $strBody = '';
    if (!empty($displayedExpansions)) {
      foreach ($displayedExpansions as $Expansion) {
        $strBody .= $Expansion->getBean()->getRowForPublicPage();
      }
    }
    /////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////
    // Affiche-t-on le filtre ?
    $showFilters = isset($this->arrFilters[self::FIELD_NAME])&&$this->arrFilters[self::FIELD_NAME]!='';
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
      '','','','','','','','','','','','','','','','','',''
    );
    return $this->getRender($this->urlTemplate, $args);
  }

  /**
   * @param array $post
   */
  public function setFilters($post=null)
  { parent::setBeanFilters($post, self::FIELD_NAME); }

}
