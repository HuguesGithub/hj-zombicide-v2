<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageMissionsBean
 * @author Hugues
 * @since 1.04.01
 * @version 1.04.28
 */
class WpPageMissionsBean extends WpPageBean
{
  protected $urlTemplate = 'web/pages/public/wppage-missions.php';
  protected $urlTemplateNavPagination = 'web/pages/public/fragments/nav-pagination.php';
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
    // On récupère l'éventuel paramètre FIELD_MISSIONID
    $missionId = $this->initVar(self::FIELD_MISSIONID, -1);
    if ($missionId==-1) {
      // S'il n'est pas défini, on affiche la liste des Missions
      $this->setFilters();
      return $this->getListContentPage();
    } else {
      // S'il est défini, on affiche la Mission associée.
      $Bean = new WpPostMissionBean($missionId);
      return $Bean->getContentPage();
    }
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
        $strBody .= $Mission->getBean()->getRowForMissionsPage();
      }
    }

    // Affiche-t-on le filtre ?
    $showFilters = !empty($this->arrFilters);

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      ($this->nbperpage==10 ? self::CST_SELECTED : ''),
      ($this->nbperpage==25 ? self::CST_SELECTED : ''),
      ($this->nbperpage==50 ? self::CST_SELECTED : ''),
      //////////////////////////////////
      // Données relatives à la pagination - 4 à 10
      // N° du premier élément - 4
      $this->getNavPagination(),
      // Anciennes données. TODO à réorganiser - 5
      '', '', '', '', '', '',
      // Fin des données relatives à la pagination
      //////////////////////////////////
      // Les lignes du tableau - 11
      $strBody,
      // Affiche ou non le bloc filtre - 12
      ($showFilters ? 'block' : 'none'),
      // Si le Titre est renseigné - 13
      $this->arrFilters[self::FIELD_TITLE],
      // La liste des Difficultés - 14
      $this->getLevelFilters($this->arrFilters[self::FIELD_LEVELID]),
      // La liste des Survivants - 15
      $this->getPlayerFilters($this->arrFilters[self::FIELD_PLAYERID]),
      // La liste des Durée - 16
      $this->getDurationFilters($this->arrFilters[self::FIELD_DURATIONID]),
      // La liste des Origines - 17
      $this->getOrigineFilters($this->arrFilters[self::FIELD_ORIGINEID]),
      // Liste des Extensions (TODO éventuellement sélectionnées) - 18
      $this->getExpansionFilters($this->arrFilters[self::FIELD_EXPANSIONID]),
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  private function getNavPagination()
  {
    // On construit les liens de la pagination.
    $strPagination = $this->getPaginateLis($this->paged, $this->nbPages);

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // N° du premier élément - 1
      $this->nbperpage*($this->paged-1)+1,
      // Nb par page - 2
      min($this->nbperpage*$this->paged, $this->nbElements),
      // Nb Total - 3
      $this->nbElements,
      // Si page 1, on peut pas revenir à la première - 4
      ($this->paged==1 ? ' '.self::CST_DISABLED : ''),
      // Liste des éléments de la Pagination - 5
      $strPagination,
      // Si page $nbPages, on peut pas aller à la dernière - 6
      ($this->paged==$this->nbperpage ? ' '.self::CST_DISABLED : ''),
      // Nombre de pages - 7
      $this->nbperpage,
      // S'il n'y a qu'une page, la pagination ne sert à rien - 8
      ($this->nbPages<=1 ? ' '.self::CST_HIDDEN : ''),
    );
    return $this->getRender($this->urlTemplateNavPagination, $args);
  }
  private function getOption($value, $name, $selection=array())
  {
    $strOption = '<option value="'.$value.'"';
    if (in_array($value, $selection)) {
      $strOption .= ' selected';
    }
    return $strOption.'>'.$name.'</option>';
  }
  /**
   * @return string
   */
  public function getExpansionFilters($expansionId='')
  { return parent::getBeanExpansionFilters($expansionId, self::FIELD_NBMISSIONS); }
  /**
   * @return string
   */
  public function getLevelFilters($levelId='')
  {
    $Levels = $this->LevelServices->getLevelsWithFilters();
    $strReturned = '<option value="">Difficultés</option>';
    while (!empty($Levels)) {
      $Level = array_shift($Levels);
      $strReturned .= $this->getOption($Level->getId(), $Level->getName(), $levelId);
    }
    return $strReturned;
  }
  /**
   * @return string
   */
  public function getPlayerFilters($playerId='')
  {
    $Players = $this->PlayerServices->getPlayersWithFilters();
    $strReturned = '<option value="">Survivants</option>';
    while (!empty($Players)) {
      $Player = array_shift($Players);
      $strReturned .= $this->getOption($Player->getId(), $Player->getNbJoueurs(), $playerId);
    }
    return $strReturned;
  }
  /**
   * @return string
   */
  public function getDurationFilters($durationId='')
  {
    $Durations = $this->DurationServices->getDurationsWithFilters();
    $strReturned = '<option value="">Durées</option>';
    while (!empty($Durations)) {
      $Duration = array_shift($Durations);
      $strReturned .= $this->getOption($Duration->getId(), $Duration->getStrDuree(), $durationId);
    }
    return $strReturned;
  }
  /**
   * @return string
   */
  public function getOrigineFilters($origineId='')
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
