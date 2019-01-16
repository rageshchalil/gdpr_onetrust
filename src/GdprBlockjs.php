<?php

namespace Drupal\onetrust_cookie_blocking;

/**
 * GdprBlockjs implements the onetrust API helpers to block cookies.
 */
class GdprBlockjs {

  private $optanonjs;
  private $insertscript;
  private $inserthtml;

  /**
   * Implement class construct.
   */
  private function __construct() {

  }

  /**
   * Create an singleton factory object.
   */
  public static function instance() {
    static $inst = NULL;
    if ($inst === NULL) {
      $inst = new GdprBlockjs();
    }
    return $inst;
  }

  /**
   * Verify the JS file and insert to the insertscript API.
   */
  public function generateOptanons($thjs) {
    if(is_array($thjs)) {
      foreach ($thjs as $js_key => $js_paths) {
        if ($this->isExistJs($js_paths['data']) === TRUE) {
          $this->optanonjs[] = $js_paths;
          $this->optanonInsertscript($js_paths['data'], $js_paths['gdpr_onetrust']['group']);
        }
      }
    }
  }

  /**
   * Function create Optanon.InsertScript
   * @param string $js_path
   *  The javascript file path
   * @param int $js_group
   *  The OneTrust Cookie Category values
   *  ONETRUST_COOKIE_BLOCKING_PERFORMANCE / ONETRUST_COOKIE_BLOCKING_FUNCTIONAL
   *  / ONETRUST_COOKIE_BLOCKING_TARGETTING
   * @param $position
   *  postion of the js script "head"
   * @param array $additional
   *  Array consisting of 3rd and 4th parameter of Optanon.InsertHTML()
   *  Optional
   *  $additional[0] = 'SomeCallbackFunction'
   *  $additional[1] = '{deleteSelectorContent: false, makeSelectorVisible: true, makeElementsVisible: \'HtmlElementIdToShowOnConsent1\',
   *   \'HtmlElementIdToShowOnConsent2\', deleteElements: \'HtmlElementIdToDeleteOnConsent\']}'
   */
  public function optanonInsertscript($js_path, $js_group, $position = "head", $additional = array()) {
    global $base_root;

    $additional_0 = (isset($additional[0]))?'|'.$additional[0]:'';
    $additional_1 = (isset($additional[1]))?'|'.$additional[1]:'';
    $parsed_url_data = parse_url($js_path);
    if (!isset($parsed_url_data['scheme']) && !isset($parsed_url_data['host'])) {
      $js_path = $base_root . '/' . $js_path;
    }

    $this->insertscript .= $js_path . '|' . $position . '|' . $js_group . $additional_0 . $additional_1 . ';';
  }

  /**
   * Verify the Javascript file from the config array.
   */
  private function isExistJs($jsdata) {
    if (is_array($this->optanonjs)) {
      foreach ($this->optanonjs as $key => $js) {
        if (isset($js['data']) && $js['data'] === $jsdata) {
          return FALSE;
        }
      }
    }
    return TRUE;
  }

  /**
   * Function create Optanon.InsertHtml
   * @param $element
   *  The HTML to be placed rendered through Optanon.InsertHtml(),
   *  that fires a third party cookie.
   * @param string $selector
   *  The wrapper div id.
   * @param INT $groupid
   *  The OneTrust Cookie Category values
   *  ONETRUST_COOKIE_BLOCKING_PERFORMANCE/ONETRUST_COOKIE_BLOCKING_FUNCTIONAL/ONETRUST_COOKIE_BLOCKING_TARGETTING
   * @param array $additional
   *  Array consisting of 3rd and 4th parameter of Optanon.InsertHTML()
   *  Optional
   * $additional[0] / $additional[1]
   */
  public function optanonInserthtml($element, $selector,  $groupid, $additional = array()) {

    $additional[0] = ($additional[0] != "")?$additional[0]:'';
    $additional[1] = ($additional[1] != "")?$additional[1]:'';
    $addhtml =  $element .  ', ' . $selector . ', '.$additional[0].', '.$additional[1].', ' . $groupid . '|';
    $this->inserthtml .= $addhtml;
  }

  /**
   * Implement the optanon wrapper function to page head section.
   */
  public function optanoPublishScript() {
    if ($this->isGdprScope() == TRUE) {
      return $this->insertscript;
    }
  }

  /**
   * Implement the optanon wrapper function to page head section.
   */
  public function optanoPublishHtml() {
    if ($this->isGdprScope() == TRUE) {
      return $this->inserthtml;
    }
  }

  /**
   * Check the current language in GDPR Scopr or not.
   */
  public function isGdprScope() {
    $languages = \Drupal::languageManager()->getLanguages();
    $gdpr_config = \Drupal::config('gdpr_onetrust.settings');
    $current_language_id = \Drupal::languageManager()->getCurrentLanguage()->getId();
    if (count($languages) == 1 && !is_null($gdpr_config->get('gdpr_onetrust_compliance_uuid'))) {
      $current_language_uuid = $gdpr_config->get('gdpr_onetrust_compliance_uuid');
    }
    else {
      $current_language_uuid = $gdpr_config->get('gdpr_onetrust_compliance_uuid_' . $current_language_id);
    }
    return empty($current_language_uuid) ? FALSE : TRUE;
  }

}
