<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MainPageBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.08.01
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
    return $this->getRender(self::$tplMainFooterContent, $args);
  }
  /////////////////////////// Fin gestion PublicFooter ///////////////////////////

  /**
   * @return string
   */
  public function displayPublicHeader()
  {
    //////////////////////////////////////////////////////////////////////////////////
    // Récupération des pages devant être affichées dans le menu du Header
    $args = array(
      self::WP_POSTTYPE => 'page',
      self::WP_ORDERBY  => 'menu_order',
      self::WP_TAXQUERY => array(),
      'post_parent'     => 0,
    );
    $WpPosts = $this->WpPostServices->getArticles($args, false, 'WpPage');
    // On construit le lien vers l'accueil
    $label = $this->getBalise(self::TAG_SPAN, 'Accueil');
    $strPages = $this->getBalise(self::TAG_A, $label, array(self::ATTR_HREF=>get_site_url()));
    // Pour chaque page, si le parent ne vaut pas 0, on passe au suivant...
    while (!empty($WpPosts)) {
      $WpPost = array_shift($WpPosts);
      $strPages .= $this->addWpPageToMenu($WpPost);
    }
    //////////////////////////////////////////////////////////////////////////////////
    // On enrichi le Template et on le restitue.
    $args = array(
      // Plus d'actualité - 1
      '',
      // Plus d'actualité - 2
      '',
      // Contenu du Menu à afficher, mais pas tout le temps (pleine page) - 3
      ($this->showHeaderAndFooter ? $strPages : '')
    );
    return $this->getRender(self::$tplMainHeaderContent, $args);
  }
  /**
   * @param WpPage $WpPost
   * @return string
   */
  private function addWpPageToMenu($WpPost)
  {
    $strMenu = '';
    // On récupère la WpPage qu'on veut afficher.
    $WpPage = new WpPage($WpPost->getID());
    $labelParent = $this->getBalise(self::TAG_SPAN, $WpPage->getPostTitle());
    // On vérifie la présence d'enfants éventuels.
    $Children = $WpPage->hasChildren();
    if (empty($Children)) {
      // S'il n'y en a pas, c'est un siple lien.
      $strMenu = $this->getBalise(self::TAG_A, $labelParent, array(self::ATTR_HREF => $WpPage->getPermalink()));
    } else {
      // Sinon, on doit construire la liste des enfants pour le sous menu
      $strSubMenus = '';
      while (!empty($Children)) {
        $Child = array_shift($Children);
        if ($Child->getMenuOrder()==0) {
          // On n'affiche que les enfants ayant un OrderMenu différent de 0
          continue;
        }
        $childLabel   = $this->getBalise(self::TAG_SPAN, $Child->getPostTitle());
        $childLink    = $this->getBalise(self::TAG_A, $childLabel, array(self::ATTR_HREF=>$Child->getPermalink()));
        $strSubMenus .= $this->getBalise(self::TAG_LI, $childLink);
      }
      $parentLink    = $this->getBalise(self::TAG_A, $labelParent, array(self::ATTR_HREF=>'#'));
      $listChildren  = $this->getBalise(self::TAG_UL, $strSubMenus);
      $strMenu      .= $this->getBalise(self::TAG_SPAN, $parentLink.$listChildren, array(self::ATTR_CLASS => 'hasDropDown'));
    }
    return $strMenu;
  }
  /////////////////////////// Fin gestion PublicHeader ///////////////////////////

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
