<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageHomeBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.04.08
 */
class WpPageHomeBean extends WpPageBean
{
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->MissionServices = new MissionServices();
  }

  /**
   * {@inheritDoc}
   * @see MainPageBean::getContentPage()
   */
  public function getContentPage()
  {
    $strContent  = '<section id="homeSectionArticles" class="batchArticles missions survivors show-survivor">';
    $strContent .= $this->addMoreNews(0, false);
    $strContent .= '</section>';
    $strContent .= '<section class="col-xs-4 col-xs-offset-4">';
    $strContent .= '<div class="text-center"><div id="more_news" class="special_buttons">Plus de news</div></div>';
    $strContent .= '</section>';
    return $strContent.'<div class="clearfix"></div>';
  }
  /**
   * @param number $offset
   * @return string
   */
  public static function staticAddMoreNews($offset=0)
  {
    $Bean = new WpPageHomeBean();
    return $Bean->addMoreNews($offset);
  }
  /**
   * @param number $offset
   * @param string $isAjax
   * @return string
   */
  public function addMoreNews($offset=0, $isAjax=true)
  {
    $postStatus = ($this->isAdmin() ? 'publish, private, future' : 'publish');
    $args = array(
      'orderby'=> 'post_date',
      'order'=>'DESC',
      'posts_per_page'=>6,
      'offset'=>$offset,
      'post_status'=>$postStatus
    );
    $WpPosts = $this->WpPostServices->getArticles($args);
    $strContent = '';
    while (!empty($WpPosts)) {
      $WpPost = array_shift($WpPosts);
      $WpBean = $WpPost->getBean();
      $strContent .= $WpBean->displayWpPost(true);
    }
    $strContent .= '<div class="clearfix"></div>';
    return ($isAjax ?  '{"homeSectionArticles":'.json_encode($strContent).'}' : $strContent);
  }
}
