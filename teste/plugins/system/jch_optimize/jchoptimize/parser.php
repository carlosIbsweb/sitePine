<?php

/**
 * JCH Optimize - Aggregate and minify external resources for optmized downloads
 * 
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
use JchOptimize\Optimize;

defined('_JCH_EXEC') or die('Restricted access');

/**
 * Class to parse HTML and find css and js links to replace, populating an array with matches
 * and removing found links from HTML
 * 
 */
class JchOptimizeParser extends JchOptimizeBase
{

        /** @var string   Html of page */
        public $sHtml = '';

        /** @var array    Array of css or js urls taken from head */
        protected $aLinks = array();
        protected $aUrls  = array();
        protected $oFileRetriever;
	protected $sRegexMarker = 'JCHREGEXMARKER';
        public $params    = null;
        public $sLnEnd    = '';
        public $sTab      = '';
        public $sFileHash = '';
	public $bAmpPage  = false;
	public $iIndex_js    = 0;
	public $iIndex_css   = 0;
	public $bExclude_js  = false;
	public $bExclude_css = false;

	public $sAttributeRegex = '[^\s/"\'=<>]*+(?:\s*=(?:\s*+"[^"]*+"|\s*+\'[^\']*+\'|[^\s>]*+[\s>]))?';
	public $sAttributeValueRegex = '(?:(?<=")[^"]*+|(?<=\')[^\']*+|(?<==)[^\s*+>]*+)'; 

        /**
         * Constructor
         * 
         * @param JRegistry object $params      Plugin parameters
         * @param string  $sHtml                Page HMTL
         */
        public function __construct($oParams, $sHtml, $oFileRetriever)
        {
                $this->params = $oParams;
                $this->sHtml  = $sHtml;

                $this->oFileRetriever = $oFileRetriever;

                $this->sLnEnd = JchPlatformUtility::lnEnd();
                $this->sTab   = JchPlatformUtility::tab();

                if (!defined('JCH_TEST_MODE'))
                {
                        $oUri            = JchPlatformUri::getInstance();
                        $this->sFileHash = serialize($this->params->getOptions()) . JCH_VERSION . $oUri->toString(array('scheme', 'host'));
                }

		$this->bAmpPage = (bool) preg_match('#<html [^>]*?(?:&\#26A1;|amp)(?: |>)#', $sHtml);

                $this->parseHtml();
        }

        /**
         * 
         * @return type
         */
        public function getOriginalHtml()
        {
                return $this->sHtml;
        }

        /**
         * 
         * @return type
         */
        public function cleanHtml()
        {
                $hash = preg_replace(array(
                        $this->getHeadRegex(true),
                        '#' . $this->ifRegex() . '#',
                        '#' . implode('', $this->getJsRegex()) . '#ix',
                        '#' . implode('', $this->getCssRegex()) . '#six'
                        ), '', $this->sHtml);


                return $hash;
        }

        /**
         * 
         */
        public function getHtmlHash()
        {
                $sHtmlHash = '';

                preg_replace_callback('#<(?!/)[^>]++>#i',
                                      function($aM) use (&$sHtmlHash)
                {
                        $sHtmlHash .= $aM[0];

                        return;
                }, $this->cleanHtml(), 200);


                return $sHtmlHash;
        }

        /**
         * Removes applicable js and css links from search area
         * 
         */
        public function parseHtml()
        {
                JCH_DEBUG ? JchPlatformProfiler::start('SetUpExcludes') : null;

                $oParams = $this->params;

                $aCBArgs = array();

                if (!JchOptimizeHelper::isMsieLT10() && $oParams->get('combine_files_enable', '1') && !$this->bAmpPage)
                {
                        loadJchOptimizeClass('JchPlatformExcludes');
			
			//These parameters will be excluded while preserving execution order
                        $aExJsComp  = $this->getExComp($oParams->get('excludeJsComponents_peo', ''));
                        $aExCssComp = $this->getExComp($oParams->get('excludeCssComponents', ''));

                        $aExcludeJs     = JchOptimizeHelper::getArray($oParams->get('excludeJs_peo', ''));
                        $aExcludeCss    = JchOptimizeHelper::getArray($oParams->get('excludeCss', ''));
                        $aExcludeScript = JchOptimizeHelper::getArray($oParams->get('pro_excludeScripts_peo'));
                        $aExcludeStyle  = JchOptimizeHelper::getArray($oParams->get('pro_excludeStyles'));

                        $aExcludeScript = array_map(function($sScript)
                        {
                                return stripslashes($sScript);
                        }, $aExcludeScript);

                        $aCBArgs['excludes']['js']         = array_merge($aExcludeJs, $aExJsComp,
                                                                         array('.com/maps/api/js', '.com/jsapi', '.com/uds', 'typekit.net','cdn.ampproject.org', 'googleadservices.com/pagead/conversion.js'),



                                                                         JchPlatformExcludes::head('js'));
                        $aCBArgs['excludes']['css']        = array_merge($aExcludeCss, $aExCssComp, JchPlatformExcludes::head('css'));
                        $aCBArgs['excludes']['js_script']  = $aExcludeScript;
                        $aCBArgs['excludes']['css_script'] = $aExcludeStyle;

                        $aCBArgs['removals']['js']  = JchOptimizeHelper::getArray($oParams->get('removeJs', ''));
                        $aCBArgs['removals']['css'] = JchOptimizeHelper::getArray($oParams->get('removeCss', ''));

			//These parameters will be excluded without preserving execution order
                        $aExJsComp_ieo  = $this->getExComp($oParams->get('excludeJsComponents', ''));
                        $aExcludeJs_ieo     = JchOptimizeHelper::getArray($oParams->get('excludeJs', ''));
                        $aExcludeScript_ieo = JchOptimizeHelper::getArray($oParams->get('pro_excludeScripts'));

			$aCBArgs['excludes_ieo']['js'] = array_merge($aExcludeJs_ieo, $aExJsComp_ieo);
			$aCBArgs['excludes_ieo']['js_script'] = $aExcludeScript_ieo;

                        JCH_DEBUG ? JchPlatformProfiler::stop('SetUpExcludes', TRUE) : null;

                        $this->initSearch($aCBArgs);
                }

                $this->getImagesWithoutAttributes();
        }

        /**
         * 
         * @param type $sType
         */
        protected function initSearch($aCBArgs)
        {

                JCH_DEBUG ? JchPlatformProfiler::start('InitSearch') : null;

                $aJsRegex = $this->getJsRegex();
                $j        = implode('', $aJsRegex);

                $aCssRegex = $this->getCssRegex();
                $c         = implode('', $aCssRegex);

                $i  = $this->ifRegex();
                $ns = '<noscript\b[^>]*+>(?><?[^<]*+)*?</noscript\s*+>';

                $sRegex = "#(?>(?:<(?!!))?[^<]*+(?:$i|$ns|<!)?)*?\K(?:$j|$c|\K$)#six";


                JCH_DEBUG ? JchPlatformProfiler::stop('InitSearch', TRUE) : null;

		$this->searchArea($sRegex, 'head', $aCBArgs);
                ##<procode>##

		if ($this->params->get('pro_bottom_js', '0') == 1)
                {
                        $aCBArgs['excludes']['js_script'] = array_merge($aCBArgs['excludes']['js_script'], array('.write(', 'var google_conversion'),
                                                                        JchPlatformExcludes::body('js', 'script'));
                        $aCBArgs['excludes']['js']        = array_merge($aCBArgs['excludes']['js'], array('.com/recaptcha/api'),
                                                                        JchPlatformExcludes::body('js'));

                        $this->searchArea($sRegex, 'body', $aCBArgs);
                }

                ##</procode>##
        }

        /**
         * 
         * @param type $sRegex
         * @param type $sType
         * @param type $sSection
         * @param type $aCBArgs
         * @throws Exception
         */
        protected function searchArea($sRegex, $sSection, $aCBArgs)
        {
                JCH_DEBUG ? JchPlatformProfiler::start('SearchArea - ' . $sSection) : null;

                $obj = $this;

                $sProcessedHtml = preg_replace_callback($sRegex,
                                                        function($aMatches) use ($obj, $aCBArgs)
                {
                        return $obj->replaceScripts($aMatches, $aCBArgs);
                }, $this->{'get' . ucfirst($sSection) . 'Html'}());

                if (is_null($sProcessedHtml))
                {
                        throw new Exception(sprintf('Error while parsing for links in %1$s', $sSection));
                }

                $this->{'set' . ucfirst($sSection) . 'Html'}($sProcessedHtml);

                JCH_DEBUG ? JchPlatformProfiler::stop('SearchArea - ' . $sSection, TRUE) : null;
        }

        /**
         * 
         */
        protected function getImagesWithoutAttributes()
        {
                if ($this->params->get('img_attributes_enable', '0'))
                {
                        JCH_DEBUG ? JchPlatformProfiler::start('GetImagesWithoutAttributes') : null;

                        $rx = '#(?><?[^<]*+)*?\K(?:<img\s++(?!(?=(?>[^\s>]*+\s++)*?width\s*+=\s*+["\'][^\'">a-z]++[\'"])'
                                . '(?=(?>[^\s>]*+\s++)*?height\s*+=\s*+["\'][^\'">a-z]++[\'"]))'
                                . '(?=(?>[^\s>]*+\s++)*?src\s*+=(?:\s*+"([^">]*+)"|\s*+\'([^\'>]*+)\'|([^\s>]++)))[^>]*+>|$)#i';

			//find all images without width and height attributes and populate the $m array
                        preg_match_all($rx, $this->getBodyHtml(), $m, PREG_PATTERN_ORDER);

                        $this->aLinks['img'] = array_map(function($a)
                        {
                                return array_slice($a, 0, -1);
                        }, $m);

                        JCH_DEBUG ? JchPlatformProfiler::stop('GetImagesWithoutAttributes', true) : null;
                }
        }

        /**
         * Callback function used to remove urls of css and js files in head tags
         *
         * @param array $aMatches       Array of all matches
         * @return string               Returns the url if excluded, empty string otherwise
         */
        public function replaceScripts($aMatches, $aCBArgs)
        {
                $sUrl = $aMatches['url'] = trim(!empty($aMatches[1]) ? $aMatches[1] : (!empty($aMatches[3]) ? $aMatches[3] : ''));
		$sDeclaration = $aMatches['content'] = !empty($aMatches[2]) ? $aMatches[2] : (!empty($aMatches[4]) ? $aMatches[4] : '');

                if (preg_match('#^<!--#', $aMatches[0])
                        || (JchOptimizeUrl::isInvalid($sUrl) && trim($sDeclaration) == ''))
                {
                        return $aMatches[0];
                }

                $sType = preg_match('#^<script#i', $aMatches[0]) ? 'js' : 'css';

                if ($sType == 'js' && !$this->params->get('javascript', '1'))
                {
                        return $aMatches[0];
                }

                if ($sType == 'css' && !$this->params->get('css', '1'))
                {
                        return $aMatches[0];
                }


                $aExcludes = array();

                if (isset($aCBArgs['excludes']))
                {
                        $aExcludes = $aCBArgs['excludes'];
                }

		if (isset($aCBArgs['excludes_ieo']))
		{
			$aExcludes_ieo = $aCBArgs['excludes_ieo'];
		}

                $aRemovals = array();

               // if (isset($aCBArgs['removals']))
               // {
               //         $aRemovals = $aCBArgs['removals'];
               // }

                $sMedia = '';

                if (($sType == 'css') && (preg_match('#media=(?(?=["\'])(?:["\']([^"\']+))|(\w+))#i', $aMatches[0], $aMediaTypes) > 0))
                {
                        $sMedia .= $aMediaTypes[1] ? $aMediaTypes[1] : $aMediaTypes[2];
                }

                switch (true)
                {
			case (($sUrl != '') && $this->isDuplicated($sUrl)):
                        //case (($sUrl != '') && !empty($aRemovals[$sType]) && JchOptimizeHelper::findExcludes($aRemovals[$sType], $sUrl)):

                                return '';

			//These cases are being excluded without preserving execution order
                        case ($sUrl != '' && !JchOptimizeUrl::isHttpScheme($sUrl)):
			case (!empty($sUrl) && !empty($aExcludes_ieo['js']) && JchOptimizeHelper::findExcludes($aExcludes_ieo['js'], $sUrl)):
                        case ($sDeclaration != '' && JchOptimizeHelper::findExcludes($aExcludes_ieo['js_script'], $sDeclaration, 'js')):

				return $aMatches[0];

			//These cases are being excluded while preserving execution order
                        case (($sUrl != '') && !$this->isHttpAdapterAvailable($sUrl)):
                        case ($sUrl != '' && JchOptimizeUrl::isSSL($sUrl) && !extension_loaded('openssl')):
                        case (($sUrl != '') && !empty($aExcludes[$sType]) && JchOptimizeHelper::findExcludes($aExcludes[$sType], $sUrl)):
                        case ($sDeclaration != '' && $this->excludeDeclaration($sType)):
                        case ($sDeclaration != '' && JchOptimizeHelper::findExcludes($aExcludes[$sType . '_script'], $sDeclaration, $sType)):
                        case (($sUrl != '') && $this->excludeExternalExtensions($sUrl)):

				//We want to put the combined js files as low as possible, if files were removed before,
				//we place them just above the excluded files
				if($sType == 'js' && !$this->bExclude_js && !empty($this->aLinks['js']))
				{
					$aMatches[0] = '<JCH_JS' . $this->iIndex_js . '>' . $this->sLnEnd . 
						$this->sTab . $aMatches[0];
				}

                                $this->{'bExclude_' . $sType} = true;

                                return $aMatches[0];

                        default:
                                $return = '';

				//mark location of first css file
				if($sType == 'css' && empty($this->aLinks['css']) 
					&& !$this->params->get('pro_optimizeCssDelivery_enable', '0'))
				{
					$return = '<JCH_CSS' . $this->iIndex_css . '>';
				}

				//The last file was excluded while preserving execution order
                                if ($this->{'bExclude_' . $sType})
                                {
					//reset Exclude flag
                                        $this->{'bExclude_' . $sType} = false;

					//mark location of next removed css file
					if ($sType == 'css' && !empty($this->aLinks['css'])
						&& !$this->params->get('pro_optimizeCssDelivery_enable', '0'))
					{
						$return = '<JCH_CSS' . ++$this->iIndex_css . '>';
					}

					if ($sType == 'js' && !empty($this->aLinks['js']))
					{
						$this->iIndex_js++;
					}
                                }

                                $array = array();

                                $array['match'] = $aMatches[0];

                                if ($sUrl == '' && trim($sDeclaration) != '')
                                {
                                        $content = JchOptimize\HTML_Optimize::cleanScript($sDeclaration, $sType);

                                        $array['content'] = $content;
                                }
                                else
                                {
                                        $array['url'] = $sUrl;
                                }

                                if ($this->sFileHash != '')
                                {
                                        $array['id'] = $this->getFileID($aMatches);
                                }

                                if ($sType == 'css')
                                {
                                        $array['media'] = $sMedia;
                                }

                                $this->aLinks[$sType][$this->{'iIndex_' . $sType}][] = $array;

                                return $return;
                }
        }

        /**
         * 
         * @param type $sUrl
         * @return type
         */
        protected function getFileID($aMatches)
        {
                $id = '';

                $containsgf = JchOptimizeHelper::getArray($this->params->get('hidden_containsgf', ''));

                if (!empty($aMatches['url']))
                {
			$id .= $aMatches['url'];

                        if (strpos($aMatches['url'], 'fonts.googleapis.com') !== FALSE
                                || in_array($aMatches['url'], $containsgf))
                        {
                                $browser = JchOptimizeBrowser::getInstance();

                                $id .= $browser->getFontHash();
                        }
                }
		else
		{
			$id .= $aMatches['content'];
		}

                return md5($this->sFileHash . $id);
        }

        /**
         * 
         * @param type $sUrl
         */
        public function isDuplicated($sUrl)
        {
                $sUrl   = JchOptimizeUrl::AbsToProtocolRelative($sUrl);
                $return = in_array($sUrl, $this->aUrls);

                if (!$return)
                {
                        $this->aUrls[] = $sUrl;
                }

                return $return;
        }

        /**
         * 
         * @param type $sPath
         */
        protected function excludeExternalExtensions($sPath)
        {
                if (!$this->params->get('includeAllExtensions', '0'))
                {
                        return !JchOptimizeUrl::isInternal($sPath) || preg_match('#' . JchPlatformExcludes::extensions() . '#i', $sPath);
                }

                return FALSE;
        }

        /**
         * Generates regex for excluding components set in plugin params
         * 
         * @param string $param
         * @return string
         */
        protected function getExComp($sExComParam)
        {
                $aComponents = JchOptimizeHelper::getArray($sExComParam);
                $aExComp     = array();

                if (!empty($aComponents))
                {
                        $aExComp = array_map(function($sValue)
                        {
                                return $sValue . '/';
                        }, $aComponents);
                }

                return $aExComp;
        }

        /**
         * Fetches Class property containing array of matches of urls to be removed from HTML
         * 
         * @return array
         */
        public function getReplacedFiles()
        {
                return $this->aLinks;
        }

        /**
         * Determines if document is of html5 doctype
         * 
         * @return boolean
         */
        public function isHtml5()
        {
                return (bool) preg_match('#^<!DOCTYPE html>#i', trim($this->sHtml));
        }

        /**
         * 
         * @return string
         */
        protected static function ifRegex()
        {
                return '<!--(?>-?[^-]*+)*?-->';
        }

        /**
         * 
         * @param type $aAttrs
         * @param type $aExts
         * @param type $bFileOptional
         */
        protected static function urlRegex($aAttrs, $aExts)
        {
                $sAttrs = implode('|', $aAttrs);
                $sExts  = implode('|', $aExts);

                $sUrlRegex = <<<URLREGEX
                (?>  [^\s>]*+\s  )+?  (?>$sAttrs)\s*+=\s*+["']?
                ( (?<!["']) [^\s>]*+  | (?<!') [^"]*+ | [^']*+ )
                                                                        
URLREGEX;

                return $sUrlRegex;
        }

        /**
         * 
         * @param type $sCriteria
         * @return string
         */
        protected static function criteriaRegex($sCriteria)
        {
                $sCriteriaRegex = '(?= (?> [^\s>]*+[\s] ' . $sCriteria . ' )*+  [^\s>]*+> )';

                return $sCriteriaRegex;
        }

        /**
         * 
         */
        public function getJsRegex()
        {
                $aRegex = array();

		$a = $this->sAttributeRegex;
		$u = $this->sAttributeValueRegex;

		$aRegex[0] = "(?:<script\b(?!(?>\s*+$a)*?\s*+type\s*+=\s*+(?![\"']?(?:text|application)/javascript[\"' ]))";
		$aRegex[1] = "(?>\s*+(?!src)$a)*\s*+(?:src\s*+=\s*+[\"']?($u))?[^<>]*+>((?><?[^<]*+)*?)</\s*+script\s*+>)";

                return $aRegex;
        }

        /**
         * 
         * @return string
         */
        public function getCssRegex()
        {
                $aRegex = array();

		$a = $this->sAttributeRegex;
		$u = $this->sAttributeValueRegex;

		$aRegex[0] = "(?:<link\b(?!(?>\s*+$a)*?\s*+(?:itemprop|disabled|type\s*+=\s*+(?![\"']?text/css[\"' ])|rel\s*+=\s*+(?![\"']?stylesheet[\"' ])))";
		$aRegex[1] = "(?>\s*+$a)*?\s*+href\s*+=\s*+[\"']?($u)[^<>]*+>)";
                $aRegex[3] = "|(?:<style\b(?:(?!(?:type\s*+=\s*+(?![\"']?text/css[\"' ]))|(?:scoped|amp))[^>])*>((?><?[^<]+)*?)</\s*+style\s*+>)";

                return $aRegex;
        }

        /**
         * Get the search area to be used..head section or body
         * 
         * @param type $sHead   
         * @return type
         */
        public function getBodyHtml()
        {
		if (preg_match($this->getBodyRegex(), $this->sHtml, $aBodyMatches) === false || empty($aBodyMatches))
		{
			throw new Exception('Error occurred while trying to match for search area.'
			. ' Check your template for open and closing body tags');
		}

		return $aBodyMatches[0] . $this->sRegexMarker;
        }

	public function setBodyHtml($sHtml)
	{
		$sHtml = $this->cleanRegexMarker($sHtml);
		$this->sHtml = preg_replace($this->getBodyRegex(), JchOptimizeHelper::cleanReplacement($sHtml), $this->sHtml, 1);	
	}

	public function getFullHtml()
	{
		return $this->sHtml . $this->sRegexMarker;
	}

	public function setFullHtml($sHtml)
	{
		$this->sHtml = $this->cleanRegexMarker($sHtml);
	}

        ##<procode2>##

        /**
         * 
         * @return boolean
         */
        public function excludeDeclaration($sType)
        {
		return ($sType == 'css' && (!$this->params->get('pro_inlineStyle', '1') || $this->params->get('pro_excludeAllStyles', '0')))
                        || ($sType == 'js' && (!$this->params->get('pro_inlineScripts', '0') || $this->params->get('pro_excludeAllScripts', '0')));
        }

        ##</procode2>##
        ##<procode>##
        /**
         * Determines if file contents can be fetched using http protocol if required
         * 
         * @param string $sPath    Url of file
         * @return boolean        
         */

        public function isHttpAdapterAvailable($sUrl)
        {
                if ($this->params->get('pro_phpAndExternal', '1'))
                {
                        if (preg_match('#^(?:http|//)#i', $sUrl) && !JchOptimizeUrl::isInternal($sUrl)
                                || $this->isPHPFile($sUrl))
                        {
                                return $this->oFileRetriever->isHttpAdapterAvailable();
                        }
                        else
                        {
                                return true;
                        }
                }
                else
                {
                        return parent::isHttpAdapterAvailable($sUrl);
                }
        }

        /**
         * 
         */
        public function runCookieLessDomain()
        {
                if ($this->params->get('pro_cookielessdomain_enable', '0'))
                {
                        JCH_DEBUG ? JchPlatformProfiler::start('RunCookieLessDomain') : null;

                        $static_files_array = array('css', 'js', 'jpe?g', 'gif', 'png', 'ico', 'bmp', 'pdf', 'tiff?', 'docx?');
                        $sf  = implode('|', $static_files_array);
			$a = $this->sAttributeRegex;
			$u = $this->sAttributeValueRegex;

                        $uri  = clone JchPlatformUri::getInstance();
                        $port = $uri->toString(array('port'));

                        if (empty($port))
                        {
                                $port = ':80';
                        }

                        $host = '(?:www\.)?' . preg_quote(preg_replace('#^www\.#i', '', $uri->getHost())) . '(?:' . $port . ')?';

			if(preg_match("#<base[^>]*?\shref\s*+=\s*+[\"']\K$u#i", 
				$this->getHeadHtml(), $mm))
			{
				$oBaseDir = JchPlatformUri::getInstance($mm[0]);
				$dir = trim($oBaseDir->getPath(), '/');
			}
			else
			{
				$dir  = trim(JchPlatformUri::base(true), '/');
			}

                        $match = '(?!data:image|[\'"])'
                                . '(?=(\s*+(?:(?:https?:)?//' . $host . ')?)(?!http|//))'
				. '(?=(?<=")\1((?>\.?[^\.>"]*+)*?\.(?>' . $sf . '))["?]'
				. '|(?<=\')\1((?>\.?[^\.>\']*+)*?\.(?>' . $sf . '))[\'?]'
				. '|(?<=\()\1((?>\.?[^\.>)]*+)*?\.(?>' . $sf . '))[)?]'
				. '|(?<==)\1((?>\.?[^\.>\s]*+)*?\.(?>' . $sf . '))[\s?])'
				. '((?<=\()[^)]*+|' . $u . ')';

			$l = '(?:xlink:)?href|(?:data-)?src';
			$x = "(?:<(?:link|script|(?:amp-)?ima?ge?|a))(?>\s*+$a)*?\s*+" 
				. "(?:(?:(?:$l)\s*+=\s*+[\"']?|style[^>(]*+(?<=url)\(['\"]?))";
			$s = '<script\b(?:(?! src\*?=)[^>])*+>(?><?[^<]*+)*?</script\s*+>';

			$sRegex = '#(?:(?=[^<>]++>)(?>[\'"(\s]?[^\'"(\s>]*+)*?\s*+'
				. "(?:(?:$l)\s*+=\s*+[\"']?|(?<=url)\(['\"]?)|" 
				. "(?>[<(]?[^<(]*+(?:$s)?)*?)(?:(?:$x|(?<=url)\([\"']?)?\K$match|\K$)#iS";

                        $sProcessedFullHtml = preg_replace_callback($sRegex,
                                                                    function($m) use ($dir)
                        {
				$m_array = array_filter($m);
				array_pop($m_array);
				$sPath = array_pop($m_array);

				if(substr($sPath, 0, 1) != '/')
				{
					$sPath = '/'. $dir . '/' . $sPath;
				}

				return JchOptimizeHelper::cookieLessDomain($this->params, $sPath, $m[0]);

                        }, $this->getFullHtml());

                        if (is_null($sProcessedFullHtml))
                        {
                                JchOptimizeLogger::log('Cookie-less domain function failed', $this->params);

                                return;
                        }

			$this->setFullHtml($sProcessedFullHtml);

                        JCH_DEBUG ? JchPlatformProfiler::stop('RunCookieLessDomain', TRUE) : null;
                }
        }

        /**
         * 
         * @param type $m
         * @param type $cdn
         * @param type $dir
         * @return type
         */
        public function cdnCB($m, $dir)
        {
		$sPath = JchOptimizeUrl::isPathRelative($m[0]) ?  '/' . $dir . '/' . $m[0] : $m[0]; 

                return JchOptimizeHelper::cookieLessDomain($this->params, $sPath);
        }

        /**
         * 
         * @return type
         */
        public function lazyLoadImages()
        {
                if ($this->params->get('pro_lazyload', '0') && !$this->bAmpPage)
                {
                        JCH_DEBUG ? JchPlatformProfiler::start('LazyLoadImages') : null;

                        $css = '        <noscript>
                        <style type="text/css">
                                img[data-jchll=true]{
                                        display: none;
                                }                               
                        </style>                                
                </noscript>
        </head>';

                        $this->sHtml = preg_replace('#</head\s*+>#i', $css, $this->sHtml, 1);

                        $sLazyLoadBodyHtml = preg_replace(
                                $this->getLazyLoadRegex(),
                                '$1$5src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="$3$7" '
                                . 'data-jchll="true"$4$8<noscript>$1$5$2$6$4$8</noscript>'
                                , $this->getBodyHtml());

                        if (is_null($sLazyLoadBodyHtml))
                        {
                                JchOptimizeLogger::log('Lazy load images function failed', $this->params);

                                return;
                        }

			$this->setBodyHtml($sLazyLoadBodyHtml);

                        JCH_DEBUG ? JchPlatformProfiler::stop('LazyLoadImages', TRUE) : null;
                }
        }

        /**
         * 
         * @return string
         */
        public function getLazyLoadRegex($admin = FALSE)
        {
                $s      = '<script\b[^>]*+>(?><?[^<]*+)*?</script\s*+>';
                $n      = '<noscript\b[^>]*+>(?><?[^<]*+)*?</noscript\s*+>';
                $t      = '<textarea\b[^>]*+>(?><?[^<]*+)*?</textarea\s*+>';
                $sRegex = "(?><?[^<]*+(?:$s|$n|$t)?)*?"
                        . '\K(?:(<img(?!(?>\s*+[^\s>]*+)*?\s*+(?>data-(?:src|original)';

                $aExcludeClass = JchOptimizeHelper::getArray($this->params->get('pro_excludeLazyLoadClass', array()));

                if (!empty($aExcludeClass))
                {

                        $aExcludeClass = array_map(function($sValue)
                        {
                                return '\b' . preg_quote($sValue) . '\b';
                        }, $aExcludeClass);
                        $sExcludeClass = implode('|', $aExcludeClass);

                        $sRegex .= '|class\s*+=\s*+[\'"]?[^\'">]*?(?>' . $sExcludeClass . ')';
                }

                $sRegex .= '))';

                if ($admin)
                {
                        $sRegex .= '(?:(?=(?>\s*+[^\s>]*+)*?\s*+class\s*+=\s*+[\'"]?([^\'">]*+)))?';
                }

                $sRegex .= '(?>\s*+[^\s>]*+)*?\s*+)(src\s*+=\s*+(?![\'"]?[^\'"> ]*?(?:data:image';

                $aExcludesFiles   = JchOptimizeHelper::getArray($this->params->get('pro_excludeLazyLoad', array()));
                $aExcludesFolders = JchOptimizeHelper::getArray($this->params->get('pro_excludeLazyLoadFolders', array()));

                $aExcludes = array_merge($aExcludesFiles, $aExcludesFolders);

                if (!empty($aExcludes))
                {
                        $aExcludes = array_map(function($sValue)
                        {
                                return preg_quote($sValue);
                        }, $aExcludes);
                        $sExcludes = implode('|', $aExcludes);
                        $sRegex .= '|' . $sExcludes;
                }

                $sRegex .= '))[\'"]?((?(?<=[\'"])[^\'"]*+|[^\s>]*+))[\'"]?)([^>]*+>)|\K$)';

		//Skip first 200 elements before starting to modify images for lazy load to avoid problems 
		//with css render blocking
		$sFullRegex = '#(?>^(?><?[^<]*+)*?(?:(?><?(?:[a-z0-9]++)(?:[^>]*+)>(?><?[^<]*+)*?(?=<[a-z0-9])){200}|$)' .
			$sRegex . ')|(?:' . $sRegex . ')#i';

                return $sFullRegex;
        }

        ##</procode>##
}
