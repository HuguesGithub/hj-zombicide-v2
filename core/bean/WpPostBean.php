<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * WpPostBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.04.08
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
  public function getBean()
  {
    $postMetas = $this->WpPost->getPostMetas();
    if (isset($postMetas[self::FIELD_SURVIVORID])) {
      $survivorId = $postMetas[self::FIELD_SURVIVORID];
      if ($survivorId[0]!='') {
        return new WpPostSurvivorBean($this->WpPost);
      }
    }

    if (isset($postMetas[self::FIELD_MISSIONID])) {
      $missionId = $postMetas[self::FIELD_MISSIONID];
      if ($missionId[0]!='') {
        return new WpPostMissionBean($this->WpPost);
      }
    }

    return new WpPageError404Bean();
  }
  /**
   * @return string|WpPageError404Bean
   */
  public function getContentPage()
  {
    $strReturned = '';
    $postMetas = $this->WpPost->getPostMetas();
    if (isset($postMetas[self::FIELD_SURVIVORID])) {
      $WpBean = new WpPostSurvivorBean($this->WpPost);
      $Survivor = $WpBean->getSurvivor();
      if ($Survivor->getId()!='') {
        $strReturned = $WpBean->getContentPage();
      }
    } elseif (isset($postMetas[self::FIELD_MISSIONID])) {
      $strReturned = 'WIP WpPostBean getContentPage.';
    }
    if ($strReturned=='') {
      $WpBean = new WpPageError404Bean();
      $strReturned = $WpBean->getContentPage();
    }
    return $strReturned;
  }
  /**
   * @return string
   */
  public function getShellClass()
  { return ''; }
}
