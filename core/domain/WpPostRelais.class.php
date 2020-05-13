<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostRelais
 * @author Hugues.
 * @since 1.05.12
 * @version 1.05.12
 */
class WpPostRelais extends LocalDomain
{

  public function __construct($attributes=array())
  { parent::__construct($attributes); }

  ////////////////////////////////////////////////////////////////////////////
  // Méthodes relatives à l'article WpPost
  /**
   * @return WpPost
   */
  public function getMainWpPost($metakey, $value, $categId)
  {
    if ($this->WpPost==null) {
      $args = array(
        self::WP_METAKEY   => $metakey,
        self::WP_METAVALUE => $value,
        self::WP_TAXQUERY  => array(),
        self::WP_CAT       => $categId,
      );
      if (MainPageBean::isAdmin()) {
        $args[self::WP_POSTSTATUS] = self::WP_PUBLISH.', future';
      }
      $WpPosts = $this->WpPostServices->getArticles($args);
      $this->WpPost = (!empty($WpPosts) ? array_shift($WpPosts) : new WpPost());
    }
    return $this->WpPost;
  }
  /**
   * @return string
   */
  public function getWpPostUrl()
  { return $this->getWpPost()->getPermalink(); }
  /**
   * @return string
   */
  public function getWpPostEditUrl()
  { return ($this->getWpPost()->getID()!='' ? '/wp-admin/post.php?post='.$this->getWpPost()->getID().'&action=edit' : '/wp-admin/post-new.php'); }
  ////////////////////////////////////////////////////////////////////////////

  /**
   * @return string
   */
  public function getEditUrl($onglet)
  {
    $queryArgs = array(
      self::CST_ONGLET     => $onglet,
      self::CST_POSTACTION => self::CST_EDIT,
      self::FIELD_ID       => $this->getId()
    );
    return $this->getQueryArg($queryArgs);
  }

}
