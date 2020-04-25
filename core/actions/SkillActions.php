<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * SkillActions
 * @author Hugues
 * @since 1.04.00
 * @version 1.04.00
 */
class SkillActions extends LocalActions
{
  /**
   * Constructeur
   */
  public function __construct($post)
  {
    parent::__construct();
    $this->post = $post;
  }
  /**
   * Point d'entrée des méthodes statiques.
   * @param array $post
   * @return string
   **/
  public static function dealWithStatic($post)
  {
    $returned = '';
    $Act = new SkillActions($post);
    switch ($post[self::CST_AJAXACTION]) {
      case self::AJAX_GETSKILLS    :
        $returned = $Act->dealWithGetSkills();
      break;
      default :
        $returned = '';
      break;
    }
    return $returned;
  }

  /**
   * Récupération du contenu de la page via une requête Ajax.
   * @param array $post
   * @return string
   */
  public function dealWithGetSkills()
  {
    $Bean = new WpPageSkillsBean();
    $Bean->setFilters($this->post);
    return $this->jsonString($Bean->getListContentPage(), self::PAGE_SKILL, true);
  }
}
