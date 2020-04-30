<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AjaxActions
 * @author Hugues
 * @since 1.04.00
 * @version 1.04.27
 */
class AjaxActions extends LocalActions
{
  /**
   * Constructeur
   */
  public function __construct()
  {}

  /**
   * Gère les actions Ajax
   * @version 1.04.30
   */
  public static function dealWithAjax()
  {
    switch ($_POST[self::CST_AJAXACTION]) {
      case self::AJAX_ADDMORENEWS    :
        $returned = HomePageActions::dealWithStatic($_POST);
      break;
      case self::AJAX_EXPANSIONVERIF :
        $returned = ExpansionActions::dealWithStatic($_POST);
      break;
      case self::AJAX_GETMISSIONS    :
        $returned = MissionActions::dealWithStatic($_POST);
      break;
      case self::AJAX_GETRANDOMTEAM  :
        $returned = SurvivorActions::dealWithStatic($_POST);
      break;
      case self::AJAX_GETSKILLS      :
      case self::AJAX_SKILLVERIF     :
        $returned = SkillActions::dealWithStatic($_POST);
      break;
      case self::AJAX_GETSURVIVORS   :
        $returned = SurvivorActions::dealWithStatic($_POST);
      break;
      default :
        $returned  = 'Erreur dans le $_POST['.self::CST_AJAXACTION.'] : '.$_POST[self::CST_AJAXACTION].'<br>';
      break;
    }
    return $returned;
  }
}
