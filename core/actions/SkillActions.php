<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * SkillActions
 * @author Hugues
 * @since 1.04.00
 * @version 1.08.01
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
      $strBilan  = "Le nombre d'articles ($nbWpPostSkills) ne correspond pas au nombre de compétences en base ($nbSkills).<br>";
      $strBilan .= "Une vérification est vivement conseillée.";
    } else {
      $strBilan = "Le nombre d'articles ($nbWpPostSkills) correspond au nombre de compétences en base.";
    }
    return $strBilan;
  }
  private function checkSkills()
  {
    $hasErrors = false;
    $strErrors = '';
    $this->strBilan  = "Début de l'analyse des données relatives aux Compétences.<br>";
    $this->strBilan .= "Il y a ".count($this->WpPostSkills)." articles de Compétences.<br>";
    $this->strBilan .= "Il y a ".count($this->Skills)." entrées en base.<br>";
    /////////////////////////////////////////////////////////////////////
    // On va réorganiser les Skills pour les retrouver facilement
    $arrSkills = array();
    while (!empty($this->Skills)) {
      $Skill = array_shift($this->Skills);
      if (isset($arrSkills[$Skill->getCode()])) {
        $strErrors .= "Le code <em>".$Skill->getCode()."</em> semble être utilisé deux fois dans la base de données.<br>";
        $hasErrors = true;
      }
      $arrSkills[$Skill->getCode()] = $Skill;
    }
    /////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////
    while (!empty($this->WpPostSkills)) {
      // On regarde les articles créés et on vérifie les données en base, si elles existent et si elles sont cohérentes entre elles.
      // On récupère le WpPost et ses données
      $this->WpPost = array_shift($this->WpPostSkills);
      $name = $this->WpPost->getPostTitle();
      $code = $this->WpPost->getPostMeta(self::FIELD_CODE);
      if (!isset($arrSkills[$code])) {
        // A priori l'article n'a pas de code associé en base. Il faut donc en créé un qui corresponde
        $Skill = new Skill();
        $Skill->setCode($code);
        $Skill->setName($name);
        $description  = $this->WpPost->getPostContent();
        $Skill->setDescription($description);
        $expansionId = $this->getExpansionId();
        $Skill->setExpansionId($expansionId);
        // On insère la donnée et on log dans le bilan
        $this->SkillServices->insertSkill($Skill);
        $this->strBilan .= "L'article <em>".$name."</em> a été créé en base.<br>";
        continue;
      }
      $Skill = $arrSkills[$code];
      unset($arrSkills[$code]);
      $this->checkSkill($Skill);
    }
    /////////////////////////////////////////////////////////////////////
    // On vérifie que la totalité des Compétences en base ont été utilisées. Si ce n'est pas le cas, il faut créer des articles correspondants.
    if (!empty($arrSkills)) {
      $this->strBilan .= "On a des données en base qui n'ont pas d'article correspondant.<br>";
      while (!empty($arrSkills)) {
        $Skill = array_shift($arrSkills);
        $this->strBilan .= '<br>Article à créer pour une Compétence  : '.$Skill->getName().' ['.$Skill->toJson().'].';
      }
    }
    /////////////////////////////////////////////////////////////////////
    $this->strBilan .= "Fin de l'analyse des données relatives aux Compétences.<br>";
    if ($hasErrors) {
      $this->strBilan .= "Anomalies constatées :<br>".$strErrors;
    } else {
      $this->strBilan .= "Aucune anomalie constatée.";
    }
  }
  private function getExpansionId()
  {
    $postId  = $this->WpPost->getPostMeta(self::FIELD_EXPANSIONID);
    $Wp_post = get_post($postId);
    $WpPost  = WpPost::convertElement($Wp_post);
    $codeExpansion = $WpPost->getPostMeta(self::FIELD_CODE);
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(self::FIELD_CODE=>$codeExpansion));
    $Expansion = array_shift($Expansions);
    return $Expansion->getId();
  }
  private function checkSkill($Skill)
  {
    $doUpdate = false;
    // On initialise les données de l'article
    $name          = $this->WpPost->getPostTitle();
    $description   = $this->WpPost->getPostContent();
    $expansionId   = $this->getExpansionId();
    // On vérifie si la donnée en base correspond à l'article.
    $strError = '';
    if ($Skill->getName()!=$name) {
      $Skill->setName($name);
      $doUpdate = true;
      $strError .= "Le Nom a été mis à jour.<br>";
    }
    if ($Skill->getDescription()!=$description) {
      $Skill->setDescription($description);
      $doUpdate = true;
      $strError .= "La description a été mise à jour.<br>";
    }
    if ($Skill->getExpansionId()!=$expansionId) {
      $Skill->setExpansionId($expansionId);
      $doUpdate = true;
      $strError .= "L'extension a été mise à jour.<br>";
    }
    if ($doUpdate) {
      // Si nécessaire, on update en base.
      $this->SkillServices->updateSkill($Skill);
      $this->strBilan .= "Les données de la Compétence <em>".$name."</em> ont été mises à jour.<br>".$strError;
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
