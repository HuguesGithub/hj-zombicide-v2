<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe LocalDomain
 * @author Hugues.
 * @since 1.00.00
 * @version 1.05.20
 */
class LocalDomain extends GlobalDomain implements ConstantsInterface
{
  /*
  protected $metakey;
  protected $metavalue;
  protected $categId;
  protected $adminOnglet;
  */

  public function __construct($attributes=array())
  {
    global $globalExpansions;
    $globalExpansions = array();
    parent::__construct($attributes);
    $this->DurationServices          = new DurationServices();
    $this->EquipmentKeywordServices  = new EquipmentKeywordServices();
    $this->EWProfileServices         = new EquipmentWeaponProfileServices();
    $this->ExpansionServices         = new ExpansionServices();
    $this->KeywordServices           = new KeywordServices();
    $this->LevelServices             = new LevelServices();
    $this->MissionServices           = new MissionServices();
    $this->MissionExpansionServices  = new MissionExpansionServices();
    $this->MissionObjectiveServices  = new MissionObjectiveServices();
    $this->MissionRuleServices       = new MissionRuleServices();
    $this->MissionTileServices       = new MissionTileServices();
    $this->ObjectiveServices         = new ObjectiveServices();
    $this->OrigineServices           = new OrigineServices();
    $this->PlayerServices            = new PlayerServices();
    $this->RuleServices              = new RuleServices();
    $this->SkillServices             = new SkillServices();
    $this->SurvivorSkillServices     = new SurvivorSkillServices();
    $this->TileServices              = new TileServices();
    $this->WeaponProfileServices     = new WeaponProfileServices();
    $this->WpPostServices            = new WpPostServices();
  }
  /**
   * @return Expansion
   */
  public function getExpansion()
  {
    global $globalExpansions;
    if ($this->Expansion==null) {
      if (isset($globalExpansion[$this->expansionId])) {
        $this->Expansion = $globalExpansion[$this->expansionId];
      } else {
        $this->Expansion = $this->ExpansionServices->selectExpansion($this->expansionId);
        $globalExpansion[$this->expansionId] = $this->Expansion;
      }
    }
    return $this->Expansion;
  }













  /**
   * @return string
   */
  public function toJson()
  {
    $classVars = $this->getClassVars();
    $str = '';
    foreach ($classVars as $key => $value) {
      if ($str!='') {
        $str .= ', ';
      }
      $str .= '"'.$key.'":'.json_encode($this->getField($key));
    }
    return '{'.$str.'}';
  }
  /**
   * @param array $post
   * @return bool
   */
  public function updateWithPost($post)
  {
    $classVars = $this->getClassVars();
    unset($classVars['id']);
    $doUpdate = false;
    foreach ($classVars as $key => $value) {
      if (is_array($post[$key])) {
        $value = stripslashes(implode(';', $post[$key]));
      } else {
        $value = stripslashes($post[$key]);
      }
      if ($this->{$key} != $value) {
        $doUpdate = true;
        $this->{$key} = $value;
      }
    }
    return $doUpdate;
  }
  /**
   * @return int
   */
  public static function getWpUserId()
  { return get_current_user_id(); }

  /**
   * @version 1.04.27
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
}
