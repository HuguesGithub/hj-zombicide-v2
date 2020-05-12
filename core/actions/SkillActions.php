<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * SkillActions
 * @author Hugues
 * @since 1.04.00
 * @version 1.05.12
 */
class SkillActions extends LocalActions
{
  /**
   * Constructeur
   */
  public function __construct($post=array())
  {
    parent::__construct();
    $this->post = $post;
    $this->ExpansionServices = new ExpansionServices();
    $this->SkillServices  = new SkillServices();
    $this->WpPostServices = new WpPostServices();
  }
  /**
   * Point d'entrée des méthodes statiques.
   * @param array $post
   * @return string
   **/
  public static function dealWithStatic($post)
  {
    $returned = '';
    $Act = new SkillActions($post);
    switch ($post[self::CST_AJAXACTION]) {
      case self::AJAX_GETSKILLS    :
        $returned = $Act->dealWithGetSkills();
      break;
      case self::AJAX_SKILLVERIF   :
        $returned = $Act->dealWithSkillVerif(true);
      break;
      default :
        $returned  = 'Erreur dans SkillActions > dealWithStatic, '.$_POST[self::CST_AJAXACTION].' inconnu.';
      break;
    }
    return $returned;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  // Bloc de gestion de vérifications des Compétences en Home Admin
  /**
   * @param boolean $isVerif
   * @return string
   */
  public function dealWithSkillVerif($isVerif=false)
  {
    // On récupère les articles de compétences
    $args = array(
      self::WP_CAT         => self::WP_CAT_SKILL_ID,
      self::WP_TAXQUERY    => array(),
      self::WP_POSTSTATUS  => self::WP_PUBLISH.', future',
    );
    $this->WpPostSkills = $this->WpPostServices->getArticles($args);
    $nbWpPostSkills = count($this->WpPostSkills);
    // Et les compétences en base
    $this->Skills = $this->SkillServices->getSkillsWithFilters();
    $nbSkills = count($this->Skills);

    if ($isVerif) {
      $this->checkSkills();
      $strBilan = $this->jsonString($this->strBilan, self::AJAX_SKILLVERIF, true);
    } elseif ($nbWpPostSkills!=$nbSkills) {
      $strBilan  = "Le nombre d'articles ($nbWpPostSkills) ne correspond pas au nombre de compétences en base ($nbSkills).";
      $strBilan .= "<br>Une vérification est vivement conseillée.";
    } else {
      $strBilan = "Le nombre d'articles ($nbWpPostSkills) correspond au nombre de compétences en base.";
    }
    return $strBilan;
  }
  private function checkSkills()
  {
    // On regarde les articles créés et on vérifie les données en base, si elles existent et si elles sont cohérentes entre elles.
    while (!empty($this->WpPostSkills)) {
      // On récupère le WpPost et ses données
      $this->WpPost = array_shift($this->WpPostSkills);
      $name = $this->WpPost->getPostTitle();
      $code = $this->WpPost->getPostMeta(self::FIELD_CODE);
      // On recherche un Skill dans la base de données qui correspond.
      $Skills       = $this->SkillServices->getSkillsWithFilters(array(self::FIELD_CODE=>$code));
      if (empty($Skills)) {
        // Si on n'en a pas, on doit créer une entrée correspondante.
        $Skill = new Skill();
        $Skill->setCode($code);
        $Skill->setName($name);
        $description  = $this->WpPost->getPostContent();
        $description  = substr($description, 25, -27);
        $Skill->setDescription($description);
        $expansionId  = $this->getExpansionId();
        $Skill->setExpansionId($expansionId);
        $this->SkillServices->insertSkill($Skill);
        $this->strBilan .= '<br>Compétence créée en base : '.$name.'.';
      } else {
        // Si on en a juste une, c'est tranquille.
        $Skill = array_shift($Skills);
        $this->checkSkill($Skill);
      }
    }
    // Puis, on regarde les données en base et on vérifie que des articles ont été créés pour elles.
    while (!empty($this->Skills)) {
      // On récupère l'extension.
      $Skill = array_shift($this->Skills);
      $Wp_post = get_page_by_title($Skill->getName(), OBJECT, self::WP_POST);
      $WpPost = WpPost::convertElement($Wp_post);
      if ($WpPost->getID()=='') {
        $this->strBilan .= '<br>Article à créer pour une Compétence  : '.$Skill->getName().' ['.$Skill->toJson().'].';
      }
    }
    if ($this->strBilan=='') {
      $this->strBilan = 'Il semblerait que tout aille à la perfection. Aucune anomalie remontée.';
    }
  }
  private function getExpansionId()
  {
    $postId        = $this->WpPost->getPostMeta(self::FIELD_EXPANSIONID);
    $Wp_post = get_post($postId);
    $WpPost = WpPost::convertElement($Wp_post);
    $codeExpansion = $WpPost->getPostMeta(self::FIELD_CODE);
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(self::FIELD_CODE=>$codeExpansion));
    $Expansion = array_shift($Expansions);
    return $Expansion->getId();
  }
  private function checkSkill($Skill)
  {
    // On initialise les données
    $doUpdate = false;
    $code          = $this->WpPost->getPostMeta(self::FIELD_CODE);
    $name          = $this->WpPost->getPostTitle();
    $description   = $this->WpPost->getPostContent();
    $description   = substr($description, 25, -27);
    $expansionId   = $this->getExpansionId();
    // On vérifie si la donnée en base correspond à l'article.
    if ($Skill->getCode()!=$code) {
      $Skill->setCode($code);
      $doUpdate = true;
    }
    if ($Skill->getName()!=$name) {
      $Skill->setName($name);
      $doUpdate = true;
    }
    if ($Skill->getDescription()!=$description) {
      $Skill->setDescription($description);
      $doUpdate = true;
    }
    if ($Skill->getExpansionId()!=$expansionId) {
      $Skill->setExpansionId($expansionId);
      $this->strBilan .= 'Compétence mise à jour au niveau de l extension : '.$name.' - '.$expansionId.'.<br>';
      $doUpdate = true;
    }
    if ($doUpdate) {
      // Si nécessaire, on update en base.
      $this->SkillServices->updateSkill($Skill);
      $this->strBilan .= 'Compétence mise à jour : '.$name.'.<br>';
    }
  }
  // Fin du bloc relatif à la vérification des compétences sur la Home Admin.
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * Récupération du contenu de la page via une requête Ajax.
   * @param array $post
   * @return string
   */
  public function dealWithGetSkills()
  {
    $Bean = new WpPageSkillsBean();
    $Bean->setFilters($this->post);
    return $this->jsonString($Bean->getListContentPage(), self::PAGE_SKILL, true);
  }
}
