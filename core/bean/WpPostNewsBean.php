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
  protected $urlTemplate = 'web/pages/public/fragments/article-news-extract.php';
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
  public function displayWpPost()
  {
    $args = array(
      $this->WpPost->getPostContent(),
      $this->WpPost->getPostTitle(),
      '','','','','','','',
    );
    return $this->getRender($this->urlTemplate, $args);
  }
}
