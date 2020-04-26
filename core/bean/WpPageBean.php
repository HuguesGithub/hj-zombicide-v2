<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * WpPageBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.04.26
 */
class WpPageBean extends MainPageBean
{
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
      /*
      case self::PAGE_ONLINE            :
        $strReturned = WpPageOnlineBean::getStaticPageContent($this->WpPage);
      break;
      case 'page-live-pioche-equipment' :
        $strReturned = WpPageLiveEquipmentBean::getStaticPageContent($this->WpPage);
      break;
      case 'page-live-pioche-invasion' :
        $strReturned = WpPageLiveSpawnBean::getStaticPageContent($this->WpPage);
      break;
      case 'page-market'               :
        $strReturned = WpPageMarketBean::getStaticPageContent($this->WpPage);
      break;
      case 'page-partie-online'        :
        $strReturned = new WpPageError404Bean();
      break;
      case 'page-piste-de-des'         :
        $strReturned = WpPageToolsBean::getStaticPisteContent($this->WpPage);
      break;
      */
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

  /**
   * Retourne la liste des liens numérotés d'une pagination
   * @param int $curPage Page courante
   * @param int $nbPages Nombre de pages
   * @return string
   */
  protected function getPaginateLis($curPage, $nbPages)
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
}
