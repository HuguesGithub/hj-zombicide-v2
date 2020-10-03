<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageTilesBean
 * @author Hugues
 * @since 1.08.30
 */
class WpPageTilesBean extends WpPageBean
{
  protected $urlTemplate = 'web/pages/public/wppage-tiles.php';
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
  { return $this->getListContentPage(); }
  /**
   * Retourne la liste des dalles
   * @return string
   */
  public function getListContentPage()
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
      // Les filtres disponibles - 1
      $strFilters,
    );
    return $this->getRender($this->urlTemplate, $args);
  }
}
