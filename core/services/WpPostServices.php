<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostServices
 * @author Hugues
 * @since 1.04.00
 * @version 1.08.01
 */
class WpPostServices extends GlobalServices implements ConstantsInterface
{

  /**
   * @param array $params
   * @param string $viaWpQuery
   * @param string $wpPostType
   * @return array
   */
  public function getArticles($params=array(), $viaWpQuery=false, $wpPostType='WpPostMission')
  {
    $args = array(
      self::WP_ORDERBY      => self::FIELD_NAME,
      self::WP_ORDER        => self::ORDER_ASC,
      self::WP_POSTSPERPAGE => -1,
      self::WP_POSTTYPE     => self::WP_POST,
      self::WP_TAXQUERY     => array(
        array(
          self::WP_TAXONOMY => self::WP_POSTTAG,
          self::WP_FIELD    => self::WP_SLUG,
          self::WP_TERMS    => array('mission', 'survivant'),
        )
      )
    );
    if (!empty($params)) {
      foreach ($params as $key => $value) {
        $args[$key] = $value;
      }
    }
    if ($viaWpQuery) {
      $wpQuery = new WP_Query($args);
      $posts_array = $wpQuery->posts;
    } else {
      $posts_array = get_posts($args);
    }
    $WpPosts = array();
    if (!empty($posts_array)) {
      foreach ($posts_array as $post) {
        $tags = wp_get_post_tags($post->ID);
        $localWpPostType = $this->getPostTypeFromTags($tags);
        $WpPosts[] = WpPost::convertElement($post, $localWpPostType);
      }
    }
    return $WpPosts;
  }
  private function getPostTypeFromTags($tags)
  {
    $wpPostType = '';
    if (!empty($tags)) {
      foreach ($tags as $WpTerm) {
        if ($WpTerm->slug == 'mission') {
          $wpPostType = 'WpPostMission';
        } elseif ($WpTerm->slug == 'news') {
          $wpPostType = 'WpPostNews';
        } elseif ($WpTerm->slug == 'survivant') {
          $wpPostType = 'WpPostSurvivor';
        }
      }
    }
    return $wpPostType;
  }

  /**
   * @param int $pageId
   * @param int $limit
   * @return array
   */
  public function getChildPagesByParentId($pageId, $limit = -1, $params=array())
  {
    global $post;
    $pages = array();
    $args = array(
      self::WP_ORDERBY      => self::FIELD_NAME,
      self::WP_ORDER        => self::ORDER_ASC,
      self::WP_POSTTYPE     => 'page',
      'post_parent'         => $pageId,
      self::WP_POSTSPERPAGE => $limit
    );
    if ( !empty($params) ) {
      foreach ( $params as $key=>$value ) {
        $args[$key] = $value;
      }
    }
    $the_query = new WP_Query($args);
    while ($the_query->have_posts()) {
      $the_query->the_post();
      $pages[] = WpPost::convertElement($post, 'WpPost');
    }
    wp_reset_postdata();
    return $pages;
  }

  public function getWpPostsByCustomField($name, $value)
  {
    $args = array('numberposts'=>-1, 'post_type'=>'post', 'meta_key'=>$name, 'meta_value'=>$value);
    $posts = get_posts($args);
    $WpPosts = array();
    while (!empty($posts)) {
      array_push($WpPosts, WpPost::convertElement(array_shift($posts), 'WpPost'));
    }
    return $WpPosts;
  }

  public function getWpPostByCategoryId($categoryId=-1)
  {
    $args = array(
      self::WP_TAXQUERY     => array(),
      self::WP_CAT          => $categoryId,
    );
    return $this->getArticles($args);
  }
}
