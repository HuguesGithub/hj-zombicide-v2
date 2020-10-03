<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageHomeBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.05.09
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
    $strContent .= $this->addMoreNews();
    $strContent .= '</section>';
    $strContent .= '<section class="col-xs-4 col-xs-offset-4">';
    $strContent .= '<div class="text-center"><div id="more_news" class="special_buttons">';
    $strContent .= ($this->lang=='en' ? 'More news' : 'Plus de news');
    $strContent .= '</div></div>';
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
    return $Bean->addMoreNews($offset, true, false);
  }
  /**
   * @param number $offset
   * @param string $isAjax
   * @return string
   *     return $Bean->addMoreNews($offset, true, false);
  }
  /**
   * @param number $offset
   * @param string $isAjax
   * @return string
   *
  public function addMoreNews($offset=0, $isAjax=false, $getSticky=true)
  {
    // TODO :
    // Si getSticky vaut true, on cherche le sticky post publié le plus récent.
    // Si on a bien un article, on met à jour posts_per_page à 5.
    // Si offset ne vaut pas 0, on le réduit de 1

   */
  public function addMoreNews($offset=0, $isAjax=false, $getSticky=true)
  {
    $nbPostPerPage = 6;
    $args = array(
      self::WP_ORDERBY    => 'post_date',
      self::WP_ORDER      => self::ORDER_DESC,
      self::WP_OFFSET     =>0,
    );
    if ($getSticky) {
      // Si on veut le Sticky, on en veut un
      $args[self::WP_POSTSPERPAGE] = 1;
      // Et donc on voudra un article de moins ensuite
      $nbPostPerPage--;
      // On veut le sticky évidemment
      $args['post__in'] = get_option( 'sticky_posts' );
      // On initialise TaxQuery pour prendre n'importe quel article
      $args[self::WP_TAXQUERY] = array();
      // Et on veut qu'il soit publié.
      $args[self::WP_POSTSTATUS] = self::WP_PUBLISH;
      // Et go !
      $WpStickyPosts = $this->WpPostServices->getArticles($args);
    } else {
      $WpStickyPosts = array();
      // Si on veut pas de sticky, c'est qu'on est en train de paginer. On fait -1 pour compenser le sticky sur la première page.
      $offset--;
    }
    // On récupère maintenant les Articles à afficher.
    $postStatus = ($this->isAdmin() ? ', private, future' : '');
    $args[self::WP_OFFSET] = $offset;
    $args[self::WP_POSTSPERPAGE] = $nbPostPerPage;
    $args[self::WP_POSTSTATUS] = self::WP_PUBLISH.$postStatus;
    $args[self::WP_TAXQUERY] = array(array(
      self::WP_TAXONOMY=>self::WP_POSTTAG,
      self::WP_FIELD=>self::WP_SLUG,
      self::WP_TERMS=>array('mission', 'survivant')
    ));
    $args['post__in'] = '';

    $WpPosts = $this->WpPostServices->getArticles($args);
    // On merge avec les Sticky
    $WpPosts = array_merge($WpStickyPosts, $WpPosts);
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
