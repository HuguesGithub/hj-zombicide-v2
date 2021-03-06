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
   * @version 1.07.21
   */
  public static function dealWithAjax()
  {
    switch ($_POST[self::CST_AJAXACTION]) {
      case self::AJAX_GETEXPANSIONS  :
      case self::AJAX_EXPANSIONVERIF :
        $returned = ExpansionActions::dealWithStatic($_POST);
      break;
      case self::AJAX_ADDMORENEWS    :
        $returned = HomePageActions::dealWithStatic($_POST);
      break;
      case self::AJAX_GETMISSIONS    :
      case self::AJAX_MISSIONVERIF   :
        $returned = MissionActions::dealWithStatic($_POST);
      break;
      case self::AJAX_GETSKILLS      :
      case self::AJAX_SKILLVERIF     :
        $returned = SkillActions::dealWithStatic($_POST);
      break;
      case self::AJAX_GETRANDOMTEAM  :
      case self::AJAX_GETSURVIVORS   :
      case self::AJAX_SURVIVORVERIF  :
        $returned = SurvivorActions::dealWithStatic($_POST);
      break;
      case self::AJAX_GETRANDOMMAP   :
      case self::AJAX_GETTHROWDICE   :
      case 'getBuildingMap'          :
      case 'getNonUsedTiles'         :
      case 'getEmptyCell'            :
      case 'getImageMap'             :
        $returned = ToolActions::dealWithStatic($_POST);
      break;
      case self::AJAX_GETTILES       :
        $returned = TileActions::dealWithStatic($_POST);
      break;
      case 'updateLiveMission'       :
        $returned = LiveMissionActions::dealWithStatic($_POST);
      break;
      default :
        $returned  = 'Erreur dans le $_POST['.self::CST_AJAXACTION.'] : '.$_POST[self::CST_AJAXACTION].'<br>';
      break;
    }
    return $returned;
  }
}
