<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * ExpansionActions
 * @author Hugues
 * @since 1.05.09
 * @version 1.08.01
 */
class ToolActions extends LocalActions
{
  /**
   * Constructeur
   */
  public function __construct($post=array())
  {
    parent::__construct();
    $this->post = $post;
    $this->TileServices = new TileServices();
    $this->debug = '';
  }
  /**
   * Point d'entrée des méthodes statiques.
   * @param array $post
   * @return string
   **/
  public static function dealWithStatic($post)
  {
    $returned = '';
    $Act = new ToolActions($post);
    if ($post[self::CST_AJAXACTION]==self::AJAX_GETTHROWDICE) {
      $returned = $Act->dealWithThrowDice();
    } elseif ($post[self::CST_AJAXACTION]==self::AJAX_GETRANDOMMAP) {
      $returned = $Act->dealWithRandomMap();
    } elseif ($post[self::CST_AJAXACTION]=='getBuildingMap') {
      $returned = $Act->dealWithBuildingMap();
    } elseif ($post[self::CST_AJAXACTION]=='getNonUsedTiles') {
      $returned = $Act->dealWithNonUsedTiles();
    } elseif ($post[self::CST_AJAXACTION]=='getEmptyCell') {
      $returned = $Act->getEmptyCell();
    } elseif ($post[self::CST_AJAXACTION]=='getImageMap') {
      $returned = $Act->getImageMap();
    } else {
      $returned  = 'Erreur dans ToolActions > dealWithStatic, '.$_POST[self::CST_AJAXACTION].' inconnu.';
    }
    return $returned;
  }

  public function getImageMap()
  {
    $this->initPost();
    $urlTarget = '/wp-content/plugins/hj-zombicide/web/rsc/img/maps/'.date('Y-m-d_H:i:s').'-map.png';
    $targetPath = getcwd().'/..'.$urlTarget;

    $im = @imagecreatetruecolor(500*$this->width, 500*$this->height);

    for ($row=0; $row<$this->height; $row++) {
      for ($col=0; $col<$this->width; $col++) {
        if (isset($this->lockedMapTiles[$row][$col])) {
          $MapTile = $this->lockedMapTiles[$row][$col];
          $srcFile = getcwd().'/../wp-content/plugins/hj-zombicide/web/rsc/img/tiles/'.$MapTile->getTile()->getCode().'-500px.png';
          $imgTile = @imagecreatefrompng($srcFile);
          switch ($MapTile->getOrientation()) {
            case 'left' :
              $angle = 90;
            break;
            case 'bottom' :
              $angle = 180;
            break;
            case 'right' :
              $angle = 270;
            break;
            default :
              $angle = 0;
            break;
          }
          $rotate = imagerotate($imgTile, $angle, 0);
          imagecopymerge($im, $rotate, 500*$col, 500*$row, 0, 0, 500, 500, 100);
        }
      }
    }

    imagepng($im, $targetPath);
    imagedestroy($im);
    return $urlTarget;
  }
  private function getStrEmptyCell()
  {
    $result  = '<div class="cell hidden cellModel"><img alt="Non définie">';
    $result .= '<nav class="hoverActions nav nav-fill nav-pills"><i class="nav-item far fa-check-square fakeCb" data-cell="cell_0_0"></i>';
    $result .= '<i class="nav-item fas fa-unlock fakeLock" data-lock="cell_0_0"></i><i class="nav-item fas fa-cog"></i>';
    $result .= '<i class="nav-item fas fa-undo"></i><i class="nav-item fas fa-redo"></i></nav></div>';
    return $result;
  }
  public function getEmptyCell()
  { return $this->jsonString($this->getStrEmptyCell(), 'empty-cell', true); }

  private function initPost()
  {
    //////////////////////////////////////////////////////////////////////
    // Initialisation des variables
    $params = $this->post['params'];
    $arrParams = explode('&', $params);
    while (!empty($arrParams)) {
      $param = array_shift($arrParams);
      list($key, $value) = explode('=', $param);
      $this->{$key} = $value;
    }
    $this->expansionIds = str_replace('set-', '', $this->expansionIds);
    $this->cellIds = explode(',', $this->cells);
    // On initialise la MapTile.
    $this->lockedMapTiles = array();
    $this->lockedOCodes   = array();
    $locks = explode(',', $this->locks);
    while (!empty($locks)) {
      $lock = array_shift($locks);
      list($label, $row, $col, $code, $orientation) = explode('_', $lock);
      $Tiles = $this->TileServices->getTilesWithFilters(array(self::FIELD_CODE=>$code));
      $Tile = array_shift($Tiles);
      $MapTile = new MapTile($Tile, $orientation, true);
      $this->lockedMapTiles[$row][$col] = $MapTile;
      $this->debug .= 'On a une Tile : '.$Tile->getCode().'<br>';
      array_push($this->lockedOCodes, $Tile->getCode());
      array_push($this->lockedOCodes, $Tile->getOCode());
    }
  }
  public function dealWithNonUsedTiles()
  {
    $this->initPost();
    // L'actuelle :
    list($label, $row, $col, $code, $orientation) = explode('_', $this->current);
    $Tiles = $this->TileServices->getTilesWithFilters(array(self::FIELD_CODE=>$code));
    $Tile = array_shift($Tiles);
    $code = $Tile->getCode();
    $oCode = $Tile->getOCode();

    $arrTmp = array_keys($this->lockedOCodes, $code);
    unset($this->lockedOCodes[$arrTmp[0]]);
    $arrTmp = array_keys($this->lockedOCodes, $oCode);
    unset($this->lockedOCodes[$arrTmp[0]]);

    //////////////////////////////////////////////////////////////////////
    // On récupère les Dalles que l'on veut pouvoir utiliser..
    $Tiles = $this->TileServices->getTilesWithFiltersIn(array(self::FIELD_EXPANSIONID=>$this->expansionIds));
    $lstTiles = '';
    while (!empty($Tiles)) {
      $Tile = array_shift($Tiles);
      if (in_array($Tile->getCode(), $this->lockedOCodes)) {
        continue;
      }
      // TODO : la Tile est-elle compatible avec les Tiles adjacentes ? Pour chacune des orientations ?
      $this->orientation = 'top';
      $MapTile = new MapTile($Tile, $this->orientation);
      $isCompatible = $MapTile->isCompatibleV2($this->lockedMapTiles, $row, $col);
      if ($isCompatible) {
        $lstTiles .= '<div class="cell" style="display:inline-block;"><img data-row="'.$row.'" data-col="'.$col.'" data-orientation="'.$this->orientation.'" data-src="'.$Tile->getImgUrl().'" data-code="'.$Tile->getCode().'" class="'.$this->orientation.'" src="'.$Tile->getImgUrl().'"></div>  ';
      }
      $this->getNextOrientation();
      $MapTile = new MapTile($Tile, $this->orientation);
      $isCompatible = $MapTile->isCompatibleV2($this->lockedMapTiles, $row, $col);
      if ($isCompatible) {
        $lstTiles .= '<div class="cell" style="display:inline-block;"><img data-row="'.$row.'" data-col="'.$col.'" data-orientation="'.$this->orientation.'" data-src="'.$Tile->getImgUrl().'" data-code="'.$Tile->getCode().'" class="'.$this->orientation.'" src="'.$Tile->getImgUrl().'"></div>  ';
      }
      $this->getNextOrientation();
      $MapTile = new MapTile($Tile, $this->orientation);
      $isCompatible = $MapTile->isCompatibleV2($this->lockedMapTiles, $row, $col);
      if ($isCompatible) {
        $lstTiles .= '<div class="cell" style="display:inline-block;"><img data-row="'.$row.'" data-col="'.$col.'" data-orientation="'.$this->orientation.'" data-src="'.$Tile->getImgUrl().'" data-code="'.$Tile->getCode().'" class="'.$this->orientation.'" src="'.$Tile->getImgUrl().'"></div>  ';
      }
      $this->getNextOrientation();
      $MapTile = new MapTile($Tile, $this->orientation);
      $isCompatible = $MapTile->isCompatibleV2($this->lockedMapTiles, $row, $col);
      if ($isCompatible) {
        $lstTiles .= '<div class="cell" style="display:inline-block;"><img data-row="'.$row.'" data-col="'.$col.'" data-orientation="'.$this->orientation.'" data-src="'.$Tile->getImgUrl().'" data-code="'.$Tile->getCode().'" class="'.$this->orientation.'" src="'.$Tile->getImgUrl().'"></div>  ';
      }
    }
    $result = '<section class="displayMap proposals" style="width:500px;">'.$lstTiles.'</section>';
    return $this->jsonString($result, self::PAGE_GENERATION_MAP, true);
  }
  public function dealWithBuildingMap()
  {
    $this->initPost();
    unset($this->version);
    $this->post['params'] = str_replace('&version=2', '', $this->post['params']);
    $this->dealWithRandomMap();

    //////////////////////////////////////////////////////////////////////
    $result  = '<div class="overlay"><div class="spinner"></div></div>';
    $result .= $this->getStrEmptyCell();
    $result .= '<section class="displayMap travaux map'.$this->width.'x'.$this->height.' mapWidth'.$this->width.' mb-2">';
    for ($i=0; $i<$this->height; $i++) {
      $result .= '<div class="row">';
      for ($j=0; $j<$this->width; $j++) {
        $result .= '<div class="cell">';
        if (!in_array('cell_'.$i.'_'.$j, $this->cellIds)) {
          if (isset($this->MapTiles[$i][$j])) {
            $MapTile = $this->MapTiles[$i][$j];
            $Tile = $MapTile->getTile();
            $orientation = $MapTile->getOrientation();
            $isLocked = $MapTile->isLocked();
            $result .= '<img class="'.$orientation.'" src="'.$Tile->getImgUrl().'">';
            $dataLock = 'cell_'.$i.'_'.$j.'_'.$Tile->getCode().'_'.$orientation;
          } else {
            $result .= '<img alt="Non définie"/>';
            $dataLock = '';
          }
          $htmlContent  = '<i class="nav-item far fa-check-square fakeCb" data-cell="cell_'.$i.'_'.$j.'"></i>';
          $htmlContent .= '<i class="nav-item fas fa-'.($isLocked ? '' : 'un').'lock fakeLock" data-lock="'.$dataLock.'"></i>';
        } else {
          $result .= '<img alt="Non sélectionnée"/>';
          $htmlContent  = '<i class="nav-item far fa-square fakeCb" data-cell="cell_'.$i.'_'.$j.'"></i>';
          $htmlContent .= '<i class="nav-item fas fa-unlock fakeLock" data-lock=""></i>';
        }
        $htmlContent .= '<i class="nav-item fas fa-cog"></i>';
        $htmlContent .= '<i class="nav-item fas fa-undo"></i>';
        $htmlContent .= '<i class="nav-item fas fa-redo"></i>';
        $result .= '<nav class="hoverActions nav nav-fill nav-pills">'.$htmlContent.'</nav>';
        $result .= '</div>';
      }
      $result .= '</div>';
    }
    $result .= '</section>';

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    $result  = '<section id="page-generation-map" class="row">'.$result.'</section>';
    if (self::isAdmin()) {
      $result .= $this->debug;
    }
    return $this->jsonString($result, self::PAGE_GENERATION_MAP, true);
  }
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  // Bloc de gestion de génération aléatoire de map
  public function dealWithRandomMap()
  {
    //////////////////////////////////////////////////////////////////////
    // Initialisation des variables
    $params = $this->post['params'];
    $arrParams = explode('&', $params);
    while (!empty($arrParams)) {
      $param = array_shift($arrParams);
      list($key, $value) = explode('=', $param);
      $this->{$key} = $value;
    }
    if (isset($this->version) && $this->version==2) {
      return $this->dealWithBuildingMap();
    }
    $result = '<section class="displayMap map'.$this->width.'x'.$this->height.' mapWidth'.$this->width.' mb-2">';
    $this->expansionIds = str_replace('set-', '', $this->expansionIds);

    //////////////////////////////////////////////////////////////////////
    // On récupère les Dalles que l'on veut pouvoir utiliser. Et on les mélange.
    $Tiles = $this->TileServices->getTilesWithFiltersIn(array(self::FIELD_EXPANSIONID=>$this->expansionIds));
    // Puis on construit la Map aléatoire.
    $this->nbTestsGlobals = 0;
    $this->maxTestGlobals = ($this->expansionIds=='' ? 1 : 500);
    $content = '';
    do {
      $this->col = 0;
      $this->row = 0;
      $this->MapTiles = isset($this->lockedMapTiles) ? $this->lockedMapTiles : array();
      $this->arrOCode = isset($this->lockedOCodes) ? $this->lockedOCodes : array();
      shuffle($Tiles);
      $this->pendingTiles = array();
      $this->launchGeneration($Tiles);
    } while (count($this->MapTiles)!=$this->width*$this->height && $this->nbTestsGlobals!=$this->maxTestGlobals && $this->row!=$this->height);
    // Et on l'affiche.
    $hasError = false;
    $nbDalles = 0;
    for ($i=0; $i<$this->height; $i++) {
      for ($j=0; $j<$this->width; $j++) {
        if (isset($this->MapTiles[$i][$j])) {
          $MapTile = $this->MapTiles[$i][$j];
          //print_r($MapTile);
          $Tile = $MapTile->getTile();
          $orientation = $MapTile->getOrientation();
          $result .= '<img class="'.$orientation.'" src="'.$Tile->getImgUrl().'">';
          $this->debug .= '['.$Tile->getCode().'/'.$orientation.']';
          $nbDalles++;
        } else {
          $hasError = true;
        }
      }
    }
    if ($hasError) {
      $content  = '<div class="alert alert-danger"><h4 class="alert-heading">Une erreur est survenue durant la génération</h4>';
      $content .= 'Ca peut arriver sur de grandes maps ou lorsque le nombre de Dalles disponibles est très proche de la taille de la Map.<br>';
      $content .= 'Si dans la ligne de debug ci-dessous, le nombre en face de "Tests Global" est <strong>'.$this->nbTestsGlobals.'</strong>, c\'est que vous avez atteint le nombre de tentatives. Réessayez !';
      $content .= '<hr><p>'.date('H:i:s').' - Dimensions : LxH ['.$this->width.';'.$this->height.'] - Nb de Dalles : '.$nbDalles.', Rebut : '.count($this->pendingTiles).', Tests Global : '.$this->nbTestsGlobals.'</p></div>';
    }
    $result .= $content.'</section>';

    $result  = '<section id="page-generation-map" class="row">'.$result;
    //$result .= '<article>'.$this->debug.'</article>';
    $result .= '</section>';

    return $this->jsonString($result, self::PAGE_GENERATION_MAP, true);
  }

  private function launchGeneration($Tiles)
  {
    $this->nbTestsGlobals++;
    $this->debug .= '-=-=-=-=- GLOBAL TEST ['.$this->nbTestsGlobals.'] -=-=-=-=-=-<br>';
    $this->debug .= 'Row '.$this->row.' / Col '.$this->col.'<br>';
    $this->nbTests = 0;
    // Si on n'a plus de Dalles disponibles et si on a fait trop de tentatives, on quitte.
    // Ou qu'on a rempli la Map
    if (empty($Tiles) || $this->nbTestsGlobals==$this->maxTestGlobals || $this->row==$this->height) {
      return;
    }

    // On prend la première Dalle
    $Tile = array_shift($Tiles);
    // Si le recto de cette Dalle a déjà utilisé dans la Map, on ne peut pas la reprendre.
    if (in_array($Tile->getCode(), $this->arrOCode)) {
      $this->debug .= 'Already used '.$Tile->getCode().'<br>';
      // On passe à la Dalle suivante.
      return $this->launchGeneration($Tiles);
    }
    do {
      // On lui affecte une orientation
      ($this->nbTests==0 ? $this->getRandomOrientation() : $this->getNextOrientation());
      // On défini la MapTile
      if (isset($this->MapTiles[$this->row][$this->col])) {
        $this->debug .= 'MapTile '.$this->row.';'.$this->col.' définie<br>';
        $MapTile = $this->MapTiles[$this->row][$this->col];
      } else {
        $this->debug .= 'MapTile '.$this->row.';'.$this->col.' non définie<br>';
        $MapTile = new MapTile($Tile, $this->orientation);
      }
      $isCompatible = true;

      // On teste si elle est viable.
      $this->debug .= 'Testing '.$Tile->getCode().'/'.$this->orientation.' ['.$this->nbTests.']<br>';
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Début des contrôles pour ajouter la Tile à la Map.
      // Si on n'est pas sur la première colonne, on doit vérifier avec celle de gauche.
      /*
      if ($this->col!=0) {
        //$isCompatible = $MapTile->isCompatible($this->MapTiles, $this->row, $this->col-1);
        $isCompatible = $MapTile->isCompatible($this->MapTiles[$this->row][$this->col-1], 'left');
      }
      // Si on n'est pas sur la première ligne, on doit vérifier avec celle au-dessus.
      if ($isCompatible && $this->row!=0) {
        $isCompatible = $MapTile->isCompatible($this->MapTiles[$this->row-1][$this->col], 'top');
      }
      */
      $isCompatible = $MapTile->isCompatibleV2($this->MapTiles, $this->row, $this->col);
      $this->nbTests++;
      if ($this->nbTests==4) {
        break;
      }
      if ($isCompatible) {
        break;
      }
    } while (true);

    // Soit la DAlle est compatible dans la Map, soit on a essayé les 4 côtés et ça n'a rien donné.
    if (!$isCompatible) {
      // On n'a pas réussi à ajouter cette Dalle, elle ne peut pas être positionnée à cet endroit de la Map.
      $this->debug .= 'Failing to add '.$Tile->getCode().'/'.$this->orientation.'<br>';
      // On met la Dalle de côté.
      array_push($this->pendingTiles, $Tile);
      // On passe à la Dalle suivante.
      return $this->launchGeneration($Tiles);
    } else {
      // On ajoute la Dalle à la liste
      $this->debug .= 'Adding '.$Tile->getCode().'/'.$this->orientation.'<br>';
      // On ajout le code du recto pour l'exclure du champ des possibilités.
      array_push($this->arrOCode, $Tile->getOCode());
      $this->MapTiles[$this->row][$this->col] = $MapTile;
      // On met à jour les coordonnées pour la prochaine Dalle
      $this->upColAndRow();
      // On reprend les Dalles mises de côté et on les met à la fin.
      $Tiles = array_merge($Tiles, $this->pendingTiles);
      // On réinitialise la liste des mises de côté.
      $this->pendingTiles = array();
      // On recherche la Dalle suivante
      return $this->launchGeneration($Tiles);
    }
  }

  private function upColAndRow()
  {
    $this->col++;
    if ($this->col==$this->width) {
      $this->col=0;
      $this->row++;
    }
  }

  private function getNextOrientation()
  {
    switch ($this->orientation) {
      case 'top' :
        $nextOrientation = 'right';
      break;
      case 'right' :
        $nextOrientation = 'bottom';
      break;
      case 'bottom' :
        $nextOrientation = 'left';
      break;
      case 'left' :
        $nextOrientation = 'top';
      break;
      default :
        $nextOrientation = $this->getRandomOrientation();
      break;
    }
    $this->orientation = $nextOrientation;
  }
  private function getRandomOrientation()
  {
    $arrOrientation = array('top', 'right', 'bottom', 'left');
    shuffle($arrOrientation);
    $this->orientation = array_shift($arrOrientation);
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////


  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  // Bloc de gestion du lancer de dés

  public function dealWithThrowDice()
  {
    $Bean = new UtilitiesBean();
    $tag = self::TAG_SPAN;
    $this->modif = 0;
    $this->seuil = 4;
    //////////////////////////////////////////////////////////////////////
    // Initialisation des variables
    $params = $this->post['params'];
    $arrParams = explode('&', $params);
    while (!empty($arrParams)) {
      $param = array_shift($arrParams);
      list($key, $value) = explode('=', $param);
      $this->{$key} = $value;
    }

    // TODO : Prendre en compte "surunsix" et "dual".
    // Si on a un nombre dans Barbare / Mode Automatique, on prend le plus gros score entre le nombre de dés de l'arme et le nombre d'acteurs dans al Zone.
    $this->nbDice = max($this->nbDice, $this->barabauto);

    $arrDice = array();
    for ($i=0; $i<$this->nbDice; $i++) {
      $dice = rand(1, 6);
      if ($dice==1) {
        $color = self::COLOR_RED;
        $dice = min(6,max(1,$dice+$this->modif));
      } else {
        $dice = min(6,max(1,$dice+$this->modif));
        if ($dice>=6) {
          $color = self::COLOR_BLUE;
          $this->nbDice += $this->surunsix;
        } elseif ($dice>=$this->seuil) {
          $color = self::COLOR_YELLOW;
        } else {
          $color = self::COLOR_ORANGE;
        }
      }
      $attributes = array(
        self::ATTR_CLASS => 'badge badge-'.$color.'-skill',
      );

      array_push($arrDice, $Bean->getBalise($tag, $dice, $attributes));
    }

    $result = '';
    while (!empty($arrDice)) {
      $num = array_shift($arrDice);
      $result .= '['.$num.']';
    }
    $result = '<section id="page-piste-de-des">Tirage '.date('d-m-Y H:i:s').' : '.$result.'</section>';
    return $this->jsonString($result, self::PAGE_PISTE_DE_DES, true);
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////

}
