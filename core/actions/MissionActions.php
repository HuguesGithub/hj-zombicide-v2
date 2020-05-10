<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * MissionActions
 * @author Hugues
 * @since 1.02.00
 * @version 1.05.10
 */
class MissionActions extends LocalActions
{
  /**
   * Constructeur
   */
  public function __construct($post=array())
  {
    parent::__construct();
    $this->post = $post;
    $this->WpPostServices   = new WpPostServices();
    $this->DurationServices = new DurationServices();
    $this->LevelServices    = new LevelServices();
    $this->MissionServices  = new MissionServices();
    $this->OrigineServices  = new OrigineServices();
    $this->PlayerServices   = new PlayerServices();
  }
  /**
   * Point d'entrée des méthodes statiques.
   * @param array $post
   * @return string
   **/
  public static function dealWithStatic($post)
  {
    $returned = '';
    $Act = new MissionActions($post);
    if ($post[self::CST_AJAXACTION]==self::AJAX_GETMISSIONS) {
      $returned = $Act->dealWithGetMissions();
    } elseif ($post[self::CST_AJAXACTION]==self::AJAX_MISSIONVERIF) {
      $returned = $Act->dealWithMissionVerif(true);
    } else {
      $returned = '';
    }
    return $returned;
  }

  /**
   * Récupération du contenu de la page via une requête Ajax.
   * @param array $post
   * @return string
   */
  public function dealWithGetMissions()
  {
    $Bean = new WpPageMissionsBean();
    $Bean->setFilters($this->post);
    return $this->jsonString($Bean->getListContentPage(), self::PAGE_MISSION, true);
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  // Bloc de gestion de vérifications des Missions en Home Admin
  /**
   * @param boolean $isVerif
   * @return string
   */
  public function dealWithMissionVerif($isVerif=false)
  {
    // On récupère les articles de missions
    $args = array(
      self::WP_CAT         => self::WP_CAT_MISSION_ID,
      self::WP_TAXQUERY    => array(),
      self::WP_POSTSTATUS  => self::WP_PUBLISH.', future',
    );
    $this->WpPostMissions = $this->WpPostServices->getArticles($args);
    $nbWpPostMissions = count($this->WpPostMissions);
    // Et les Missions en base
    $this->Missions = $this->MissionServices->getMissionsWithFilters();
    $nbMissions = count($this->Missions);
    if ($isVerif) {
      $this->checkMissions();
      $strBilan = $this->jsonString($this->strBilan, self::AJAX_MISSIONVERIF, true);
    } elseif ($nbWpPostMissions!=$nbMissions) {
      $strBilan  = "Le nombre d'articles ($nbWpPostMissions) ne correspond pas au nombre de Missions en base ($nbMissions).";
      $strBilan .= "<br>Une vérification est vivement conseillée.";
    } else {
      $strBilan = "Le nombre d'articles ($nbWpPostMissions) correspond au nombre de Missions en base.";
    }
    return $strBilan;
  }

  private function checkMissions()
  {
    // On regarde les articles créés et on vérifie les données en base, si elles existent et si elles sont cohérentes entre elles.
    while (!empty($this->WpPostMissions)) {
      $this->areDataOkay = true;
      $this->doUpdate = false;
      $this->WpPost = array_shift($this->WpPostMissions);
      $href = '/wp-admin/post.php?post='.$this->WpPost->getID().'&action=edit';
      $postTitle = $this->WpPost->getPostTitle();
      $missionId = $this->WpPost->getPostMeta(self::FIELD_MISSIONID);
      if ($missionId!='') {
        $Mission = $this->MissionServices->selectMission($missionId);
      } else {
        // On n'a pas de correspondance avec la BDD. On va chercher par le nom.
        $this->strBilan .= '<br>L article WpPost <a href="'.$href.'">'.$postTitle.'</a> a un missionId qui vaut -1.';

        $Missions = $this->MissionServices->getMissionsWithFilters(array(self::FIELD_TITLE=>$postTitle));
        if (empty($Missions)) {
          $Mission = new Mission();
        } else {
          $this->Mission = array_shift($Missions);
          $this->strBilan .= '<br>On peut renseigner missionId du WpPost avec : '.$this->Mission->getId().'.';
        }
      }

      // On checke le Titre
      if ($postTitle!=$this->Mission->getTitle()) {
        $this->strBilan .= '<br>Le titre doit être mis à jour en base.';
        $this->doUpdate = true;
      }

      $this->checkCode();
      $this->checkLevel();
      $this->checkPlayer();
      $this->checkDuration();
      $this->checkOrigine();

      if (!$this->areDataOkay) {
        $this->strBilan .= '<br>Analyse de l article WpPost <a href="'.$href.'">'.$postTitle.'</a> terminée, des données ne sont pas renseignées.<br>';
      } elseif ($this->doUpdate) {
        if ($missionId==-1) {
          $this->Mission->setTitle($postTitle);
          $this->MissionServices->insertMission($this->Mission);
        } else {
          $this->MissionServices->updateMission($this->Mission);
        }
        $this->strBilan .= '<br>Analyse de l article WpPost <a href="'.$href.'">'.$postTitle.'</a> terminée, avec anomalie.<br>';
      }

    }

    /*
    // Puis, on regarde les données en base et on vérifie que des articles ont été créés pour elles.
    while (!empty($this->Missions)) {
      // On récupère l'extension.
      $Mission = array_shift($this->Missions);
      $args = array(
        self::WP_METAKEY      => self::FIELD_MISSIONID,
        self::WP_METAVALUE    => $Mission->getId(),
        self::WP_TAXQUERY     => array(),
        self::WP_CAT          => self::WP_CAT_MISSION_ID,
      );
      $WpPost = $this->WpPostServices->getArticles($args);
      if (empty($WpPost)) {
        $this->strBilan .= '<br>Article à créer pour une Mission : '.$Mission->getTitle().' ['.$Mission->toJson().'].';
      }
    }
    */
    if ($this->strBilan=='') {
      $this->strBilan = 'Il semblerait que tout aille à la perfection. Aucune anomalie remontée.';
    }
  }
  private function checkCode()
  {
    // On checke le code
    $postCode = $this->WpPost->getPostMeta(self::FIELD_CODE);
    if ($postCode=='') {
      $this->addStrBilan('Code', $this->Mission->getCode());
      update_post_meta($this->WpPost->getID(), self::FIELD_CODE, $this->Mission->getCode());
      $this->areDataOkay = false;
    } elseif ($postCode!=$this->Mission->getCode()) {
      $this->strBilan .= '<br><strong>Code</strong> à mettre à jour.';
      $this->Mission->setCode($postCode);
      $this->doUpdate = true;
    }
  }
  private function checkLevel()
  {

    // On checke le levelId
    $levelId = $this->WpPost->getPostMeta(self::FIELD_LEVELID);
    $Levels = $this->LevelServices->getLevelsWithFilters(array(self::FIELD_NAME=>$levelId));
    $Level = array_shift($Levels);
    if ($levelId=='') {
      $this->addStrBilan('Difficulté', $this->Mission->getLevel()->getName());
      update_post_meta($this->WpPost->getID(), self::FIELD_LEVELID, $this->Mission->getLevel()->getName());
      $this->areDataOkay = false;
    } else {
      $levelId = $Level->getId();
      if ($levelId!=$this->Mission->getLevelId()) {
        $this->strBilan .= '<br><strong>Difficulté</strong> à mettre à jour.';
        $this->Mission->setLevelId($levelId);
        $this->doUpdate = true;
      }
    }
  }
  private function checkPlayer()
  {
    // On checke le playerId
    $playerId = $this->WpPost->getPostMeta(self::FIELD_PLAYERID);
    $Players = $this->PlayerServices->getPlayersWithFilters(array(self::FIELD_NAME=>$playerId));
    $Player = array_shift($Players);
    if ($playerId=='') {
      $this->addStrBilan('Nombre', $this->Mission->getPlayer()->getName());
      update_post_meta($this->WpPost->getID(), self::FIELD_PLAYERID, $this->Mission->getPlayer()->getName());
      $this->areDataOkay = false;
    } else {
      $playerId = $Player->getId();
      if ($playerId!=$this->Mission->getPlayerId()) {
        $this->strBilan .= '<br><strong>Nombre</strong> à mettre à jour. ['.$playerId.';'.$this->Mission->getPlayerId().']';
        $this->Mission->setPlayerId($playerId);
        $this->doUpdate = true;
      }
    }
  }
  private function checkDuration()
  {
    // On checke le durationId
    $durationId = $this->WpPost->getPostMeta(self::FIELD_DURATIONID);
    list($min, $max) = explode('-', $durationId);
    $Durations = $this->DurationServices->getDurationsWithFilters(array(self::FIELD_MINDURATION=>$min, self::FIELD_MAXDURATION=>$max));
    if (!empty($Durations)) {
      $Duration = array_shift($Durations);
    } else {
      $Duration = new Duration();
    }
    if ($durationId=='') {
      $strDuree = $this->Mission->getDuration()->getMinDuration().($this->Mission->getDuration()->getMaxDuration()!=0 ? '-'.$this->Mission->getDuration()->getMaxDuration() : '');
      $this->addStrBilan('Durée', $strDuree);
      update_post_meta($this->WpPost->getID(), self::FIELD_DURATIONID, $strDuree);
      $this->areDataOkay = false;
    } else {
      $durationId = $Duration->getId();
      if ($durationId!=$this->Mission->getDurationId()) {
        $this->strBilan .= '<br><strong>Durée</strong> à mettre à jour.';
        $this->Mission->setDurationId($durationId);
        $this->doUpdate = true;
      }
    }
  }
  private function checkOrigine()
  {
    // On checke le origineId
    $origineId = $this->WpPost->getPostMeta(self::FIELD_ORIGINEID);
    $Origines = $this->OrigineServices->getOriginesWithFilters(array(self::FIELD_NAME=>$origineId));
    $Origine = array_shift($Origines);
    if ($origineId=='') {
      $this->addStrBilan('Origine', $this->Mission->getOrigine()->getName());
      update_post_meta($this->WpPost->getID(), self::FIELD_ORIGINEID, $this->Mission->getOrigine()->getName());
      $this->areDataOkay = false;
    } else {
      $origineId = $Origine->getId();
      if ($origineId!=$this->Mission->getOrigineId()) {
        $this->strBilan .= '<br><strong>Origine</strong> à mettre à jour.';
        $this->Mission->setOrigineId($origineId);
        $this->doUpdate = true;
      }
    }
  }
  private function addStrBilan($type, $name)
  { $this->strBilan .=  '<br><strong>'.$type.'</strong> <em>'.$name.'</em>.';
  }
  // Fin du bloc relatif à la vérification des compétences sur la Home Admin.
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
}
