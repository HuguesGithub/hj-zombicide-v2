<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe MissionBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.04.27
 */
class MissionBean extends LocalBean
{
  protected $urlTemplateExtract = 'web/pages/public/fragments/mission-article.php';
  protected $urlTemplateHome    = 'web/pages/public/fragments/mission-article-home.php';
  protected $strModelObjRules   = '<li class="objRule">%1$s <span class="tooltip"><header>%1$s</header><content>%2$s</content></span></li>';
  protected $h5Ul               = '<h5>%1$s</h5><ul>%2$s</ul>';
  protected $urlRowPublic       = 'web/pages/public/fragments/mission-row.php';




  /**
   * Class par défaut du Select
   * @var $classe
   */
  public $classe = 'custom-select custom-select-sm filters';
  /**
   * Template pour afficher une Mission
   * @var $tplMissionExtract
   */
  public static $tplMissionExtract  = 'web/pages/public/fragments/article-mission-extract.php';
  /**
   * @param Mission $Mission
   */
  public function __construct($Mission=null)
  {
    parent::__construct();
    $this->Mission = ($Mission==null ? new Mission() : $Mission);
    $this->ExpansionServices = new ExpansionServices();
    $this->MissionServices   = new MissionServices();
    $this->WpPostServices    = new WpPostServices();
    $this->tplRow = 'web/pages/admin/mission/row.php';
    $this->tplEdit = 'web/pages/admin/mission/edit.php';
  }
  /**
   * @param string $isHome
   * @return string
   */
  public function getExtract($isHome=false)
  {
    ///////////////////////////////////////////////////////////////
    // Construction des listes d'obectifs et de règles.
    $contentRules  = $this->getMissionContentObjectives();
    $contentRules .= $this->getMissionContentRules();
    // On prépare l'affichage de la Map.
    $media = get_attached_media('image');
    $WpPostMedia = (!empty($media) ? WpPost::convertElement(array_shift($media)) : new WpPost());

    ///////////////////////////////////////////////////////////////
    // On enrichi le template et on le retourne.
    $args = array(
      // Code de la Mission - 1
      $this->Mission->getCode(),
      // Titre de la Mission - 2
      $this->Mission->getTitle(),
      // Retourne la chaîne de Difficulté, Nb de joueurs et Durée - 3
      $this->Mission->getStrDifPlaDur(),
      // Synopsis - 4
      $this->Mission->getWpPost()->getPostContent(),
      // Extensions nécessaires - 5
      $this->Mission->getStrExpansions(),
      // Dalles nécessaires - 6
      $this->Mission->getStrTiles(),
      // Listes des Objectifs et des Règles - 7
      $contentRules,
      // Image de la Map éventuelle - 8
      $this->getBalise(self::TAG_IMG, '', array(self::ATTR_SRC=>$WpPostMedia->getGuid(), self::ATTR_ALT=>$this->Mission->getTitle())),
      // A partir de 9, principalement des éléments pour l'affichage dans les news.
    );
    return $this->getRender(($isHome ? $this->urlTemplateHome : $this->urlTemplateExtract), $args);
  }
  public function getMissionContentObjectives()
  {
    $WpPosts = $this->WpPostServices->getWpPostsByCustomField(self::FIELD_MISSIONID, $this->Mission->getWpPost()->getID());
    $strObj = array();
    while (!empty($WpPosts)) {
      $WpPost = array_shift($WpPosts);
      $WpCategories = $WpPost->getCategories();
      $isObj = false;
      while (!empty($WpCategories)) {
        $WpCategory = array_shift($WpCategories);
        if ($WpCategory->getCatId()==71) {
          $isObj = true;
        }
      }
      if ($isObj) {
        $rank = $WpPost->getPostMeta('rang');
        $strObj[$rank] = vsprintf($this->strModelObjRules, array($WpPost->getPostTitle(), $WpPost->getPostContent()));
      }
    }
    if (count($strObj)!=0) {
      ksort($strObj);
      return vsprintf($this->h5Ul, array('Objectifs', implode('', $strObj)));
    } else {
      return '';
    }
  }
  public function getMissionContentRules()
  {
    $WpPosts = $this->WpPostServices->getWpPostsByCustomField(self::FIELD_MISSIONID, $this->Mission->getWpPost()->getID());
    $strObj = array();
    while (!empty($WpPosts)) {
      $WpPost = array_shift($WpPosts);
      $WpCategories = $WpPost->getCategories();
      $isObj = false;
      while (!empty($WpCategories)) {
        $WpCategory = array_shift($WpCategories);
        if ($WpCategory->getCatId()==72) {
          $isObj = true;
        }
      }
      if ($isObj) {
        $rank = $WpPost->getPostMeta('rang');
        $strObj[$rank] = vsprintf($this->strModelObjRules, array($WpPost->getPostTitle(), $WpPost->getPostContent()));
      }
    }
    if (count($strObj)!=0) {
      ksort($strObj);
      return vsprintf($this->h5Ul, array('Regles speciales', implode('', $strObj)));
    } else {
      return '';
    }
  }
  /**
   * @return string
   */
  public function getRowForMissionsPage()
  {
    ///////////////////////////////////////////////////////////////
    // On enrichi le template et on le retourne.
    $urlWpPost = $this->Mission->getWpPostUrl();
    $args = array(
      // L'identifiant de la Mission - 1
      $this->Mission->getId(),
      // L'url pour accéder au détail de la Mission - 2
      $urlWpPost,
      // Le Titre de la Mission - 3
      $this->Mission->getTitle(),
      // La Difficulté, le nombre de Survivants et la Durée de la Mission - 4
      $this->Mission->getStrDifPlaDur(),
      // La liste des Extensions nécessaires à la Mission - 5
      $this->Mission->getStrExpansions(),
      // L'origine de la publication originelle - 6
      $this->getStrOrigine(),
    );
    return $this->getRender($this->urlRowPublic, $args);
  }
  /**
   * @return Mission
   */
  public function getMission()
  { return $this->Mission; }

  private function getStrOrigine()
  {
    $str = $this->Mission->getStrOrigine();
    if (empty($str)) {
      $str = 'TODO';
    }
    return $str;
  }






















  /**
   * @return string
   */
  public function getRowForAdminPage()
  {
    $Mission = $this->Mission;
    $queryArgs = array(self::CST_ONGLET=>self::CST_MISSION, self::CST_POSTACTION=>'edit', 'id'=>$Mission->getId());
    $hrefEdit = $this->getQueryArg($queryArgs);
    $queryArgs[self::CST_POSTACTION] = self::CST_TRASH;
    $hrefTrash = $this->getQueryArg($queryArgs);
    $queryArgs[self::CST_POSTACTION] = 'clone';
    $hrefClone = $this->getQueryArg($queryArgs);
    $urlWpPost = $Mission->getWpPostUrl();
    $args = array(
      // Identifiant de la Mission
      $Mission->getId(),
      // Code de la Mission
      $Mission->getCode(),
      // Url d'édition
      $hrefEdit,
      // Titre de la Mission
      $Mission->getTitle(),
      // Url de suppression
      $hrefTrash,
      // Url de Duplication
      $hrefClone,
      // Article publié ?
      $urlWpPost!='#' ? '' : ' hidden',
      // Url Article
      $urlWpPost,
      $Mission->getStrDifficulty(),
      $Mission->getStrDuree(),
      $Mission->getStrNbJoueurs(),
      $Mission->getStrOrigine(),
      $Mission->getStrTiles(),
      $Mission->getStrRules(),
      $Mission->getStrObjectives(),
      $Mission->getStrExpansions(),
  );
    $str = file_get_contents(PLUGIN_PATH.'web/pages/admin/fragments/fragment-row-mission.php');
    return vsprintf($str, $args);
  }
  /**
   * @param Mission $Mission
   * @param int $rkRow
   */
  public static function removeRow($Mission, $rkRow)
  {
    $MissionTiles = $Mission->getMissionTiles();
    if (!empty($MissionTiles)) {
      $MissionTileServices = new MissionTileServices();
      foreach ($MissionTiles as $MissionTile) {
        if ($MissionTile->getCoordY()==$rkRow) {
          $MissionTileServices->delete(__FILE__, __LINE__, $MissionTile);
        } elseif ($MissionTile->getCoordY()>$rkRow) {
          $MissionTile->setCoordY($MissionTile->getCoordY()-1);
          $MissionTileServices->update(__FILE__, __LINE__, $MissionTile);
        }
      }
    }
    $Mission->setHeight($Mission->getHeight()-1);
  }
  /**
   * @param Mission $Mission
   * @param int $rkCol
   */
  public static function removeCol($Mission, $rkCol)
  {
    $MissionTiles = $Mission->getMissionTiles();
    if (!empty($MissionTiles)) {
      $MissionTileServices = new MissionTileServices();
      foreach ($MissionTiles as $MissionTile) {
        if ($MissionTile->getCoordX()==$rkCol) {
          $MissionTileServices->delete(__FILE__, __LINE__, $MissionTile);
        } elseif ($MissionTile->getCoordX()>$rkCol) {
          $MissionTile->setCoordX($MissionTile->getCoordX()-1);
          $MissionTileServices->update(__FILE__, __LINE__, $MissionTile);
        }
      }
    }
    $Mission->setWidth($Mission->getWidth()-1);
  }
  /**
   * @param array $post
   * @return string
   */
  public static function staticBuildBlockTiles($post)
  {
    $action = $post['dealAction'];
    $rkCol = ($action==self::CST_RMVCOL ? $post['rkCol'] : 0);
    $rkRow = ($action==self::CST_RMVROW ? $post['rkRow'] : 0);
    $missionId = $post[self::CST_MISSIONID];
    $MissionServices = new MissionServices();
    $Mission = $MissionServices->select(__FILE__, __LINE__, $missionId);
    $Bean = new MissionBean($Mission);
    switch ($action) {
      case self::CST_RMVROW :
        self::removeRow($Mission, $rkRow);
      break;
      case self::CST_RMVCOL  :
        self::removeCol($Mission, $rkCol);
      break;
      case 'addRow' :
        $Mission->setHeight($Mission->getHeight()+1);
      break;
      case 'addCol'  :
        $Mission->setWidth($Mission->getWidth()+1);
      break;
      default : break;
    }
    $MissionServices->update(__FILE__, __LINE__, $Mission);
     return '{"mapEditor":'.json_encode($Bean->buildBlockTiles($Mission)).'}';
  }
  /**
   * @return string
   */
  public function buildBlockTiles()
  {
    $Mission = $this->Mission;
    $width = $Mission->getWidth();
    $height = $Mission->getHeight();
    $disabledButton = '<button type="button" class="btn btn-secondary" disabled></button>';
    $openDivTile = '<div class="col tile %2$s" data-rkcol="%1$s">';
    $closeDivTile = '</div>';
    $colBreaker = '<div class="w-100"></div>';
    $addButton = '<button type="button" class="btn btn-info" data-action="%1$s">+</button>';
    $rmvButton = '<button type="button" class="btn btn-info" data-action="%1$s" data-%2$s="%3$s">-</button>';
    $firstRow  = vsprintf($openDivTile, array(0, self::CST_FIRSTROW)).$disabledButton.$closeDivTile;
    $lastRow  =  $colBreaker.'<div class="col tile prependBefore firstRow" data-rkcol="0">'.sprintf($addButton, 'addRow').$closeDivTile;
    $innerRows = array();
    for ($i=0; $i<$height; $i++) {
      $innerRows[$i]  = $colBreaker.vsprintf($openDivTile, array(0, ''));
      $innerRows[$i] .= vsprintf($rmvButton, array(self::CST_RMVROW, 'row', $i+1)).$closeDivTile;
    }
    for ($i=1; $i<=$width; $i++) {
      $firstRow .= vsprintf($openDivTile, array($i, self::CST_FIRSTROW));
      $firstRow .= vsprintf($rmvButton, array(self::CST_RMVCOL, 'col', $i)).$closeDivTile;
      $lastRow  .= vsprintf($openDivTile, array($i, self::CST_FIRSTROW)).$disabledButton.$closeDivTile;
    }
    $arrOrientations = array('N'=>'north', 'E'=>'east', 'S'=>'south', 'O'=>'west');
    for ($i=0; $i<$height; $i++) {
      for ($j=1; $j<=$width; $j++) {
        $name = 'tile_'.$j.'_'.($i+1).'-';
        $orientation = $Mission->getTileOrientation($j, $i+1);
        switch ($orientation) {
          case 'N' :
            $classImg = ' north';
          break;
          case 'E' :
            $classImg = ' east';
          break;
          case 'S' :
            $classImg = ' south';
          break;
          case 'O' :
            $classImg = ' west';
          break;
          default : break;
        }
        $tileCode = $Mission->getTileCode($j, $i+1);
        $innerRows[$i] .= vsprintf($openDivTile, array($j, ''));
        $innerRows[$i] .= '<img class="thumbTile'.$classImg.'" src="/wp-content/plugins/zombicide/web/rsc/images/tiles/';
        $innerRows[$i] .= $Mission->getTileCode($j, $i+1).'-500px.png" alt="'.$tileCode.'">';
        $innerRows[$i] .= $this->TileServices->getTilesSelect(__FILE__, __LINE__, $tileCode, $name, $this->classe, false, '--');
        foreach ($arrOrientations as $key => $value) {
          $innerRows[$i] .= '<button type="button" class="rdv '.$value.($orientation==$key ? ' active' : '');
          $innerRows[$i] .= '" data-action="'.$key.'" data-col="'.$j.'" data-row="'.($i+1).'"></button>';
        }
        $innerRows[$i] .= $closeDivTile;
      }
      $innerRows[$i] .= vsprintf($openDivTile, array($width+1, '')).$disabledButton.$closeDivTile;
    }
    $firstRow .= vsprintf($openDivTile, array($width+1, self::CST_FIRSTROW)).sprintf($addButton, 'addCol').$closeDivTile;
    $lastRow .= vsprintf($openDivTile, array($width+1, self::CST_FIRSTROW)).$disabledButton.$closeDivTile;
    $returned = '<div class="row tileRow" data-width="'.$Mission->getWidth().'" data-height="';
    return $returned.$Mission->getHeight().'">'.$firstRow.implode('', $innerRows).$lastRow.'</div>';
  }
  private function getMissionObjAndRuleGenericBlock($Objs, $none, $type, $select)
  {
    $Mission = $this->Mission;
    $str = '';
    if (empty($Objs)) {
      $str .= '<li>'.$none.'</li>';
    } else {
      foreach ($Objs as $id => $Obj) {
        $str .= '<li class="showTooltip"><span class="tooltip"><header>'.$Obj->getTitle();
        $str .= ' <button class="btn btn-xs btn-danger float-right" data-type="'.$type;
        $str .= '" data-id="'.$id.'"><i class="fas fa-times-circle"></i></button></header><content>';
        $str .= $Obj->getDescription().'</content></span></li>';
      }
    }
    $str .= '<li class="showTooltip"><span class="tooltip"><header><div class="input-group"><input type="text" id="'.$type;
    $str .= '-title" name="'.$type.'-title" class="form-control"><div class="input-group-append">';
    $str .= '<button class="btn btn-success float-right" data-type="'.$type;
    $str .= '" data-missionid="'.$Mission->getId().'"><i class="fas fa-plus-circle"></i></button></div></div></header><content>';
    $str .= $select;
    return $str.'<textarea id="'.$type.'-description" name="'.$type.'-description" class="form-control"></textarea></content></span></li>';
  }
  /**
   * @return string
   */
  public function getMissionRulesBlock()
  {
    $this->MissionRules = $this->Mission->getMissionRules();
    $displayMissionRules = array();
    if (!empty($this->MissionRules)) {
      foreach ($this->MissionRules as $MissionRule) {
        $Rule = $MissionRule->getRule();
        if ($Rule->getSetting()==1) {
          continue;
        }
        $displayMissionRules[$MissionRule->getId()] = $MissionRule;
      }
    }
    $none = '<li>Aucune règle spéciale</li>';
    $type = 'rule';
    $select = $this->RuleServices->getRuleNoSettingSelect('', 'id', $this->classe);
    return $this->getMissionObjAndRuleGenericBlock($displayMissionRules, $none, $type, $select);
  }
  /**
   * @return string
   */
  public function getMissionSettingsBlock()
  {
    if (!empty($this->MissionRules)) {
      foreach ($this->MissionRules as $MissionRule) {
        $Rule = $MissionRule->getRule();
        if ($Rule->getSetting()==0) {
          continue;
        }
        $displayMissionRules[$MissionRule->getId()] = $MissionRule;
      }
    }
    $none = '<li>Aucune mise en place particulière</li>';
    $type = 'setting';
    $select = $this->RuleServices->getRuleSettingSelect('', 'id', $this->classe);
    return $this->getMissionObjAndRuleGenericBlock($displayMissionRules, $none, $type, $select);
  }
  /**
   * @return string
   */
  public function getMissionObjectivesBlock()
  {
    $this->MissionObjectives = $this->Mission->getMissionObjectives();
    if (!empty($this->MissionObjectives)) {
      foreach ($this->MissionObjectives as $MissionObjective) {
        $displayMissionObjectives[$MissionObjective->getId()] = $MissionObjective;
      }
    }
    $none = '<li>Aucun objectif</li>';
    $type = 'objective';
    $select = $this->ObjectiveServices->getObjectiveSelect('', 'id', $this->classe);
    return $this->getMissionObjAndRuleGenericBlock($displayMissionObjectives, $none, $type, $select);
  }
  /**
   * @return string
   */
  public function displayCanvas()
  {
    $Mission = $this->Mission;
    $strCanvas = '<canvas id="canvas-background" width="'.($Mission->getWidth()*500).'" height="'.($Mission->getHeight()*500).'"></canvas>';
    $strCanvas .= '<script src="/wp-content/plugins/zombicide/web/rsc/jcanvas.min.js"></script>';
    $strCanvas .= '<script>';
    $strCanvas .= "var srcImg ='/wp-content/plugins/zombicide/web/rsc/images/missions/".$Mission->getCode().".jpg';\r\n";
    $strCanvas .= "var xStart ='".($Mission->getWidth()*250)."';\r\n";
    $strCanvas .= "var yStart ='".($Mission->getHeight()*250)."';\r\n";
    return $strCanvas.'</script>';
  }


}
