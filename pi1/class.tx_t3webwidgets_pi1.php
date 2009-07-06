<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Steffen Kamper <info@sk-typo3.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'T3 Web Widgets' for the 't3webwidgets' extension.
 *
 * @author	Steffen Kamper <info@sk-typo3.de>
 * @package	TYPO3
 * @subpackage	tx_t3webwidgets
 */
class tx_t3webwidgets_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_t3webwidgets_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_t3webwidgets_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 't3webwidgets';	// The extension key.
	var $pi_checkCHash = true;
	protected $id;
	var $html;
	
	protected $sysJsFiles = array();
	protected $jsFiles = array();
	protected $jsInline = array();
	
	protected $cssFiles = array();
	protected $sysCssFiles = array();
	protected $cssInline = array();
	
	
	protected $settings = array();
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->id = $this->cObj->data['uid'];
		
		$fConf = array();
		$this->readFlexformIntoConf($this->cObj->data['pi_flexform'], $fConf);
	#debug($fConf);
	#debug(array($this->cObj->data['pi_flexform'],$fConf));
		
		$this->sysCssFiles[] = 'typo3/contrib/extjs/resources/css/ext-all-notheme.css';
		if ($this->conf['extJStheme']) {
			$this->sysCssFiles[] = $this->conf['extJStheme'];
		}
		
		$this->sysJsFiles[] = 'typo3/contrib/extjs/adapter/ext/ext-base.js';
		$this->sysJsFiles[] = 'typo3/contrib/extjs//ext-all-debug.js';
		$this->jsFiles[] = 'SETTINGS';
		
		if (is_array($fConf['Widgets.'])) {
			foreach($fConf['Widgets.'] as $key => $val) {
				switch ($key) {
					case 'Twitter':
						foreach ($val as $tkey => $conf) {
							$this->twitter($conf, intval($tkey));
						}
					break;
					case 'Flickr':
						foreach ($val as $akey => $conf) {
				   			$this->flickr($conf, intval($akey));
						}
				   	break;  			
					case 'Accordion':
						foreach ($val as $akey => $conf) {
				   			$this->accordion($conf, intval($akey));
						}
				   	break;  			
				}
			}
		}
		
		$content = $this->render();
	
		return $this->pi_wrapInBaseClass($content);
	}
	
	protected function render() {
		$this->sysCssFiles = array_unique($this->sysCssFiles);
		$this->sysJsFiles = array_unique($this->sysJsFiles);
		$this->cssFiles = array_unique($this->cssFiles);
		$this->jsFiles = array_unique($this->jsFiles);
		
		/* add system files unique */
		$sys = $GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '_systemcss'];
		foreach ($this->sysCssFiles as $file) {
			if (strpos($sys, $file) === false) {
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '_systemcss'] .= '
					<link rel="stylesheet" type="text/css" href="' . $file . '" />';
			}
		}
		
		$sys = $GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '_systemjs'];
		foreach ($this->sysJsFiles as $file) {
			if (strpos($sys, $file) === false) {
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '_systemjs'] .= '
					<script type="text/javascript" src="' . $file. '"></script>';
			}
		}
		
		/* add individual files */
		foreach ($this->cssFiles as $file) {
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $this->id . '_css'] .= '
	<link rel="stylesheet" type="text/css" href="' . $file . '" />';
		}
		
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $this->id . '_css'] .= '
	<style type="text/css">
	' . implode(chr(10), $this->cssInline) . '
	</style>';
	
		foreach ($this->jsFiles as $file) {
			if ($file == 'SETTINGS') {
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $this->id . '_js'] .='
	<script type="text/javascript">
		Ext.namespace(\'T3WIDGETS\');
		T3WIDGETS.settings = ' . json_encode($this->settings) . ';
	</script>';
			} else {
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $this->id . '_js'] .= '
	<script type="text/javascript" src="' . $file. '"></script>';
		}
			}
			

		if ($this->jsInline) {
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $this->id . '_js'] .= '
				<script type="text/javascript">
				' . $this->jsInline . '
				</script>';
		}
		
		
		return $this->html;
	}
	
	protected function twitter($conf, $key) {
		$count = 10;
		$page = 1;
		$search = 'typo3';
		
		
		if (intval($conf['twitterType']) == 0) {
			$url = 'http://twitter.com/statuses/user_timeline.json?screen_name=' . $conf['twitterKeyword'];
		} else {
			$url = 'http://search.twitter.com/search.json?q=' . $conf['twitterKeyword'] . '&rpp=15';
		}
		$this->settings['twitter_' .  $this->id . '_' . $key] = array(
			'url' => $url .  '&count=' . $count . '&page=' . $page,
			'div' => 'twitter-grid-' . $this->id,
			'width' => intval($conf['twitterWidth']),
			'height' => intval($conf['twitterHeight']),
			'imageWidth' => intval($conf['twitterImageWidth']),
			'interval' => $conf['twitterInterval'] * 1000,
		);
		
		$this->cssFiles[] = 'typo3conf/ext/t3webwidgets/widgets/twitter/twitter.css';
		if ($this->conf['twitter.']['additionalCSSfile']) {
 			$this->cssFiles[] = $this->conf['twitter.']['additionalCSSfile'];		
		}
		
		$js = t3lib_div::getURL(t3lib_extMgm::extPath($this->extKey) . 'widgets/twitter/twitter.js');
		$js = strtr($js, array(
			'###INDEX###' =>  $this->id . '_' . $key,
		));
		$js = t3lib_div::minifyJavaScript($js, $error);
		$this->jsInline .= chr(10) . $js;
		
		$html = t3lib_div::getURL(t3lib_extMgm::extPath($this->extKey) . 'widgets/twitter/twitter.html');
		$html = strtr($html, array(
			'###ID###' => $this->id,
			'###INDEX###' => $key,
		));
		$this->html .= $html;
	}   
	
	
	protected function flickr($conf, $key) {
		$this->settings['flickr_' . $this->id . '_' . $key] = array(
			'div' => 'flickr-' . $this->id . '-' . $key,
			'width' => intval($conf['flickrWidth']),
			'height' => intval($conf['flickrHeight']),
			'keyword' => trim($conf['flickrKeyword']),
			'title' => $conf['flickrTitle'],
			'picHeight' => $conf['flickrPicHeight'],
			'lightbox' => intval($conf['flickrLightbox']),
			'style' => intval($conf['flickrStyle']),
			'mode' => intval($conf['flickrMode']),
		);
		
		if ($conf['flickrLightbox']) {
			$this->sysJsFiles[] = 'typo3conf/ext/t3webwidgets/ux/lightbox.js';
			$this->sysCssFiles[] = 'typo3conf/ext/t3webwidgets/ux/lightbox.css'; 
		}
		if ($conf['flickrStyle'] > 0) {
			$this->sysJsFiles[] = 'typo3conf/ext/t3webwidgets/ux/carousel.js';
			$this->sysCssFiles[] = 'typo3conf/ext/t3webwidgets/ux/carousel.css'; 
		}
		
		#$this->jsFiles[] = 'typo3conf/ext/t3webwidgets/widgets/flickr/flickr.js';
		$this->cssFiles[] = 'typo3conf/ext/t3webwidgets/widgets/flickr/flickr.css';
		
		
		if ($this->conf['accordion.']['additionalCSSfile']) {
 			$this->cssFiles[] = $this->conf['twitter.']['additionalCSSfile'];		
		}
		
		$js = t3lib_div::getURL(t3lib_extMgm::extPath($this->extKey) . 'widgets/flickr/flickr.js');
		$js = strtr($js, array(
			'###INDEX###' =>  $this->id . '_' . $key,
		));
		$js = t3lib_div::minifyJavaScript($js, $error);
		$this->jsInline .= chr(10) . $js;
		
		$html = t3lib_div::getURL(t3lib_extMgm::extPath($this->extKey) . 'widgets/flickr/flickr.html');
		$html = strtr($html, array(
			'###ID###' => $this->id,
			'###INDEX###' =>  $key,
		));
		$this->html .= $html;
		
	}
	
	protected function accordion($conf, $key) {
		$count = 10;
		$page = 1;
		
		$this->settings['accordion_' . $this->id . '_' . $key] = array(
			'div' => 'accordion-' . $this->id . '-' . $key,
			'width' => intval($conf['accordionWidth']),
			'height' => intval($conf['accordionHeight']),
			'title' => $conf['accordionTitle'],
		);
		
		$this->jsFiles[] = 'typo3conf/ext/t3webwidgets/widgets/accordion/accordion.js';
		$this->cssFiles[] = 'typo3conf/ext/t3webwidgets/widgets/accordion/accordion.css';
		
		if ($this->conf['accordion.']['additionalCSSfile']) {
 			$this->cssFiles[] = $this->conf['twitter.']['additionalCSSfile'];		
		}
		
		/*  render of CEs */
		$records = $this->conf['renderRecords.'];
		$records['source'] = $conf['accordionCEs'];
		
		$content = $this->cObj->cObjGetSingle('RECORDS', $records);


		$html = t3lib_div::getURL(t3lib_extMgm::extPath($this->extKey) . 'widgets/accordion/accordion.html');
		$html = strtr($html, array(
			'###ID###' => $this->id,
			'###INDEX###' =>  $this->id . '_' . $key,
			'###CONTENT###' => $content
		));
		$this->html .= $html;
	}
	
	
	protected function readFlexformIntoConf($flexData, &$conf, $recursive=FALSE) {
		if ($recursive === FALSE) {
			$flexData = t3lib_div::xml2array($flexData, 'T3'); 
		} 

		if (is_array($flexData)) {
			if (isset($flexData['data']['sDEF']['lDEF'])) {
				$flexData = $flexData['data']['sDEF']['lDEF'];
			}
			
			foreach ($flexData as $key => $value) {
				if (is_array($value['el']) && count($value['el']) > 0) {
					foreach ($value['el'] as $ekey => $element) {
						if (isset($element['vDEF'])) {
							$conf[$ekey] =  $element['vDEF'];
						} else {
							if(is_array($element)) {
								$this->readFlexformIntoConf($element, $conf[$key . '.'][key($element)][$ekey . '.'], TRUE);							
							} else {
								$this->readFlexformIntoConf($element, $conf[$key . '.'][$ekey . '.'], TRUE);
							}
						}
					}
				} else {
					$this->readFlexformIntoConf($value['el'], $conf[$key . '.'], TRUE);
				}
				if ($value['vDEF']) {
					$conf[$key] = $value['vDEF'];
				}
			}
		}
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3webwidgets/pi1/class.tx_t3webwidgets_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3webwidgets/pi1/class.tx_t3webwidgets_pi1.php']);
}

?>