<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageSkillsBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.04.28
 */
class WpPageSkillsBean extends WpPageBean
{
  protected $urlTemplate = 'web/pages/public/wppage-skills.php';
  /**
   * Class Constructor
   * @param WpPage $WpPage
   */
  public function __construct($WpPage='')
  {
    parent::__construct($WpPage);
    $this->SkillServices = new SkillServices();
  }
  /**
   * On vérifie si on est ici pour traiter la page des compétences, ou une compétence en particulier.
   * Pour le cas d'une compétence, on retourne une WpPostSkillBean.
   * @return string
   */
  public function getContentPage()
  {
    // On récupère l'éventuel paramètre FIELD_SKILLID
    $skillId = $this->initVar(self::FIELD_SKILLID, -1);
    if ($skillId==-1) {
      // S'il n'est pas défini, on affiche la liste des compétences
      $this->setFilters();
      return $this->getListContentPage();
    } else {
      // S'il est défini, on affiche la compétence associée.
      $Bean = new WpPostSkillBean($skillId);
      return $Bean->getContentPage();
    }
  }
  /**
   * @return string
   */
  public function getListContentPage()
  {
    /////////////////////////////////////////////////////////////////////////////
    // On récupère la liste de compétences puis les éléments nécessaires à la pagination.
    $Skills = $this->SkillServices->getSkillsWithFilters($this->arrFilters, $this->colSort, $this->colOrder);
    $nbElements = count($Skills);
    $nbPages = ceil($nbElements/$this->nbperpage);
    // On slice la liste pour n'avoir que celles à afficher
    $displayedSkills = array_slice($Skills, $this->nbperpage*($this->paged-1), $this->nbperpage);
    // On construit le corps du tableau
    $strBody = '';
    if (!empty($displayedSkills)) {
      foreach ($displayedSkills as $Skill) {
        $strBody .= $Skill->getBean()->getRowForSkillsPage();
      }
    }

    // On construit les liens de la pagination.
    $strPagination = $this->getPaginateLis($this->paged, $nbPages);

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      ($this->nbperpage==10 ? self::CST_SELECTED : ''),
      ($this->nbperpage==25 ? self::CST_SELECTED : ''),
      ($this->nbperpage==50 ? self::CST_SELECTED : ''),
      // Tri sur le Code - 4
      ($this->colSort==self::FIELD_CODE ? $this->colOrder : ''),
      // Tri sur le Nom - 5
      ($this->colSort==self::FIELD_NAME && $this->colOrder==self::ORDER_ASC ? self::ORDER_ASC : self::ORDER_DESC),
      // Les lignes du tableau - 6
      $strBody,
      // N° du premier élément - 7
      $this->nbperpage*($this->paged-1)+1,
      // Nb par page - 8
      min($this->nbperpage*$this->paged, $nbElements),
      // Nb Total - 9
      $nbElements,
      // Liste des éléments de la Pagination - 10
      $strPagination,
      // Si page 1, on peut pas revenir à la première
      ($this->paged==1 ? ' '.self::CST_DISABLED : ''),
      // Si page $nbPages, on peut pas aller à la dernière
      ($this->paged==$this->nbperpage ? ' '.self::CST_DISABLED : ''),
      // Nombre de pages - 13
      $nbPages,
      // Filtre sur la Description - 14
      $this->arrFilters[self::FIELD_DESCRIPTION],
      // Affiche ou non le bloc filtre - 15
      (isset($this->arrFilters[self::FIELD_DESCRIPTION])&&$this->arrFilters[self::FIELD_DESCRIPTION]!='' ? 'block' : 'none'),
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  /**
   * @param array $post
   */
  public function setFilters($post=null)
  { parent::setBeanFilters($post, self::FIELD_NAME); }

}
