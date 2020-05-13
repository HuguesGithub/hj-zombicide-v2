<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageSkillsBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.05.01
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
    $this->nbElements = count($Skills);
    $this->nbPages = ceil($this->nbElements/$this->nbperpage);
    // On slice la liste pour n'avoir que celles à afficher
    $displayedSkills = array_slice($Skills, $this->nbperpage*($this->paged-1), $this->nbperpage);
    // On construit le corps du tableau
    $strBody = '';
    if (!empty($displayedSkills)) {
      foreach ($displayedSkills as $Skill) {
        $strBody .= $Skill->getBean()->getRowForPublicPage();
      }
    }
    /////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////
    // Affiche-t-on le filtre ?
    $showFilters = (isset($this->arrFilters[self::FIELD_DESCRIPTION])&&$this->arrFilters[self::FIELD_DESCRIPTION]!='');
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
      // Filtre sur la Description - 5
      $this->arrFilters[self::FIELD_DESCRIPTION],
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  /**
   * @param array $post
   */
  public function setFilters($post=null)
  { parent::setBeanFilters($post, self::FIELD_NAME); }

}
