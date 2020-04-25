<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Survivor
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
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

  /**
   * @param array $attributes
   */
  public function __construct($attributes=array())
  {
    parent::__construct($attributes);
    $this->ExpansionServices     = new ExpansionServices();
    $this->SkillServices         = new SkillServices();
    $this->SurvivorSkillServices = new SurvivorSkillServices();
    $this->WpPostServices        = new WpPostServices();
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
  public function isZombivor()
  { return ($this->zombivor==1); }
  /**
   * @return boolean
   */
  public function isUltimate()
  { return ($this->ultimate==1); }
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
   * @return array SurvivorSkill
   */
  public function getSurvivorSkills($survivorTypeId='')
  {
    if ($this->SurvivorSkills == null) {
      $arrFilters = array('survivorId'=>$this->id);
      if ($survivorTypeId!='') {
        $arrFilters['survivorTypeId'] = $survivorTypeId;
      }
      $this->SurvivorSkills = $this->SurvivorSkillServices->getSurvivorSkillsWithFilters($arrFilters);
    }
    return $this->SurvivorSkills;
  }
  /**
   * @return Expansion
   */
  public function getExpansion()
  {
    if ($this->Expansion == null) {
      $this->Expansion = $this->getExpansionFromGlobal($this->expansionId);
    }
    return $this->Expansion;
  }
  /**
   * @return string
   */
  public function getExpansionName()
  { return $this->getExpansion()->getName(); }
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
    $nicename = $this->getNiceName($this->name);
    $baseUrl = '/wp-content/plugins/zombicide/web/rsc/images/portraits/p';
    return $baseUrl.$nicename.($type!=''?'-'.$type:'').'.jpg';
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
  /**
   * @return string
   */
  public function getWpPostUrl()
  {
    $url = '#';
    $args = array('meta_key'=>self::FIELD_SURVIVORID, 'meta_value'=>$this->id);
    if (MainPageBean::isAdmin()) {
      $args['post_status'] = 'publish, future';
    }
    $WpPosts = $this->WpPostServices->getArticles($args);
    if (!empty($WpPosts)) {
      $WpPost = array_shift($WpPosts);
      $url = $WpPost->getPermalink();
    }
    return $url;
  }

  public function getBean()
  { return new SurvivorBean($this); }
}
