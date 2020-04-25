<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * SurvivorActions
 * @author Hugues
 * @since 1.04.00
 * @version 1.04.24
 */
class SurvivorActions extends LocalActions
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
    $Act = new SurvivorActions($post);
    switch ($post[self::CST_AJAXACTION]) {
      case self::AJAX_GETSURVIVORS    :
        $returned = $Act->dealWithGetSurvivors();
      break;
      case self::AJAX_GETRANDOMTEAM :
        $returned = $Act->dealWithGetRandomTeam();
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
  public function dealWithGetSurvivors()
  {
    $Bean = new WpPageSurvivorsBean();
    $Bean->setFilters($this->post);
    return $this->jsonString($Bean->getListContentPage(), self::PAGE_SURVIVOR, true);
  }

  /**
   * @return string;
   */
  public function dealWithGetRandomTeam()
  {
    $Bean = new WpPageSurvivorsBean();
    return $this->jsonString($Bean->getRandomTeam($this->post), self::PAGE_SELECT_SURVIVORS, true);
  }
}
