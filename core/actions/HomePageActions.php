<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * HomePageActions
 * @author Hugues
 * @since 1.04.07
 * @version 1.05.09
 */
class HomePageActions extends LocalActions
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
    $Act = new HomePageActions($post);
    if ($post[self::CST_AJAXACTION]==self::AJAX_ADDMORENEWS) {
      $returned = $Act->dealWithGetMoreNews();
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
  public function dealWithGetMoreNews()
  {
    $Bean = new WpPageHomeBean();
    return $Bean->addMoreNews($this->post[self::ATTR_VALUE], true, false);
  }
}
