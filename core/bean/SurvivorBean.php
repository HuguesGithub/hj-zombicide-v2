<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.07.20
 */
class SurvivorBean extends LocalBean
{
  protected $urlRowAdmin  = 'web/pages/admin/fragments/survivor-row.php';
  protected $urlRowPublic = 'web/pages/public/fragments/survivor-row.php';
  protected $urlArticle   = 'web/pages/public/fragments/survivor-article.php';
  /**
   * @param Survivor $Survivor
   */
  public function __construct($Survivor=null)
  {
    parent::__construct();
    $this->Survivor = ($Survivor==null ? new Survivor() : $Survivor);
    $this->ExpansionServices = new ExpansionServices();
    $this->SurvivorServices  = new SurvivorServices();
  }

  //////////////////////////////////////////////////////////////////////////
  // Différentes modes de présentation des Survivants
  /**
   * @return string
   */
  public function getRowForAdminPage()
  {
    ///////////////////////////////////////////////////////////////////////////
    // On enrichit le template
    $args = array(
      // Identifiant du Survivant - 1
      $this->Survivor->getId(),
      // Portraits - 2
      $this->getAllPortraits(),
      // Url d'édition - 3
      $this->Survivor->getEditUrl(self::CST_SURVIVOR),
      // Nom du Survivant - 4
      $this->Survivor->getName(),
      // Url d'édition du WpPost - 5
      $this->Survivor->getWpPostEditUrl(),
      // Article publié ? - 6
      $this->Survivor->getWpPostUrl(),
      // Liste des profils existants - 7
      $this->getListeProfils(),
      // Extension de provenance - 8
      $this->Survivor->getExpansion()->getName(),
      // Background du Survivant - 9
      ($this->Survivor->getBackground()!='' ? substr($this->Survivor->getBackground(), 0, 50).'...' : 'Non renseigné'),
      // Nom de l'image alternative, si défini. - 10
      $this->Survivor->getAltImgName(),
      // Type du Survivant - 11
      (!$this->Survivor->isStandard() ? ' ultimate' : ''),
    );
    ///////////////////////////////////////////////////////////////
    // Puis on le retourne
    return $this->getRender($this->urlRowAdmin, $args);
  }
  /**
   * @return string
   */
  public function getRowForPublicPage()
  {
    ///////////////////////////////////////////////////////////////
    // On enrichit le template et on le retourne.
    $args = array(
      // Id du Survivant - 1
      $this->Survivor->getId(),
      // Les portraits du Survivants - 2
      $this->getAllPortraits(),
      // Url du WpPost associé, s'il existe - 3
      $this->Survivor->getWpPostUrl(),
      // Nom du Survivant - 4
      $this->Survivor->getName(),
      // Si on a un profil de Zombivant, on donne la possibilité de l'afficher - 5
      ($this->Survivor->isZombivor()?self::CST_CHANGEPROFILE:''),
      // Si on a un profil de Zombivant, on veut une case à cocher - 6
      ($this->Survivor->isZombivor()?self::CST_SQUAREPOINTER:self::CST_WINDOWCLOSE),
      // Si on a un profil d'Ultimate, on donne la possibilité de l'afficher - 7
      ($this->Survivor->isUltimate()?self::CST_CHANGEPROFILE:''),
      // Si on a un profil d'Ultimate, on veut une case à cocher - 8
      ($this->Survivor->isUltimate()?self::CST_SQUAREPOINTER:self::CST_WINDOWCLOSE),
      // Extension à laquelle est rattaché le Survivant - 9
      $this->Survivor->getExpansion()->getName(),
      // Liste des Compétences du Survivant - 10
      $this->getAllSkills(),
      // Background du Survivant - 11
      $this->Survivor->getBackground(),
      // Classe additionnelle, pour la ligne - 12
      (!$this->Survivor->isStandard() ? ' ultimate' : ''),
    );
    return $this->getRender($this->urlRowPublic, $args);
  }
  /**
   * @return string
   */
  public function getContentForHome()
  {
    //////////////////////////////////////////////////////////////////
    // Liste des profils du Survivants.
    $checked = false;
    $strProfiles  = '';
    $strSkills = '';
    $name = self::CST_SURVIVOR.'-skill-'.$this->Survivor->getId();
    if ($this->Survivor->isStandard()) {
      $strProfiles .= '<li class="active">'.$this->getFormRadioBouton('survivant', self::LBL_SURVIVANT).'</li>';
      $strSkills   .= $this->getBalise(self::TAG_UL, $this->Survivor->getUlSkills('', false, true), array(self::ATTR_CLASS=>'colSkills skills-survivant'));
    }
    if ($this->Survivor->isZombivor()) {
      $strProfiles .= '<li>'.$this->getFormRadioBouton('zombivant', self::LBL_ZOMBIVANT).'</li>';
      $strSkills   .= $this->getBalise(self::TAG_UL, $this->Survivor->getUlSkills('z', false, true), array(self::ATTR_CLASS=>'colSkills skills-zombivant'));
    }
    if ($this->Survivor->isUltimate()) {
      $strProfiles .= $this->getFormRadioBouton(self::CST_ULTIMATE, self::LBL_ULTIMATE, self::CST_CHANGEPROFILE, $checked);
    }
    if ($this->Survivor->isUltimatez()) {
      $strProfiles .= $this->getFormRadioBouton(self::CST_ULTIMATEZ, self::LBL_ULTIMATEZOMBIVANT, self::CST_CHANGEPROFILE, $checked);
    }
    //////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////
    // On enrichit le template
    $args = array(
      // Url de l'article - 1
      $this->Survivor->getWpPostUrl(),
      // Url du portrait du Survivant - 2
      $this->Survivor->getPortraitUrl(),
      // Url vers la page Survivants - 3
      '/'.self::PAGE_SURVIVOR,
      // Nom du Survivant - 4
      $this->Survivor->getName(),
      // Les Compétences du Survivant - 5
      $strSkills,
      // Liste des profils du Survivant - 6
      '<ul>'.$strProfiles.'</ul>',
      // Plus utilisé - 7
      '',
      // Background du Survivant - 8
      $this->Survivor->getBackground(),
      // Classe additionnelle de l'article - 9
      $this->Survivor->getStrClassFilters().' '.$this->Survivor->getExpansion()->getCode(),
      // Le Nom de l'extension - 10
      $this->Survivor->getExpansion()->getName(),
    );
    ///////////////////////////////////////////////////////////////
    // Puis on le retourne
    return $this->getRender($this->urlArticle, $args);
  }















  protected $urlFormRadioBouton = 'web/pages/public/fragments/form-radio-bouton.php';
  protected $urlCardVisit = 'web/pages/public/fragments/survivor-cardvisit.php';
  protected $tplSkillBadge = '<a class="badge badge-%1$s-skill" href="%2$s">%3$s</a>';
  protected $tplDisabledSkillBadge = '<span class="badge badge-%1$s-skill">%3$s</span>';
  private $strPortraitSurvivant = 'portrait-survivant';
  private $strPortraitZombivant = 'portrait-zombivant';
  private $strPortraitUltimate  = ' portrait-ultimate';


  //////////////////////////////////////////////////////////////////////////
  private function getFormRadioBouton($value, $libelle)
  {
    return '<div class="form-check badge badge-outline changeProfile" data-type="'.$value.'">'.$libelle.'</div>';
  }



  /**
   * @return string
   */
  public function getButton()
  {
    $label = $this->getIconFarSquare().' '.$this->Survivor->getName();
    $attributes = array(
      self::ATTR_TYPE             => self::TAG_BUTTON,
      self::ATTR_CLASS            => 'btn btn-light btn-survivor hidden',
      self::ATTR_DATA_EXPANSIONID => $this->Survivor->getExpansionId(),
      self::ATTR_DATA_SURVIVORID  => $this->Survivor->getId(),
    );
    return $this->getBalise(self::TAG_BUTTON, $label, $attributes);
  }







  /**
   * @param string $color
   * @return string
   */
  public function getSkillBadge($color)
  {
    ////////////////////////////////////////////////////////////////////
    // On enrichi les paramètres du template et on le retourne
    $args = array(
      $color,
      $this->Survivor->getWpPostUrl(),
      $this->Survivor->getName()
    );
    return vsprintf(($this->Survivor->getWpPostUrl()=='#' ? $this->tplDisabledSkillBadge : $this->tplSkillBadge), $args);
  }
  public function getPortrait($type='')
  { return $this->getStrImgPortrait($this->Survivor->getPortraitUrl($type), 'Portrait', ''); }
  /**
   * @return string
   */
  public function getAllPortraits($displayedFiltered=true)
  {
    $Survivor = $this->Survivor;
    $name = $Survivor->getName();
    $str = '';
    if ($Survivor->isStandard()) {
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl(), 'Portrait Survivant - '.$name, ($displayedFiltered?$this->strPortraitSurvivant:''));
    }
    if ($Survivor->isZombivor()) {
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl('z'), 'Portrait Zombivant - '.$name, ($displayedFiltered?$this->strPortraitZombivant:''));
    }
    if ($Survivor->isUltimate()) {
      $label = $this->strPortraitSurvivant.$this->strPortraitUltimate;
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl('u'), 'Portrait Ultimate - '.$name, ($displayedFiltered?$label:''));
    }
    if ($Survivor->isUltimatez()) {
      $label = $this->strPortraitZombivant.$this->strPortraitUltimate;
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl('uz'), 'Portrait ZUltimate - '.$name, ($displayedFiltered?$label:''));
    }
    return $str;
  }
  private function getProfileLi($type, $survivorTypeId, $label)
  {
    $strProfils = '<li data-id="'.$this->Survivor->getId().'" data-type="'.$type.'" class="hasTooltip pointer"> ';
    if ($this->Survivor->areDataSkillsOkay($survivorTypeId)) {
      $strProfils .= $this->getIconFarCheckSquare().' '.$label.' <div class="tooltip">';
      $strProfils .= $this->Survivor->getAdminUlSkills($survivorTypeId).'</div>';
    } else {
      $strProfils .=  $this->getIconFarWindowClose().' '.$label;
    }
    return $strProfils.'</li>';
  }
  public function getListeProfils()
  {
    $strProfils  = '<ul>';
    // A-t-il un profil Standard ?
    if ($this->Survivor->isStandard()) {
      $strProfils .= $this->getProfileLi('survivant', self::CST_SURVIVORTYPEID_S, 'Standard');
    }
    // A-t-il un profil Zombivant ?
    if ($this->Survivor->isZombivor()) {
      $strProfils .= $this->getProfileLi('zombivant', self::CST_SURVIVORTYPEID_Z, 'Zombivant');
    }
    // A-t-il un profil Ultimate ?
    if ($this->Survivor->isUltimate()) {
      $strProfils .= $this->getProfileLi('ultimate survivant', self::CST_SURVIVORTYPEID_U, 'Ultimate');
    }
    // A-t-il un profil UltimateZ ?
    if ($this->Survivor->isUltimatez()) {
      $strProfils .= $this->getProfileLi('ultimate zombivant', self::CST_SURVIVORTYPEID_UZ, 'Ultimate Zombivant');
    }
    return $strProfils.'</ul>';
  }
  /**
   * @param string $src
   * @param string $alt
   * @param string $addClass
   * @return string
   */
  private function getStrImgPortrait($src, $alt, $addClass)
  {
    $attributes = array(
      self::ATTR_SRC => $src,
      self::ATTR_ALT => $alt,
      self::ATTR_CLASS => 'thumb '.$addClass,
    );
    return $this->getBalise(self::TAG_IMG, '', $attributes);
  }
  public function getSkills($type='')
  { return $this->getSkillsBySurvivorType('row', $this->Survivor->getUlSkills($type, true)); }
  /**
   * @return string
   */
  public function getAllSkills()
  {
    $Survivor = $this->Survivor;
    $str = $this->getSkillsBySurvivorType('skills-survivant row', $Survivor->getUlSkills('', true));
    if ($Survivor->isZombivor()) {
      $str .= $this->getSkillsBySurvivorType('skills-zombivant row', $Survivor->getUlSkills('z', true));
    }
    if ($Survivor->isUltimate()) {
      $str .= $this->getSkillsBySurvivorType('skills-ultimate skills-survivant row', $Survivor->getUlSkills('u', true));
      $str .= $this->getSkillsBySurvivorType('skills-ultimate skills-zombivant row', $Survivor->getUlSkills('uz', true));
    }
    return $this->getBalise(self::TAG_UL, $str);
  }
  /**
   * @param string $addClass
   * @param string $content
   * @return string
   */
  private function getSkillsBySurvivorType($addClass, $content)
  { return $this->getBalise(self::TAG_LI, $content, array(self::ATTR_CLASS=>$addClass)); }
  /**
   * @return string
   */
  public function getCheckBoxType()
  {
    $strType  = '';
    if ($this->Survivor->isZombivor()) {
      $attributes = array(
        self::ATTR_DATA_ID => $this->Survivor->getId(),
        self::ATTR_DATA_TYPE => 'zombivant',
        self::ATTR_CLASS => 'changeProfile',
      );
      $strType .= $this->getBalise(self::TAG_DIV, $this->getIconFarSquarePointer().' Zombivant', $attributes);
      if ($this->Survivor->isUltimate()) {
        $attributes[self::ATTR_DATA_TYPE] = self::FIELD_ULTIMATE;
        $strType .= '&nbsp;'.$this->getBalise(self::TAG_DIV, $this->getIconFarSquarePointer().' Ultimate', $attributes);
      }
    }
    return $strType;
  }

  public function getCartouche($extraAttributes=array(), $linked=false)
  {
    $content = $this->getStrImgPortrait($this->Survivor->getPortraitUrl(), '', '').' '.$this->Survivor->getName();
    $attributes = array(
      self::ATTR_CLASS => 'cartouche',
    );
    if (!empty($extraAttributes)) {
      $attributes = array_merge($attributes, $extraAttributes);
    }
    if ($linked) {
      $tag = self::TAG_A;
      $attributes[self::ATTR_HREF] = $this->Survivor->getWpPost()->getPermalink();
    } else {
      $tag = self::TAG_SPAN;
    }
    return $this->getBalise($tag, $content, $attributes);
  }


}
