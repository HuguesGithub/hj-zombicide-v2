<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AdminPageSkillsBean
 * @author Hugues
 * @version 1.03.00
 * @since 1.00.00
 */
class AdminPageSkillsBean extends AdminPageBean
{
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct(self::CST_SKILL);
    $this->title = 'Compétences';
    $this->SkillServices = new SkillServices();
  }
  /**
   * @param array $urlParams
   * @return $Bean
   */
  public static function getStaticContentPage($urlParams)
  {
    $Bean = new AdminPageSkillsBean();
    if (!isset($urlParams[self::CST_POSTACTION])) {
      return $Bean->getListingPage();
    }
    return $Bean->returnPostActionPage($urlParams);
  }
  /**
   * @return string
   */
  public function getListingPage()
  {
    $strRows = '';
    $nbPerPage = 15;
    $curPage = $this->initVar(self::CST_CURPAGE, 1);
    $orderby = $this->initVar(self::CST_ORDERBY, self::CST_NAME);
    $order = $this->initVar(self::CST_ORDER, 'ASC');
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
    $queryArg = array(self::CST_ONGLET=>self::CST_SKILL,
      self::CST_ORDERBY=>$orderby,
      self::CST_ORDER=>$order
    );
    // Pagination
    $strPagination = $this->getPagination($queryArg, $post_status, $curPage, $nbPages, $nbElements);
    // Sorts
    $queryArg[self::CST_ORDERBY] = 'code';
    $queryArg[self::CST_ORDER] = ($orderby=='code' && $order=='asc' ? 'desc' : 'asc');
    $urlSortCode = $this->getQueryArg($queryArg);
    $queryArg[self::CST_ORDERBY] = self::CST_NAME;
    $queryArg[self::CST_ORDER] = ($orderby==self::CST_NAME && $order=='asc' ? 'desc' : 'asc');
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
      ($orderby=='code' ? $order : 'desc'),
      // url pour le tri sur code - 7
      $urlSortCode,
      // class pour le tri sur title - 8
      ($orderby==self::CST_NAME ? $order : 'desc'),
      // url pour le tri sur title - 9
      $urlSortTitle,
      '','','','','','','','','','','','','');
    $str = file_get_contents(PLUGIN_PATH.'web/pages/admin/skill-listing.php');
    return vsprintf($str, $args);
  }
  /**
   * @return string
   */
  public function getAddPage()
  {
    $Skill = new Skill();
    if (isset($_POST) && !empty($_POST)) {
      $doInsert = $Skill->updateWithPost($_POST);
      if ($doInsert) {
        $this->SkillServices->insert(__FILE__, __LINE__, $Skill);
        $Skill->setId(MySQL::getLastInsertId());
        return $this->getAddEditPage($Skill);
      }
    }
    return $this->getAddEditPage($Skill, 'Ajouter une Compétence', 'add');
  }
  /**
   * @return string
   */
  public function getClonePage($skillId)
  {
    $Skill = $this->SkillServices->select(__FILE__, __LINE__, $skillId);
    $Skill->setId('');
    if (isset($_POST) && !empty($_POST)) {
      $doUpdate = $Skill->updateWithPost($_POST);
      if ($doUpdate) {
        $this->SkillServices->insert(__FILE__, __LINE__, $Skill);
        $Skill->setId(MySQL::getLastInsertId());
        return $this->getAddEditPage($Skill);
      }
    }
    return $this->getAddEditPage($Skill, 'Créer une Compétence', 'add');
  }
  /**
   * @return string
   */
  public function getEditPage($skillId)
  {
    $Skill = $this->SkillServices->select(__FILE__, __LINE__, $skillId);
    if (isset($_POST) && !empty($_POST)) {
      $doUpdate = $Skill->updateWithPost($_POST);
      if ($doUpdate) {
        $this->SkillServices->update(__FILE__, __LINE__, $Skill);
      }
    }
    return $this->getAddEditPage($Skill);
  }
  /**
   * @param Skill $Skill
   * @param string $title
   * @param string $postAction
   * @return string
   */
  public function getAddEditPage($Skill, $title='Editer une Compétence', $postAction='edit')
  {
    $args = array(
      // Nom de l'interface - 1
      $title,
      // Url de l'action - 2
      '#',
      // Url pour Annuler - 3
      $this->getQueryArg(array(self::CST_ONGLET=>self::CST_SKILL)),
      // Id de la Compétence - 4
      $Skill->getId(),
      // Type de l'action - 5
      $postAction,
      // Nom de la Compétence - 6
      $Skill->getName(),
      // Code de la Compétence - 7
      $Skill->getCode(),
      // Description de la Compétence - 8
      $Skill->getDescription(),
      '','','','','','','','','','','','','','',
    );
    $str = file_get_contents(PLUGIN_PATH.'web/pages/admin/skill-edit.php');
    return vsprintf($str, $args);
  }
  /**
   * Affiche l'interface de confirmation de suppression. Supprime la Compétence si suppression confirmée.
   * @param int|null $skillId Identifiant de la Compétence à supprimer.
   * @return string
   */
  public function getTrashPage($skillId=null)
  {
    // Si on confirme la suppression, on le fait et on affiche la nouvelle liste
    if (isset($_POST) && !empty($_POST) && !empty($_POST['skillIds'])) {
      foreach ($_POST['skillIds'] as $skillId) {
        $Skill = $this->SkillServices->select(__FILE__, __LINE__, $skillId);
        $this->SkillServices->delete(__FILE__, __LINE__, $Skill);
      }
      return $this->getListingPage();
    }
    // On affiche l'interface de confirmation de suppression avant de commettre l'irrémédiable.
    $Skill = $this->SkillServices->select(__FILE__, __LINE__, $skillId);
    // Préparation des variables pour la mutualisation de l'interface de suppression
    $title = 'Suppression de compétences';
    $subTitle = 'la compétence suivante';
    $strLis  = '<li><input type="hidden" name="skillIds[]" value="'.$skillId.'"/>'.$Skill->getName().'</li>';
    $urlCancel = $this->getQueryArg(array(self::CST_ONGLET=>self::CST_SKILL));
    return $this->getConfirmDeletePage($title, $subTitle, $strLis, $urlCancel);
  }
  /**
   * Affiche l'interface de confirmation de suppression. Supprime la Compétence si suppression confirmée.
   * @return string
   */
  public function getBulkTrashPage()
  {
    if (isset($_POST['post']) && !empty($_POST['post'])) {
      if (count($_POST['post'])==1) {
        return $this->getTrashPage($_POST['post'][0]);
      } else {
        $title = 'Suppression de compétences';
        $subTitle = 'les compétences suivantes';
        $urlCancel = $this->getQueryArg(array(self::CST_ONGLET=>self::CST_SKILL));
        $strLis  = '';
        foreach ($_POST['post'] as $value) {
          $Skill = $this->SkillServices->select(__FILE__, __LINE__, $value);
          $strLis .= '<li><input type="hidden" name="skillIds[]" value="'.$value.'"/>'.$Skill->getName().'</li>';
        }
        return $this->getConfirmDeletePage($title, $subTitle, $strLis, $urlCancel);
      }
    } else {
      return $this->getListingPage();
    }
  }

}
