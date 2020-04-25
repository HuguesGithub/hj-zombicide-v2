<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * MissionActions
 * @author Hugues
 * @version 1.02.00
 * @since 1.02.00
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
    switch ($post[self::CST_AJAXACTION]) {
      case self::AJAX_GETMISSIONS    :
        $returned = $Act->dealWithGetMissions();
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
  public function dealWithGetMissions()
  {
    $Bean = new WpPageMissionsBean();
    $Bean->setFilters($this->post);
    return $this->jsonString($Bean->getListContentPage(), self::PAGE_MISSION, true);
  }
}
