<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AdminPageSkillsBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.04.30
 */
class AdminPageSkillsBean extends AdminPageBean
{
  protected $tplHomeCheckCard  = 'web/pages/admin/fragments/home-check-card.php';
  protected $urlSkillListing = 'web/pages/admin/skill-listing.php';
  /**
   * Class Constructor
   */
  public function __construct($urlParams='')
  {
    $this->urlParams = $urlParams;
    parent::__construct(self::CST_SKILL);
    $this->title = 'Compétences';
    $this->SkillServices  = new SkillServices();
  }
  /**
   * @param array $urlParams
   * @return $Bean
   */
  public function getSpecificContentPage()
  {
    $strRows = '';
    $nbPerPage = 15;
    $curPage = $this->initVar(self::WP_CURPAGE, 1);
    $orderby = $this->initVar(self::WP_ORDERBY, self::FIELD_NAME);
    $order = $this->initVar(self::WP_ORDER, self::ORDER_ASC);
    $Skills = $this->SkillServices->getSkillsWithFilters(array(), $orderby, $order);
    $nbElements = count($Skills);
    $nbPages = ceil($nbElements/$nbPerPage);
    $curPage = max(1, min($curPage, $nbPages));
    $DisplayedSkills = array_slice($Skills, ($curPage-1)*$nbPerPage, $nbPerPage);
    if (!empty($DisplayedSkills)) {
      foreach ($DisplayedSkills as $Skill) {
        $SkillBean = new SkillBean($Skill);
        $strRows .= $SkillBean->getRowForAdminPage();
      }
    }
    $queryArg = array(
      self::CST_ONGLET => self::CST_SKILL,
      self::WP_ORDERBY => $orderby,
      self::WP_ORDER   => $order
    );
    // Pagination
    $strPagination = $this->getPagination($queryArg, $post_status, $curPage, $nbPages, $nbElements);
    // Sorts
    $queryArg[self::WP_ORDERBY] = self::FIELD_CODE;
    $queryArg[self::WP_ORDER] = ($orderby==self::FIELD_CODE && $order==self::ORDER_ASC ? self::ORDER_DESC : self::ORDER_ASC);
    $urlSortCode = $this->getQueryArg($queryArg);
    $queryArg[self::WP_ORDERBY] = self::FIELD_NAME;
    $queryArg[self::WP_ORDER] = ($orderby==self::FIELD_NAME && $order==self::ORDER_ASC ? self::ORDER_DESC : self::ORDER_ASC);
    $urlSortTitle = $this->getQueryArg($queryArg);
    $args = array(
      // Liste des compétences affichées - 1
      $strRows,
      // Filtres - 2
      '',
      // Url pour créer une nouvelle Compétence - 3
      $this->getQueryArg(array(self::CST_ONGLET=>self::CST_SKILL, self::CST_POSTACTION=>'add')),
      // Subs - 4
      '',
      // Pagination - 5
      $strPagination,
      // class pour le tri sur code - 6
      ($orderby==self::FIELD_CODE ? $order : self::ORDER_DESC),
      // url pour le tri sur code - 7
      $urlSortCode,
      // class pour le tri sur title - 8
      ($orderby==self::FIELD_NAME ? $order : self::ORDER_DESC),
      // url pour le tri sur title - 9
      $urlSortTitle,
      '','','','','','','','','','','','',''
    );
    return $this->getRender($this->urlSkillListing, $args);
  }

  /**
   * @return string
   */
  public function getCheckCard()
  {
    /////////////////////////////////////////////////
    // Gestion des Compétences.
    // On récupère la liste des Compétences qui ont un Article. Puis les données dans la base. On compare et on effectue un diagnostic.
    $Act = new SkillActions();
    $strBilan  = $Act->dealWithSkillVerif();

    $args = array(
      // Le titre de la carte - 1
      $this->title,
      // L'id du container de retour pour afficher les vérifications - 2
      self::CST_SKILL.'-verif',
      // Le contenu du container de vérification - 3
      $strBilan,
   );
    return $this->getRender($this->tplHomeCheckCard, $args);
  }
}
