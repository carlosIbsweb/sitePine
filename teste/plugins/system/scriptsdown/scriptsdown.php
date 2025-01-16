<?php

/**
 * @package plugin ScriptsDown
 * @copyright (C) 2010-2012 RicheyWeb - www.richeyweb.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * ScriptsDown system plugin
 */
class plgSystemScriptsDown extends JPlugin {

	private $_scripts = array();
	private $_script = '';
	private $_mediaVersion;

	function onBeforeCompileHead() {
		if (JFactory::getApplication()->isAdmin())
		{
			return;
		}
		$doc = JFactory::getDocument();
		$this->_removeScripts($doc);
		if ($this->params->get('declarations', false))
		{
			$this->_script = $doc->_script['text/javascript'];
			unset($doc->_script['text/javascript']);
		}
	}

	function onAfterRender() {
		if (JFactory::getApplication()->isAdmin())
		{
			return;
		}
		$doc = JFactory::getDocument();
		if (count($this->_scripts))
		{
			$this->_mediaVersion = '?' . $doc->getMediaVersion();
			$this->_moveScripts();
			if ($this->params->get('declarations', false))
			{
				$this->_moveScript();
			}
		}
		if ($this->params->get('comments', false))
		{
			$this->_removeComments();
		}
	}

	private function _removeScripts($doc) {
		$regex = $this->_prepExclude((array) $this->params->get('scripts', array()));
		$matched = array();
		$regexinclude = $this->params->get('include', false);
		foreach ($regex as $r)
		{
			$match = preg_grep('/' . $r . '/', array_keys($doc->_scripts));
			$matched = array_merge($matched, $match);
		}
		foreach ($doc->_scripts as $src => $attribs)
		{
			if (!$regexinclude)
			{
				if (!in_array($src, $matched))
				{
					$this->_scripts[$src] = $attribs;
					unset($doc->_scripts[$src]);
				}
				continue;
			}

			if (in_array($src, $matched))
			{
				$this->_scripts[$src] = $attribs;
				unset($doc->_scripts[$src]);
			}
		}
	}

	private function _moveScripts() {
		$app = JFactory::getApplication();
		$body = $app->getBody();
		foreach ($this->_scripts as $src => $attribs)
		{
			$body = str_replace('</body>', $this->_renderScript($src, $attribs) . "</body>", $body);
		}
		$app->setBody($body);
	}

	private function _moveScript() {
		$app = JFactory::getApplication();
		$body = str_replace('</body>', $this->_renderDeclaration($this->_script) . "</body>", $app->getBody());
		$app->setBody($body);
	}

	private function _removeComments() {
		$app = JFactory::getApplication();
		$body = $app->getBody();
		$regex = array(
			'/\s{0,}<!--(?!\s{0,}\[if)(.*?)-->/'
		);
		foreach ($regex as $r)
		{
			$body = preg_replace($r, '', $body);
		}
		$app->setBody($body);
	}

	private function _renderDeclaration($script) {
		$tag = $this->_renderScript(false, array('type' => 'text/javascript'));
		return str_replace('</script>', "\n" . $script . "\n</script>", $tag);
	}

	private function _renderScript($src = false, $attribs) {
		$defaultJsMimes = array('text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript');
		$doc = JFactory::getDocument();
		$mediaVersion = (isset($attribs['options']['version']) && $attribs['options']['version'] && strpos($src, '?') === false && ($this->_mediaVersion || $attribs['options']['version'] !== 'auto')) ? $this->_mediaVersion : '';
		$dom = new DOMDocument('1.0', 'UTF-8');
		$script = $dom->createElement('script');
		// src attribute
		if ($src)
		{
			$this->_addAttribute($dom, $script, 'src', $src . $mediaVersion);
		}
		// type attribute
		if (array_intersect(array_keys($attribs), array('type', 'mime')) && !$doc->isHtml5() && in_array((isset($attribs['type']) ? $attribs['type'] : $attribs['mime']), $defaultJsMimes))
		{
			$this->_addAttribute($dom, $script, 'type', isset($attribs['type'])?$attribs['type']:$attribs['mime']);
		}
		// defer attribute
		if (isset($attribs['defer']) && $attribs['defer'] === true)
		{
			$this->_addAttribute($dom, $script, 'defer');
		}
		// async attribute
		if (isset($attribs['async']) && $attribs['async'] === true)
		{
			$this->_addAttribute($dom, $script, 'asnyc');
		}
		// charset attribute
		if (isset($attribs['charset']))
		{
			$this->_addAttribute($dom, $script, 'charset', $attribs['charset']);
		}

		$dom->appendChild($script);

		if (isset($attribs['options']) && isset($attribs['options']['conditional']))
		{
			$tag = $dom->saveHTML();
			return implode("\n", array('<!--[if ' . $attribs['options']['conditional'] . ']>', $tag . '<![endif]-->', ''));
		}
		return $dom->saveHTML();
	}

	private function _addAttribute($dom, &$element, $name, $value = false) {
		$attr = $dom->createAttribute($name);
		if ($value)
		{
			$attr->value = $value;
		}
		$element->appendChild($attr);
	}

	private function _prepExclude($a) {
		return array_values(array_map(function($i)
			{
				return $i->regex;
			}, $a));
	}

}
