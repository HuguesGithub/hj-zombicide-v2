<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe LocalBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.07.21
 */
class LocalBean extends UtilitiesBean implements ConstantsInterface
{

  public function __construct()
  {}

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
    return add_query_arg($addArg, remove_query_arg($remArg, 'http://zombicide.jhugues.fr/wp-admin/admin.php'));
  }
  /**
   * @param array $addArg
   * @param array $remArg
   * @param string $url
   * @return string
   */
  public function getFrontQueryArg($addArg, $remArg=array(), $url='http://zombicide.jhugues.fr/')
  { return add_query_arg($addArg, remove_query_arg($remArg, $url)); }
  /**
   * @return bool
   */
  public static function isAdmin()
  { return current_user_can('manage_options'); }
  /**
   * @return bool
   */
  public static function isLogged()
  { return is_user_logged_in(); }
  /**
   * @return int
   */
  public static function getWpUserId()
  { return get_current_user_id(); }
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
