<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MainPageBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.04.16
 */
class MainPageBean extends UtilitiesBean implements ConstantsInterface
{
  /**
   * Template pour afficher le header principal
   * @var $tplMainHeaderContent
   */
  public static $tplMainHeaderContent  = 'web/pages/public/public-main-header.php';
  /**
   * Template pour afficher le footer principal
   * @var $tplMainFooterContent
   */
  public static $tplMainFooterContent  = 'web/pages/public/public-main-footer.php';
  /**
   * Option pour cacher le Header et le footer.
   * @var $showHeaderAndFooter
   */
  public $showHeaderAndFooter  = true;
  /**
   * La classe du shell pour montrer plus ou moins le haut de l'image de fond.
   * @var $shellClass
   */
  protected $shellClass;
  /**
   * Class Constructor
   */
  public function __construct()
  { $this->WpPostServices = new WpPostServices(); }
  /**
   * @return string
   */
  public function displayPublicFooter()
  {
    $args = array(admin_url('admin-ajax.php'));
    $str = file_get_contents(PLUGIN_PATH.'web/pages/public/public-main-footer.php');
    return vsprintf($str, $args);
  }
  /**
   * @return string
   */
  public function displayPublicHeader()
  {
    if ($this->WpPostServices==null) {
      $this->WpPostServices = new WpPostServices();
    }
    $WpPosts = $this->WpPostServices->getArticles(array('post_type'=>'page', 'orderby'=>'menu_order', 'tax_query'=>array()), false, 'WpPage');
    $strPages = '<a href="'.get_site_url().'"><span>Accueil</span></a>';
    while (!empty($WpPosts)) {
      $WpPost = array_shift($WpPosts);
      if ($WpPost->getPostParent()!=0) {
        continue;
      }
      $WpPage = new WpPage($WpPost->getID());
      $Children = $WpPage->hasChildren();
      if (empty($Children)) {
        $strPages .= '<a href="'.$WpPage->getPermalink().'"><span>'.$WpPage->getPostTitle().'</span></a>';
      } else {
        $strPages .= '<span class="hasDropDown"><a href="#"><span>'.$WpPage->getPostTitle().'</span></a><ul>';
        while (!empty($Children)) {
          $Child = array_shift($Children);
          if ($Child->getMenuOrder()==0) {
            continue;
          }
          $strPages .= '<li><a href="'.$Child->getPermalink().'"><span>'.$Child->getPostTitle().'</span></a></li>';
        }
        $strPages .= '</ul></span>';
      }
    }

    if ($this->showHeaderAndFooter) {
      $args = array(
          '',
          '',
          $strPages
    );
    } else {
      $args = array('', '', '');
    }
    $str = file_get_contents(PLUGIN_PATH.'web/pages/public/public-main-header.php');
    return vsprintf($str, $args);
  }
  /**
   * @return Bean
   */
  public static function getPageBean()
  {
    if (is_front_page()) {
      $returned = new WpPageHomeBean();
    } else {
      $scriptUrl = $_SERVER['REDIRECT_SCRIPT_URL'];
      if (strpos($scriptUrl, '/tag/')!==false) {
        $returned = new WpPageTagBean($scriptUrl);
      } else {
        $post = get_post();
        if (empty($post)) {
          // On a un problème (ou pas). On pourrait être sur une page avec des variables, mais qui n'est pas prise en compte.
          $slug = str_replace('/', '', $_SERVER['REDIRECT_SCRIPT_URL']);
          $args = array(
              'name'=>$slug,
              'post_type'=>'page',
              'numberposts'=>1
          );
          $my_posts = get_posts($args);
          $post = array_shift($my_posts);
        }
        if ($post->post_type == 'page') {
          $returned = new WpPageBean($post);
        } elseif ($post->post_type == 'post') {
          $WpPostBean = new WpPostBean($post);
          $returned = $WpPostBean->getBean();
        } else {
          $returned = new WpPageError404Bean();
        }
      }
    }
    return $returned;
  }
  /**
   * @param array $addArg
   * @param array $remArg
   * @return string
   */
  public function getQueryArg($addArg, $remArg=array())
  {
    $addArg['page'] = 'hj-zombicide/admin_manage.php';
    $remArg[] = 'form';
    $remArg[] = 'id';
    return add_query_arg($addArg, remove_query_arg($remArg, 'http://zombicidev2.jhugues.fr/wp-admin/admin.php'));
  }
  /**
   * @return bool
   */
  public static function isAdmin()
  { return current_user_can('manage_options'); }
  /**
   * @return string
   */
  public function getShellClass()
  { return $this->shellClass; }
  /**
   * @param string $id
   * @param string $default
   * @return mixed
   */
  public function initVar($id, $default='')
  {
    if (isset($_POST[$id])) {
      return $_POST[$id];
    }
    if (isset($_GET[$id])) {
      return $_GET[$id];
    }
    return $default;
  }

}
