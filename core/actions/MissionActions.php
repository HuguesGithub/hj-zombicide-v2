<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * MissionActions
 * @author Hugues
 * @since 1.02.00
 * @version 1.04.27
 */
class MissionActions extends LocalActions
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
    $Act = new MissionActions($post);
    if ($post[self::CST_AJAXACTION]==self::AJAX_GETMISSIONS) {
      $returned = $Act->dealWithGetMissions();
    } else {
      $returned = '';
    }
    return $returned;
  }

  /**
   * Récupération du contenu de la page via une requête Ajax.
   * @param array $post
   * @return string
   */
  public function dealWithGetMissions()
  {
    $Bean = new WpPageMissionsBean();
    $Bean->setFilters($this->post);
    return $this->jsonString($Bean->getListContentPage(), self::PAGE_MISSION, true);
  }
}
