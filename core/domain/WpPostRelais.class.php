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
  protected $metakey;
  protected $metavalue;
  protected $categId;
  protected $adminTab;

  public function __construct($attributes=array())
  {
    parent::__construct($attributes);
  }

  ////////////////////////////////////////////////////////////////////////////
  // Méthodes relatives à l'article WpPost
  /**
   * @return WpPost
   */
  public function getWpPost()
  {
    $args = array(
      self::WP_METAKEY   => $this->metakey,
      self::WP_METAVALUE => $this->getField($this->metavalue),
      self::WP_TAXQUERY  => array(),
      self::WP_CAT       => $this->categId,
    );
    if (MainPageBean::isAdmin()) {
      $args[self::WP_POSTSTATUS] = self::WP_PUBLISH.', future';
    }
    $WpPosts = $this->WpPostServices->getArticles($args);
    return (!empty($WpPosts) ? array_shift($WpPosts) : new WpPost());
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
  public function getEditUrl()
  {
    $queryArgs = array(
      self::CST_ONGLET     => $this->adminTab,
      self::CST_POSTACTION => self::CST_EDIT,
      self::FIELD_ID       => $this->getId()
    );
    return $this->getQueryArg($queryArgs);
  }

}
