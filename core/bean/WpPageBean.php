<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * WpPageBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.05.06
 */
class WpPageBean extends MainPageBean
{
  protected $urlTemplateDropdown = 'web/pages/public/fragments/dropdown-nbperpages.php';
  protected $urlTemplateNavPagination = 'web/pages/public/fragments/nav-pagination.php';
  /**
   * WpPost affiché
   * @var WpPost $WpPage
   */
  protected $WpPage;
  /**
   * @param string $post
   */
  public function __construct($post='')
  {
    if ($post=='') {
      $post = get_post();
    }
    if (get_class($post)=='WpPost') {
      $this->WpPage = $post;
    } else {
      $this->WpPage = WpPost::convertElement($post);
    }
    parent::__construct();
  }
  /**
   * @return string|Error404PageBean
   */
  public function getContentPage()
  {
    switch ($this->WpPage->getPostName()) {
      case self::PAGE_EQUIPMENT         :
        $Bean = new WpPageEquipmentsBean($this->WpPage);
        $strReturned = $Bean->getContentPage();
      break;
      case self::PAGE_MISSION           :
        $Bean = new WpPageMissionsBean($this->WpPage);
        $strReturned = $Bean->getContentPage();
      break;
      case self::PAGE_SELECT_SURVIVORS  :
        $Bean = new WpPageToolsBean($this->WpPage);
        $strReturned = $Bean->getSelectSurvivorsContent();
      break;
      case self::PAGE_SKILL             :
        $Bean = new WpPageSkillsBean($this->WpPage);
        $strReturned = $Bean->getContentPage();
      break;
      case self::PAGE_SPAWN             :
        $Bean = new WpPageSpawnsBean($this->WpPage);
        $strReturned = $Bean->getContentPage();
      break;
      case self::PAGE_SURVIVOR          :
        $Bean = new WpPageSurvivorsBean($this->WpPage);
        $strReturned = $Bean->getContentPage();
      break;
      default                          :
        if ($this->isAdmin()) {
          echo "[[".$this->WpPage->getPostName()."]]";
        }
        $Bean = new WpPageError404Bean();
        $strReturned = $Bean->getContentPage();
      break;
    }
    return $strReturned;
  }
  /**
   * {@inheritDoc}
   * @see MainPageBean::getShellClass()
   */
  public function getShellClass()
  { return ''; }


  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  protected function getDropdownNbPerPages()
  {
    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      ($this->nbperpage==10 ? self::CST_SELECTED : ''),
      ($this->nbperpage==25 ? self::CST_SELECTED : ''),
      ($this->nbperpage==50 ? self::CST_SELECTED : ''),
    );
    return $this->getRender($this->urlTemplateDropdown, $args);
  }
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Méthodes relatives à la pagination
  /**
   * Retourne le bloc de pagination complet
   * @return string
   */
  protected function getNavPagination()
  {
    /////////////////////////////////////////////////////////////////////////////
    // On construit les liens de la pagination.
    $strPagination = $this->getPaginateLis($this->paged, $this->nbPages);
    /////////////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////
    // On enrichi le template puis on le restitue.
    $args = array(
      // Nb Total - 1
      $this->nbElements,
      // Si page 1, on peut pas revenir à la première - 2
      ($this->paged==1 ? ' '.self::CST_DISABLED : ''),
      // Liste des éléments de la Pagination - 3
      $strPagination,
      // Si page $nbPages, on peut pas aller à la dernière - 4
      ($this->paged==$this->nbperpage ? ' '.self::CST_DISABLED : ''),
      // Nombre de pages - 5
      $this->nbperpage,
      // S'il n'y a qu'une page, la pagination ne sert à rien - 6
      ($this->nbPages<=1 ? ' '.self::CST_HIDDEN : ''),
    );
    return $this->getRender($this->urlTemplateNavPagination, $args);
  }
  /**
   * Retourne la liste des liens numérotés d'une pagination
   * @param int $curPage Page courante
   * @param int $nbPages Nombre de pages
   * @return string
   */
  private function getPaginateLis($curPage, $nbPages)
  {
    $strPagination = '';
    //////////////////////////////////////////////////////////////////////////
    // On renseigne la page 1
    $strPagination .= $this->buildPaginationElement(1, $curPage);
    //////////////////////////////////////////////////////////////////////////

    $hasPrevIgnore = false;
    $hasNextIgnore = false;

    for ($i=2; $i<$nbPages; $i++) {
      if ($i<$curPage-1) {
        if (!$hasPrevIgnore) {
          $strPagination .= $this->buildPaginationElement('...', '...');
        }
        $hasPrevIgnore = true;
      } elseif ($i>$curPage+1) {
        if (!$hasNextIgnore) {
          $strPagination .= $this->buildPaginationElement('...', '...');
        }
        $hasNextIgnore = true;
      } else {
        $strPagination .= $this->buildPaginationElement($i, $curPage);
      }
    }

    //////////////////////////////////////////////////////////////////////////
    // On renseigne la page 12
    return $strPagination.$this->buildPaginationElement($nbPages, $curPage);
  }
  private function buildPaginationElement($i, $curPage)
  {
    $attributes = array(
      self::ATTR_CLASS => 'page-link '.self::CST_AJAXACTION,
      self::ATTR_HREF  => '#',
      self::ATTR_DATA_PAGED => $i,
      self::ATTR_DATA_AJAXACTION => self::AJAX_PAGED,
    );
    $label = $this->getBalise(self::TAG_A, $i, $attributes);
    if ($i=='...') {
      $attrClass = ' '.self::CST_DISABLED;
    } elseif ($i==$curPage) {
      $attrClass = ' '.self::CST_ACTIVE;
    } else {
      $attrClass = '';
    }
    $argsBalise = array(self::ATTR_CLASS => 'page-item'.$attrClass);
    return $this->getBalise(self::TAG_LI, $label, $argsBalise);
  }
  // Fin des méthodes relatives à la pagination
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


  /**
   * @param array $post
   * @param string $fieldNameTitle
   */
  public function setBeanFilters($post=null, $fieldNameTitle='')
  {
    $this->arrFilters = array();
    if (isset($post[self::CST_FILTERS])) {
      $arrParams = explode('&', $post[self::CST_FILTERS]);
      while (!empty($arrParams)) {
        $arrParam = array_shift($arrParams);
        list($key, $value) = explode('=', $arrParam);
        if ($value!='') {
          $this->arrFilters[$key]= $value;
        }
      }
    }
    $this->paged     = (isset($post[self::AJAX_PAGED]) ? $post[self::AJAX_PAGED] : 1);
    $this->colSort   = (isset($post[self::CST_COLSORT]) ? $post[self::CST_COLSORT] : $fieldNameTitle);
    $this->colOrder  = (isset($post[self::CST_COLORDER]) ? $post[self::CST_COLORDER] : self::ORDER_ASC);
    $this->nbperpage = (isset($post[self::CST_NBPERPAGE]) ? $post[self::CST_NBPERPAGE] : 10);
  }
  /**
   * @return string
   */
  public function getBeanExpansionFilters($expansionId='', $fieldToCheck=0)
  {
    $selExpansionsId = explode(',', $expansionId);
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters();
    $strReturned = '';
    while (!empty($Expansions)) {
      $Expansion = array_shift($Expansions);
      if ($Expansion->getField($fieldToCheck)==0)
      { continue; }
      $strReturned .= $this->getOption($Expansion->getId(), $Expansion->getName(), $selExpansionsId);
    }
    return $strReturned;
  }
  /**
   * @return string
   */
  public function getBeanSkillFilters($color='', $skillId='')
  {
    switch ($color) {
      case self::COLOR_BLUE :
        $label = 'Bleues';
        $tagLevelIds = '10,11';
      break;
      case self::COLOR_YELLOW :
        $label = 'Jaunes';
        $tagLevelIds = '20';
      break;
      case self::COLOR_ORANGE :
        $label = 'Oranges';
        $tagLevelIds = '30,31';
      break;
      case self::COLOR_RED :
        $label = 'Rouges';
        $tagLevelIds = '40,41,42';
      break;
      default :
        $label = 'Toutes';
        $tagLevelIds = '';
      break;
    }
    $strReturned = $this->getOption('', $label, $skillId);
    if ( $tagLevelIds!='') {
      $filters = array(self::FIELD_TAGLEVELID=>$tagLevelIds);
      $Skills = $this->SkillServices->getSkillsWithFiltersIn($filters);
    } else {
      $Skills = $this->SkillServices->getSkillsWithFilters();
    }
    while (!empty($Skills)) {
      $Skill = array_shift($Skills);
      $strReturned .= $this->getOption($Skill->getId(), $Skill->getName(), $skillId);
    }
    return $strReturned;
  }

  protected function getOption($value, $name, $selection=array())
  {
    $strOption = '<option value="'.$value.'"';
    if (!is_array($selection)) {
      $selection = array($selection);
    }
    if (in_array($value, $selection)) {
      $strOption .= ' selected';
    }
    return $strOption.'>'.$name.'</option>';
  }
}
