<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Survivor
 * @author Hugues.
 * @since 1.0.00
 * @version 1.05.07
 */
class Survivor extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Nom de la donnée
   * @var string $name
   */
  protected $name;
  /**
   * A un profil Standard
   * @var int $standard
   */
  protected $standard;
  /**
   * A un profil Zombivor
   * @var int $zombivor
   */
  protected $zombivor;
  /**
   * A un profil Ultimate
   * @var int $ultimate
   */
  protected $ultimate;
  /**
   * A un profil Ultimate Zombivor
   * @var int $ultimatez
   */
  protected $ultimatez;
  /**
   * Id de l'extension
   * @var int $expansionId
   */
  protected $expansionId;
  /**
   * Background du Survivant
   * @var string $background
   */
  protected $background;
  /**
   * Eventuelle image alternative
   * @var string $altImgName
   */
  protected $altImgName;
  /**
   * Le Survivant peut-il être joué en ligne ?
   * @var int $liveAble
   */
  protected $liveAble;

  public function __construct($attributes=array())
  {
    parent::__construct($attributes);
    $this->imgBaseUrl = 'http://www.jhugues.fr/wp-content/plugins/hj-zombicide/web/rsc/img/portraits/p';
    $this->SurvivorSkills = array();
  }
  /**
   * @return int
   */
  public function getId()
  {return $this->id; }
  /**
   * @return string
   */
  public function getName()
  { return $this->name; }
  /**
   * @return boolean
   */
  public function isStandard()
  { return ($this->standard==1); }
  /**
   * @return boolean
   */
  public function isZombivor()
  { return ($this->zombivor==1); }
  /**
   * @return boolean
   */
  public function isUltimate()
  { return ($this->ultimate==1); }
  /**
   * @return boolean
   */
  public function isUltimatez()
  { return ($this->ultimatez==1); }
  /**
   * @return int
   */
  public function getExpansionId()
  { return $this->expansionId; }
  /**
   * @return string
   */
  public function getBackground()
  { return $this->background; }
  /**
   * @return string
   */
  public function getAltImgName()
  { return $this->altImgName; }
  /**
   * @return boolean
   */
  public function isLiveAble()
  { return ($this->liveAble==1); }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param string $name
   */
  public function setName($name)
  { $this->name=$name; }
  /**
   * @param int $standard
   */
  public function setStandard($standard)
  { $this->standard=$standard; }
  /**
   * @param int $zombivor
   */
  public function setZombivor($zombivor)
  { $this->zombivor=$zombivor; }
  /**
   * @param int $ultimate
   */
  public function setUltimate($ultimate)
  { $this->ultimate=$ultimate; }
  /**
   * @param int $ultimatez
   */
  public function setUltimatez($ultimatez)
  { $this->ultimatez=$ultimatez; }
  /**
   * @param int $expansionId
   */
  public function setExpansionId($expansionId)
  { $this->expansionId=$expansionId; }
  /**
   * @param string $background
   */
  public function setBackground($background)
  { $this->background=$background; }
  /**
   * @param string $altImgName
   */
  public function setAltImgName($altImgName)
  { $this->altImgName=$altImgName; }
  /**
   * @param int $liveAble
   */
  public function setLiveAble($liveAble)
  { $this->liveAble=$liveAble; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Survivor'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return Survivor
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Survivor(), self::getClassVars(), $row); }
  /**
   * @return Bean
   */
  public function getBean()
  { return new SurvivorBean($this); }

  ////////////////////////////////////////////////////////////////////////////
  // Méthodes relatives à l'article WpPost
  /**
   * @return WpPost
   */
  public function getWpPost()
  {
    $args = array(
      self::WP_METAKEY   => self::FIELD_SURVIVORID,
      self::WP_METAVALUE => $this->id,
      self::WP_TAXQUERY  => array(),
    );
    if (MainPageBean::isAdmin()) {
      $args[self::WP_POSTSTATUS] = self::WP_PUBLISH.', future';
    }
    $WpPosts = $this->WpPostServices->getArticles($args);
    return (!empty($WpPosts) ? array_shift($WpPosts) : new WpPost());
  }
  /**
   * @return string
   */
  public function getWpPostUrl()
  { return $this->getWpPost()->getPermalink(); }
  ////////////////////////////////////////////////////////////////////////////

  ////////////////////////////////////////////////////////////////////////////
  // Méthodes relatives aux Portraits
  /**
   * @param string $str
   * @return string
   */
  public function getNiceName($str='')
  {
    if ($str=='') {
      $str = $this->name;
    }
    return str_replace(array(' ', '#'), '', strtolower($str));
  }
  /**
   * @param string $type
   * @return string
   */
  public function getPortraitUrl($type='')
  {
    if ($this->getAltImgName()!='') {
      $name = $this->getAltImgName();
    } else {
      $name = $this->name;
    }
    $wholeUrl = $this->imgBaseUrl.$this->getNiceName($name).($type!='' ? '-'.$type : '').'.jpg';
    if (self::isAdmin() && @getimagesize($wholeUrl)===false) {
      $wholeUrl = $this->imgBaseUrl.($type!='' ? '-'.$type : '').'.jpg';
    }
    return $wholeUrl;
  }
  ////////////////////////////////////////////////////////////////////////////

  protected function initSurvivorSkills()
  {
    $SurvivorSkills = $this->SurvivorSkillServices->getSurvivorSkillsWithFilters(array(self::FIELD_SURVIVORID=>$this->getId()));
    while (!empty($SurvivorSkills)) {
      $SurvivorSkill = array_shift($SurvivorSkills);
      $survivorTypeId = $SurvivorSkill->getSurvivorTypeId();
      $tagLevelId = $SurvivorSkill->getTagLevelId();
      if (!isset($this->SurvivorSkills[$survivorTypeId])) {
        $this->SurvivorSkills[$survivorTypeId] = array();
      }
      $this->SurvivorSkills[$survivorTypeId][$tagLevelId] = $SurvivorSkill->getSkill();
    }
  }

  public function getSkill($type, $rank)
  {
    if ($this->SurvivorSkills == null) {
      $this->SurvivorSkills = array();
      $this->initSurvivorSkills();
    }
    return ($this->SurvivorSkills[$type][$rank]==null ? new Skill() : $this->SurvivorSkills[$type][$rank]);
  }





  /**
   * @param int $survivorTypeId
   * @return boolean
   */
  public function areDataSkillsOkay($survivorTypeId=1)
  {
    // On récupère les Compétences associées au Survivant pour le profil passé en paramètre.
    $SurvivorSkills = $this->getSurvivorSkills($survivorTypeId);
    $nbSkills = count($SurvivorSkills);
    // On doit avoir 7 (profils standards et zombivants) ou
    // 8 (ultimate survivant et zombivant, ou standard avec Descente en Rappel ou Pilote d'Hélicoptère) compétences de retournées.
    // Si on a ce nombre de compétences, on retourne true. Sinon false.
    return ($nbSkills==7 || $nbSkills==8);
  }
  /**
   * @return array SurvivorSkill
   */
  public function getSurvivorSkills($survivorTypeId='')
  {
    if ($this->SurvivorSkills == null) {
      $arrFilters = array(self::FIELD_SURVIVORID=>$this->id);
      if ($survivorTypeId!='') {
        $arrFilters[self::FIELD_SURVIVORTYPEID] = $survivorTypeId;
      }
      $this->SurvivorSkills = $this->SurvivorSkillServices->getSurvivorSkillsWithFilters($arrFilters);
    }
    return $this->SurvivorSkills;
  }

  public function getAdminUlSkills($survivorTypeId=1)
  {
    $args = array(
      self::FIELD_SURVIVORID     => $this->getId(),
      self::FIELD_SURVIVORTYPEID => $survivorTypeId,
    );
    $SurvivorSkills = $this->SurvivorSkillServices->getSurvivorSkillsWithFilters($args);
    $strReturned = '<ul class="col-12">';
    while (!empty($SurvivorSkills)) {
      $SurvivorSkill = array_shift($SurvivorSkills);
      if ($SurvivorSkill->getSurvivorTypeId()!=$survivorTypeId) {
        continue;
      }
      $strReturned .= '<li><span>'.$SurvivorSkill->getBean()->getBadge().'</span></li>';
    }
    return $strReturned.'</ul>';
  }





  /**
   * @param bool $isHome
   * @return string
   */
  public function getStrClassFilters($isHome)
  { return 'col-12 col-md-6 col-xl-4'; }
  /**
   * Retourne si le type de Survivant associé au SurvivorSkill est bien celui attendu.
   * @param string $type Le type recherché
   * @param SurvivorSkill $SurvivorSkill
   * @return boolean
   */
  public function controlTypeAndSkill($type, $SurvivorSkill)
  {
    return ($type=='' && $SurvivorSkill->getSurvivorTypeId()!=1 ||
        $type=='z' && $SurvivorSkill->getSurvivorTypeId()!=2 ||
        $type=='u' && $SurvivorSkill->getSurvivorTypeId()!=3 ||
        $type=='uz' && $SurvivorSkill->getSurvivorTypeId()!=4);
  }
  /**
   * @param string $type
   * @param boolean $withLink
   * @return string
   */
  public function getUlSkills($type='', $withLink=false)
  {
    if ($type=='' && !$this->isStandard() && $this->isUltimate()) {
      $type='u';
    }
    $SurvivorSkills = $this->getSurvivorSkills();
    $str = '';
    $strTmp = '';
    if (!empty($SurvivorSkills)) {
      foreach ($SurvivorSkills as $SurvivorSkill) {
        if ($this->controlTypeAndSkill($type, $SurvivorSkill)) {
          continue;
        }
        switch ($SurvivorSkill->getTagLevelId()) {
          case 20 :
          case 30 :
          case 40 :
            $str .= '<ul class="col-12 col-sm-6 col-lg-3">'.$strTmp.'</ul>';
            $strTmp = '';
          break;
          default :
          break;
        }
        $strTmp .= $this->getSkillLi($SurvivorSkill, $withLink);
      }
      $str .= '<ul class="col-12 col-sm-6 col-lg-3">'.$strTmp.'</ul>';
    }
    return $str;
  }
  private function getSkillLi($SurvivorSkill, $withLink)
  {
    switch ($SurvivorSkill->getTagLevelId()) {
      case 10 :
      case 11 :
        $strColor = 'blue';
      break;
      case 20 :
        $strColor = 'yellow';
      break;
      case 30 :
      case 31 :
        $strColor = 'orange';
      break;
      case 40 :
      case 41 :
      case 42 :
        $strColor = 'red';
      break;
      default :
        $strColor = '';
      break;
    }
    $str = '<li><span';
    if ($withLink) {
      $str .= '><a class="badge badge-'.$strColor.'-skill" href="/page-competences/?skillId=';
      $str .= $SurvivorSkill->getSkillId().'">'.$SurvivorSkill->getSkillName().'</a>';
    } else {
      $str .= ' class="badge badge-'.$strColor.'-skill">'.$SurvivorSkill->getSkillName();
    }
    return $str.'</span></li>';
  }
}
