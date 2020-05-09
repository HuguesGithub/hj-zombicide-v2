<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostNewsBean
 * @author Hugues
 * @since 1.05.09
 * @version 1.05.09
 */
class WpPostNewsBean extends WpPostBean
{
  /**
   * Constructeur
   */
  public function __construct($WpPost='')
  {
    parent::__construct();
    $this->WpPost = $WpPost;
  }
  /**
   * @param string $isHome
   * @return string
   */
  public function displayThumbWpPost($isHome=false)
  {
    return 'News Thumb';
  }
  /**
   * @param string $isHome
   * @return string
   */
  public function displayWpPost($isHome=false)
  {
    $WpPost = $this->WpPost;
    $args = array(
      $WpPost->getPostContent(),
      $WpPost->getPostTitle(),
      '','','','','','','',
    );
    $str = file_get_contents(PLUGIN_PATH.'web/pages/public/fragments/article-news-extract.php');
    return vsprintf($str, $args);
  }
}
