<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageSpawnsBean
 * @author Hugues
 * @since 1.0.00
 * @version 1.0.00
 */
class WpPageSpawnsBean extends WpPageBean
{
  protected $urlTemplate = 'web/pages/public/wppage-spawncards.php';
  /**
   * Class Constructor
   * @param WpPage $WpPage
   */
  public function __construct($WpPage='')
  {
    parent::__construct($WpPage);
    $this->ExpansionServices = new ExpansionServices();
    $this->SpawnServices     = new SpawnServices();
  }
  /**
   * @return string
   */
  public function getContentPage()
  { return $this->getListContentPage(); }
  /**
   * Retourne la liste des cartes Invasions
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
      // On récupère les cartes Invasions relatives à l'extension. S'il n'y en a pas, on passe à l'extension suivante.
      $SpawnCards = $this->SpawnServices->getSpawnsWithFilters(array(self::FIELD_EXPANSIONID=>$Expansion->getId()));
      if (empty($SpawnCards)) {
        continue;
      }
      // On en profite aussi pour construire le bloc de filtres.
      $strFilters .= $this->getBalise(self::TAG_OPTION, $Expansion->getName(), array(self::ATTR_VALUE => 'set-'.$Expansion->getId()));
      // On ajoute chaque carte Invasion à la liste à afficher.
      foreach ($SpawnCards as $SpawnCard) {
        $strSpawns .= $SpawnCard->getBean()->displayCard();
      }
    }

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // La liste des cartes - 1
      $strSpawns,
      // Les filtres disponibles - 2
      $strFilters,
    );
    return $this->getRender($this->urlTemplate, $args);
  }
}
