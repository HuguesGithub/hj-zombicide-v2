<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AdminPageExpansionsBean
 * @author Hugues
 * @since 1.04.30
 * @version 1.05.12
 */
class AdminPageExpansionsBean extends AdminPageBean
{
  protected $tplHomeCheckCard  = 'web/pages/admin/fragments/home-check-card.php';
  protected $urlExpansionListing = 'web/pages/admin/expansion-listing.php';
  protected $urlAdminEdit = 'web/pages/admin/expansion-edit.php';
  /**
   * Class Constructor
   */
  public function __construct($urlParams='')
  {
    $this->urlParams = $urlParams;
    parent::__construct(self::CST_EXPANSION);
    $this->title = 'Extensions';
    $this->ExpansionServices  = new ExpansionServices();
    $this->MissionServices    = new MissionServices();
    $this->SurvivorServices   = new SurvivorServices();
  }
  /**
   * @param array $urlParams
   * @return $Bean
   */
  public function getSpecificContentPage()
  {
    if (isset($this->urlParams[self::FIELD_ID])) {
      $this->Expansion = $this->ExpansionServices->selectExpansion($this->urlParams[self::FIELD_ID]);
    }
    if (isset($_POST)&&!empty($_POST)) {
      $this->dealWithPost();
    }
    switch ($this->urlParams[self::CST_POSTACTION]) {
      case 'confirmEdit'  :
      case self::CST_EDIT :
        return $this->getEditContentPage();
      break;
      default :
        return $this->getListContentPage();
      break;
    }
  }
  private function dealWithPost()
  {
    if ($this->urlParams[self::CST_POSTACTION]=='confirmEdit') {
      // On ne met à jour via cette interface que les données suivantes :
      // Le nombre de Survivants.
      $this->Expansion->setNbSurvivants($_POST[self::FIELD_NBSURVIVANTS]);
      // Le nombre de Missions.
      $this->Expansion->setNbMissions($_POST[self::FIELD_NBMISSIONS]);
      // Une fois fait, on peut sauvegarder les modifications.
      $this->ExpansionServices->updateExpansion($this->Expansion);
    }
  }
  public function getEditContentPage()
  {
    $args = array(
      self::FIELD_EXPANSIONID => $this->Expansion->getId(),
    );
    // Nombre de Survivants attendus
    $ExpectedSurvivors = $this->SurvivorServices->getSurvivorsWithFilters($args);
    $nbExpectedSurvivors = count($ExpectedSurvivors);

    // Nb de Missions attendues
    $ExpectedMissions = $this->MissionServices->getMissionsByExpansionId($this->Expansion->getId());
    $nbExpectedMissions = count($ExpectedMissions);

    //////////////////////////////////////////////////////////////////////////
    // On enrichit le template
    $args = array(
    // L'identifiant de l'extension - 1
    $this->Expansion->getId(),
    // Le code de l'extension - 2
    $this->Expansion->getCode(),
    // Le nom de l'extension - 3
    $this->Expansion->getName(),
    // Le rang d'affichage de l'extension - 4
    $this->Expansion->getDisplayRank(),
    // Le nombre de Survivants de l'extension - 5
    $this->Expansion->getNbSurvivants(),
    // Le nombre théorique de Survivants de l'extension - 6
    $nbExpectedSurvivors,
    // Le nombre de Missions de l'extension - 7
    $this->Expansion->getNbMissions(),
    // Le nombre théorique de Missions - 8
    $nbExpectedMissions,
    // L'extension est-elle officielle ? - 9
    ($this->Expansion->isOfficial() ? self::CST_CHECKED : ''),
    );
    // Puis on le restitue.
    return $this->getRender($this->urlAdminEdit, $args);
  }

  public function getListContentPage()
  {
    $strRows = '';
    $nbPerPage = 15;
    $curPage = $this->initVar(self::WP_CURPAGE, 1);
    $orderby = $this->initVar(self::WP_ORDERBY, self::FIELD_NAME);
    $order = $this->initVar(self::WP_ORDER, self::ORDER_ASC);
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(), $orderby, $order);
    $nbElements = count($Expansions);
    $nbPages = ceil($nbElements/$nbPerPage);
    $curPage = max(1, min($curPage, $nbPages));
    $DisplayedExpansions = array_slice($Expansions, ($curPage-1)*$nbPerPage, $nbPerPage);
    if (!empty($DisplayedExpansions)) {
      foreach ($DisplayedExpansions as $Expansion) {
        $ExpansionBean = new ExpansionBean($Expansion);
        $strRows .= $ExpansionBean->getRowForAdminPage();
      }
    }
    $queryArg = array(
      self::CST_ONGLET => self::CST_EXPANSION,
      self::WP_ORDERBY => $orderby,
      self::WP_ORDER   => $order
    );
    // Pagination
    $strPagination = $this->getPagination($queryArg, $post_status, $curPage, $nbPages, $nbElements);

    $args = array(
      // Liste des extensions affichées - 1
      $strRows,
      // Filtres - 2
      '',
      // Url pour créer une nouvelle Extension - 3
      '/wp-admin/post-new.php',
      // Subs - 4
      '',
      // Pagination - 5
      $strPagination,
    );
    return $this->getRender($this->urlExpansionListing, $args);
  }

  /**
   * @return string
   */
  public function getCheckCard()
  {
    /////////////////////////////////////////////////
    // Gestion des Extensions.
    // On récupère la liste des Extensions qui ont un Article. Puis les données dans la base. On compare et on effectue un diagnostic.
    $Act = new ExpansionActions();
    $strBilan  = $Act->dealWithExpansionVerif();

    $args = array(
      // Le titre de la carte - 1
      $this->title,
      // L'id du container de retour pour afficher les vérifications - 2
      self::CST_EXPANSION,
      // Le contenu du container de vérification - 3
      $strBilan,
   );
    return $this->getRender($this->tplHomeCheckCard, $args);
  }
}
