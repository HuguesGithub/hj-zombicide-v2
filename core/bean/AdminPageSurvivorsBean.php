<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AdminPageSurvivorsBean
 * @author Hugues
 * @since 1.05.01
 * @version 1.05.07
 */
class AdminPageSurvivorsBean extends AdminPageBean
{
  protected $urlFragmentSurvSkillTabContent = 'web/pages/admin/fragments/fragment-survivor-skills-tabcontent.php';
  protected $tplHomeCheckCard  = 'web/pages/admin/fragments/home-check-card.php';
  protected $urlSurvivorListing = 'web/pages/admin/survivor-listing.php';
  protected $urlSurvivorEdit = 'web/pages/admin/survivor-edit.php';
  /**
   * Class Constructor
   */
  public function __construct($urlParams='')
  {
    $this->urlParams = $urlParams;
    parent::__construct(self::CST_SURVIVOR);
    $this->title = 'Survivants';
    $this->SkillServices = new SkillServices();
    $this->SurvivorServices  = new SurvivorServices();
    $this->SurvivorSkillServices = new SurvivorSkillServices();
  }
  /**
   * @param array $urlParams
   * @return $Bean
   */
  public function getSpecificContentPage()
  {
    if (isset($this->urlParams[self::FIELD_ID])) {
      $this->Survivor = $this->SurvivorServices->selectSurvivor($this->urlParams[self::FIELD_ID]);
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
      // Faudrait supprimer les données existantes.
      $SurvivorSkill = new SurvivorSkill();
      $SurvivorSkill->setSurvivorId($this->Survivor->getId());
      $this->SurvivorSkillServices->deleteBulkSurvivorSkill(array(self::FIELD_SURVIVORID=>$this->Survivor->getId()));
      foreach ($_POST as $key=>$value) {
        list($check, $survivorTypeId, $tagLevelId) = explode('_', $key);
        if ($check!='ss' || $value=='') {
          continue;
        }
        $SurvivorSkill->setSkillId($value);
        $SurvivorSkill->setSurvivorTypeId($survivorTypeId);
        $SurvivorSkill->setTagLevelId($tagLevelId);
        $this->SurvivorSkillServices->insertSurvivorSkill($SurvivorSkill);
      }
    }
  }
  public function getListContentPage()
  {
    $strRows = '';
    $nbPerPage = 10;
    $curPage = $this->initVar(self::WP_CURPAGE, 1);
    $orderby = $this->initVar(self::WP_ORDERBY, self::FIELD_NAME);
    $order = $this->initVar(self::WP_ORDER, self::ORDER_ASC);
    $Survivors = $this->SurvivorServices->getSurvivorsWithFilters(array(), $orderby, $order);
    $nbElements = count($Survivors);
    $nbPages = ceil($nbElements/$nbPerPage);
    $curPage = max(1, min($curPage, $nbPages));
    $DisplayedSurvivors = array_slice($Survivors, ($curPage-1)*$nbPerPage, $nbPerPage);
    while (!empty($DisplayedSurvivors)) {
      $Survivor = array_shift($DisplayedSurvivors);
      $strRows .= $Survivor->getBean()->getRowForAdminPage();
    }
    $queryArg = array(
      self::CST_ONGLET => self::CST_SURVIVOR,
      self::WP_ORDERBY => $orderby,
      self::WP_ORDER   => $order
    );
    // Pagination
    $strPagination = $this->getPagination($queryArg, $post_status, $curPage, $nbPages, $nbElements);
    // Tris
    $queryArg[self::WP_ORDERBY] = self::FIELD_NAME;
    $queryArg[self::WP_ORDER] = ($orderby==self::FIELD_NAME && $order==self::ORDER_ASC ? self::ORDER_DESC : self::ORDER_ASC);
    $urlSortTitle = $this->getQueryArg($queryArg);

    $args = array(
      // Liste des survivants affichés - 1
      $strRows,
      // 2
      '',
      // Lien pour ajouter un nouveau Survivant - 3
      '/wp-admin/post-new.php',
      // 4
      '',
      // Pagination - 5
      $strPagination,
      // 6 & 7
      '','',
      // class pour le tri sur title - 8
      ($orderby==self::FIELD_NAME ? $order : self::ORDER_DESC),
      // url pour le tri sur title - 9
      $urlSortTitle,
      '','','','','','','','','','','','','','','','','','','','','','',''
    );
    return $this->getRender($this->urlSurvivorListing, $args);
  }

  private function getOption($value, $name, $selection='')
  { return '<option value="'.$value.'"'.($value==$selection ? ' selected' : '').'>'.$name.'</option>'; }

  private function getSkillSelect($name, $id='')
  {
    $strReturned = $this->getOption('', 'Aucune', $id);
    $Skills = $this->Skills;
    while (!empty($Skills)) {
      $Skill = array_shift($Skills);
      $strReturned .= $this->getOption($Skill->getId(), $Skill->getName(), $id);
    }
    return '<select name="'.$name.'">'.$strReturned.'</select>';
  }
  public function getEditContentPage()
  {
    $msgError = "Ce Survivant n'a pas de profil de ce type. Il n'est donc pas possible de sélectionner des compétences.";
    $this->Skills = $this->SkillServices->getSkillsWithFilters();

    $args = array(
    // Le Survivant a-t-il un profil Standard ? - 1
    (!$this->Survivor->isStandard() ? $msgError : $this->getListSelects(self::CST_SURVIVORTYPEID_S)),
    // Le Survivant a-t-il un profil Zombivant ? - 2
    (!$this->Survivor->isZombivor() ? $msgError : $this->getListSelects(self::CST_SURVIVORTYPEID_Z)),
    // Le Survivant a-t-il un profil Ultimate ? - 3
    (!$this->Survivor->isUltimate() ? $msgError : $this->getListSelects(self::CST_SURVIVORTYPEID_U)),
    // Le Survivant a-t-il un profil Ultimate Zombivant ? - 4
    (!$this->Survivor->isUltimatez() ? $msgError : $this->getListSelects(self::CST_SURVIVORTYPEID_UZ)),
    // L'identifiant du Survivant - 5
    $this->Survivor->getId(),
    // Le nom du Survivant - 6
    $this->Survivor->getName(),
    // A-t-il un profil Standard ? - 7
    ($this->Survivor->isStandard() ? ' '.self::CST_CHECKED : ''),
    // A-t-il un profil Standard ? - 8
    ($this->Survivor->isZombivor() ? ' '.self::CST_CHECKED : ''),
    // A-t-il un profil Standard ? - 9
    ($this->Survivor->isUltimate() ? ' '.self::CST_CHECKED : ''),
    // A-t-il un profil Standard ? - 10
    ($this->Survivor->isUltimatez() ? ' '.self::CST_CHECKED : ''),
    // Extension d'origine du Survivant - 11
    $this->Survivor->getExpansion()->getName(),
    );

    return $this->getRender($this->urlSurvivorEdit, $args);
  }
  private function getListSelects($survivorTypeId)
  {
    $tagLevelIds = array('10', '11', '20', '30', '31', '40', '41', '42');
    $args = array();
    while (!empty($tagLevelIds)) {
      $levelId = array_shift($tagLevelIds);
      $name = 'ss_'.$survivorTypeId.'_'.$levelId;
      array_push($args, $this->getSkillSelect($name, $this->Survivor->getSkill($survivorTypeId, $levelId)->getId()));
    }
    return  $this->getRender($this->urlFragmentSurvSkillTabContent, $args);
  }

  /**
   * @return string
   */
  public function getCheckCard()
  {
    /////////////////////////////////////////////////
    // Gestion des Survivants.
    // On récupère la liste des Survivants qui ont un Article. Puis les données dans la base. On compare et on effectue un diagnostic.
    $Act = new SurvivorActions();
    $strBilan  = $Act->dealWithSurvivorVerif();

    $args = array(
      // Le titre de la carte - 1
      $this->title,
      // L'id du container de retour pour afficher les vérifications - 2
      self::CST_SURVIVOR,
      // Le contenu du container de vérification - 3
      $strBilan,
   );
    return $this->getRender($this->tplHomeCheckCard, $args);
  }
}
