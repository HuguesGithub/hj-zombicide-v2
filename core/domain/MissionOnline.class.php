<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionOnline
 * @author Hugues.
 * @since 1.11.01
 * @version 1.11.01
 */
class MissionOnline extends WpPostRelais
{
  protected $urlDirLiveMissions = 'web/rsc/missions/live/';

  private function openMissionFile()
  {
    $this->fileId = $_SESSION['zombieKey'];
    $this->fileName = PLUGIN_PATH.$this->urlDirLiveMissions.$this->fileId.".mission.xml";
    $this->objXmlDocument = simplexml_load_file($this->fileName);
  }
  private function saveMissionFile()
  {
    $this->objXmlDocument->asXML($this->fileName);
  }

  public function __construct($Mission=null)
  {
    parent::__construct();
    $this->MissionServices = new MissionServices();
    $this->WpPostServices = new WpPostServices();

    if ($Mission==null) {
      $this->openMissionFile();
      $codeMission = $this->objXmlDocument->attributes()['code'];
      $Missions = $this->MissionServices->getMissionsWithFilters(array(self::FIELD_CODE=>$codeMission));
      $Mission = array_shift($Missions);
    }
    $this->Mission = $Mission;
    $this->WpPost  = $Mission->getWpPost();
  }


  public function setUp()
  {
    // On ouvre le fichier pour pouvoir le modifier.
    $this->openMissionFile();
    // On récupère les Custom Fields liées à la Mission
    $WpPosts = $this->WpPostServices->getWpPostsByCustomField(self::FIELD_MISSIONID, $this->WpPost->getID());
    while (!empty($WpPosts)) {
      // On les parcourt.
      $WpPost = array_shift($WpPosts);

      $hasCategory = false;
      // On en récupère les Catégories
      $WpCategories = $WpPost->getCategories();
      while (!empty($WpCategories)) {
        $WpCategory = array_shift($WpCategories);
        if ($WpCategory->getCatId()==self::WP_CAT_RULE_ID) {
          // On cherche les Catégories de type MissionRule
          $hasCategory = true;
        }
      }

      if ($hasCategory) {
        // Si on en une, on récupère la donnée Méta Code associée
        $metaValue = $WpPost->getPostMeta(self::FIELD_CODE);
        if ($metaValue!='') {
          // Et on vérifie que rien n'est à faire dans le Set Up.
          $this->dealWithSetUpRule($metaValue);
        }
      }
    }
    // On sauvegarde les éventuels changements.
    $this->saveMissionFile();
  }

  private function dealWithSetUpRule($metaValue)
  {
    // Les Règles qui débutent par "AMONG_RED" permettent de mélanger des objectifs de couleur parmi les rouges.
    if (substr($metaValue, 0, 9)=='AMONG_RED') {
      $arrColors = explode('_', $metaValue);
      for ($i=2; $i<count($arrColors); $i++) {
        $lstElements = $this->objXmlDocument->xpath('//map/chips/chip[@type="Objective"][@color="red"]');
        $nbElements = count($lstElements);
        $rnd = random_int(0, $nbElements-1);
        $this->objXmlDocument->xpath('//map/chips/chip[@type="Objective"][@color="red"]')[$rnd]->attributes()['color'] = strtolower($arrColors[$i]);
      }
    }
  }
}
