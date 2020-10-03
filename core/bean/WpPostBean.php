<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * WpPostBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.05.01
 */
class WpPostBean extends MainPageBean
{
  /**
   * WpPost affiché
   * @var WpPost $WpPost
   */
  protected $WpPost;
  /**
   * @param string $post
   * @param array $services
   */
  public function __construct($post='', $services=array())
  {
    if ($post=='') {
      $post = get_post();
    }
    if (get_class($post) == 'WpPost') {
      $this->WpPost = $post;
    } else {
      $this->WpPost = WpPost::convertElement($post);
    }
    parent::__construct($services);
  }
  /**
   * @return Bean
   */
  public function getBean()
  {
    $this->WpCategs = $this->WpPost->getCategories();
      if (self::isAdmin()) {
        print_r($this->WpCategs);
      }
    if (!empty($this->WpCategs)) {
      $this->WpCateg = array_shift($this->WpCategs);
      switch ($this->WpCateg->getCatID()) {
        case self::WP_CAT_EXPANSION_ID  :
          $Bean = new WpPostExpansionBean($this->WpPost);
        break;
        case self::WP_CAT_MISSION_ID    :
          $Bean = new WpPostMissionBean($this->WpPost);
        break;
        case self::WP_CAT_NEWS_ID       :
          $Bean = new WpPostNewsBean($this->WpPost);
        break;
        case self::WP_CAT_SKILL_ID      :
          $Bean = new WpPostSkillBean($this->WpPost);
        break;
        case self::WP_CAT_SURVIVOR_ID   :
          $Bean = new WpPostSurvivorBean($this->WpPost);
        break;
        default :
          $Bean = new WpPageError404Bean();
        break;
      }
    } else {
      $Bean = new WpPageError404Bean();
    }
    return $Bean;
  }

  /**
   * @return string
   */
  public function getShellClass()
  { return ''; }
}
