<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageMissionsBean
 * @author Hugues
 * @since 1.04.01
 * @version 1.05.01
 */
class WpPageMissionsBean extends WpPageBean
{
  protected $urlTemplate = 'web/pages/public/wppage-missions.php';
  /**
   * Class Constructor
   * @param WpPage $WpPage
   */
  public function __construct($WpPage='')
  {
    parent::__construct($WpPage);
    $this->DurationServices  = new DurationServices();
    $this->ExpansionServices = new ExpansionServices();
    $this->LevelServices     = new LevelServices();
    $this->MissionServices   = new MissionServices();
    $this->OrigineServices   = new OrigineServices();
    $this->PlayerServices    = new PlayerServices();
  }
  /**
   * On vérifie si on est ici pour traiter la page des Missions, ou une Mission en particulier.
   * Pour le cas d'une Mission, on retourne une WpPostMissionBean.
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
    // On récupère la liste des Missions puis les éléments nécessaires à la pagination.
    $Missions = $this->MissionServices->getMissionsWithFilters($this->arrFilters, $this->colSort, $this->colOrder);
    $this->nbElements = count($Missions);
    $this->nbPages = ceil($this->nbElements/$this->nbperpage);
    // On slice la liste pour n'avoir que ceux à afficher
    $displayedMissions = array_slice($Missions, $this->nbperpage*($this->paged-1), $this->nbperpage);
    // On construit le corps du tableau
    $strBody = '';
    if (!empty($displayedMissions)) {
      foreach ($displayedMissions as $Mission) {
        $strBody .= $Mission->getBean()->getRowForPublicPage();
      }
    }
    /////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////
    // Affiche-t-on le filtre ?
    $showFilters = !empty($this->arrFilters);
    /////////////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // On affiche les lignes du tableau - 1
      $strBody,
      // On affiche le dropdown par pages - 2
      $this->getDropdownNbPerPages(),
      // On affiche la pagination - 3
      $this->getNavPagination(),
      // Affiche ou non le bloc filtre - 4
      ($showFilters ? 'block' : 'none'),
      // Si le Titre est renseigné - 5
      $this->arrFilters[self::FIELD_TITLE],
      // La liste des Difficultés - 6
      $this->getLevelFilters($this->arrFilters[self::FIELD_LEVELID]),
      // La liste des Survivants - 7
      $this->getPlayerFilters($this->arrFilters[self::FIELD_PLAYERID]),
      // La liste des Durée - 8
      $this->getDurationFilters($this->arrFilters[self::FIELD_DURATIONID]),
      // La liste des Origines - 9
      $this->getOrigineFilters($this->arrFilters[self::FIELD_ORIGINEID]),
      // Liste des Extensions - 10
      $this->getExpansionFilters($this->arrFilters[self::FIELD_EXPANSIONID]),
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  private function getExpansionFilters($expansionId='')
  { return parent::getBeanExpansionFilters($expansionId, self::FIELD_NBMISSIONS); }
  private function getLevelFilters($levelId='')
  {
    $Levels = $this->LevelServices->getLevelsWithFilters();
    $strReturned = '<option value="">Difficultés</option>';
    while (!empty($Levels)) {
      $Level = array_shift($Levels);
      $strReturned .= $this->getOption($Level->getId(), $Level->getName(), $levelId);
    }
    return $strReturned;
  }
  private function getPlayerFilters($playerId='')
  {
    $Players = $this->PlayerServices->getPlayersWithFilters();
    $strReturned = '<option value="">Survivants</option>';
    while (!empty($Players)) {
      $Player = array_shift($Players);
      $strReturned .= $this->getOption($Player->getId(), $Player->getNbJoueurs(), $playerId);
    }
    return $strReturned;
  }
  private function getDurationFilters($durationId='')
  {
    $Durations = $this->DurationServices->getDurationsWithFilters();
    $strReturned = '<option value="">Durées</option>';
    while (!empty($Durations)) {
      $Duration = array_shift($Durations);
      $strReturned .= $this->getOption($Duration->getId(), $Duration->getStrDuree(), $durationId);
    }
    return $strReturned;
  }
  private function getOrigineFilters($origineId='')
  {
    $Origines = $this->OrigineServices->getOriginesWithFilters();
    $strReturned = '<option value="">Origine</option>';
    while (!empty($Origines)) {
      $Origine = array_shift($Origines);
      $strReturned .= $this->getOption($Origine->getId(), $Origine->getName(), $origineId);
    }
    return $strReturned;
  }
  /**
   * @param array $post
   */
  public function setFilters($post=null)
  { parent::setBeanFilters($post, self::FIELD_TITLE); }
}
