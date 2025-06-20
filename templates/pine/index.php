<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

$doc = JFactory::getDocument();
$app = JFactory::getApplication();

$helix_path = JPATH_PLUGINS . '/system/helixultimate/core/helixultimate.php';
if (file_exists($helix_path)) {
    require_once($helix_path);
    $theme = new helixUltimate;
} else {
    die('Install and activate <a target="_blank" href="https://www.joomshaper.com/helix">Helix Ultimate Framework</a>.');
}

//Coming Soon
if ($this->params->get('comingsoon'))
{
  header("Location: " . $this->baseUrl . "?tmpl=comingsoon");
}

$custom_style = $this->params->get('custom_style');
$preset = $this->params->get('preset');

if($custom_style || !$preset)
{
    $scssVars = array(
        'preset' => 'default',
        'text_color' => $this->params->get('text_color'),
        'bg_color' => $this->params->get('bg_color'),
        'link_color' => $this->params->get('link_color'),
        'link_hover_color' => $this->params->get('link_hover_color'),
        'header_bg_color' => $this->params->get('header_bg_color'),
        'logo_text_color' => $this->params->get('logo_text_color'),
        'menu_text_color' => $this->params->get('menu_text_color'),
        'menu_text_hover_color' => $this->params->get('menu_text_hover_color'),
        'menu_text_active_color' => $this->params->get('menu_text_active_color'),
        'menu_dropdown_bg_color' => $this->params->get('menu_dropdown_bg_color'),
        'menu_dropdown_text_color' => $this->params->get('menu_dropdown_text_color'),
        'menu_dropdown_text_hover_color' => $this->params->get('menu_dropdown_text_hover_color'),
        'menu_dropdown_text_active_color' => $this->params->get('menu_dropdown_text_active_color'),
        'footer_bg_color' => $this->params->get('footer_bg_color'),
        'footer_text_color' => $this->params->get('footer_text_color'),
        'footer_link_color' => $this->params->get('footer_link_color'),
        'footer_link_hover_color' => $this->params->get('footer_link_hover_color'),
        'topbar_bg_color' => $this->params->get('topbar_bg_color'),
        'topbar_text_color' => $this->params->get('topbar_text_color')
    );
}
else
{
    $scssVars = (array) json_decode($this->params->get('preset'));
}

$scssVars['header_height'] = $this->params->get('header_height', '60px');
$scssVars['offcanvas_width'] = $this->params->get('offcanvas_width', '300') . 'px';


//Body Background Image
if ($bg_image = $this->params->get('body_bg_image'))
{
    $body_style = 'background-image: url(' . JURI::base(true) . '/' . $bg_image . ');';
    $body_style .= 'background-repeat: ' . $this->params->get('body_bg_repeat') . ';';
    $body_style .= 'background-size: ' . $this->params->get('body_bg_size') . ';';
    $body_style .= 'background-attachment: ' . $this->params->get('body_bg_attachment') . ';';
    $body_style .= 'background-position: ' . $this->params->get('body_bg_position') . ';';
    $body_style = 'body.site {' . $body_style . '}';
    $doc->addStyledeclaration($body_style);
}

//Custom CSS
if ($custom_css = $this->params->get('custom_css'))
{
    $doc->addStyledeclaration($custom_css);
}

$progress_bar_position = $this->params->get('reading_timeline_position');

if( $app->input->get('view') == 'article' && $this->params->get('reading_time_progress', 0) ) {
    
    $progress_style = 'position:fixed;';
    $progress_style .= 'z-index:9999;';
    $progress_style .= 'height:'.$this->params->get('reading_timeline_height').';';
    $progress_style .= 'background-color:'.$this->params->get('reading_timeline_bg').';';
    $progress_style .= $progress_bar_position == 'top' ? 'top:0;' : 'bottom:0;';
    $progress_style = '.sp-reading-progress-bar { '.$progress_style.' }';
    $doc->addStyledeclaration($progress_style);
}

//Custom JS
if ($custom_js = $this->params->get('custom_js'))
{
    $doc->addScriptdeclaration($custom_js);
}

//if(isset($_GET['tt'])){
    $doc->addStylesheet(Juri::base(true).'/templates/'.$this->template.'/'.'css/pinet.css?'.uniqid());
//}else{

  //  $doc->addStylesheet(Juri::base(true).'/templates/'.$this->template.'/'.'css/pine.css?'.uniqid());
//}

?>
<link href="https://fonts.googleapis.com/css2?family=Rampart+One&display=swap" rel="stylesheet">
<!doctype html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="canonical" href="<?php echo JUri::getInstance()->toString(); ?>">
        <?php

        $theme->head();
        
        $theme->add_css('font-awesome.min.css');
        $theme->add_js('jquery.sticky.js, main.js');

        $theme->add_scss('master', $scssVars, 'template');

        if($this->direction == 'rtl')
        {
            $theme->add_scss('rtl', $scssVars, 'rtl');
        }

        $theme->add_scss('presets', $scssVars, 'presets/' . $scssVars['preset']);
        $theme->add_css('custom');

        //Before Head
        if ($before_head = $this->params->get('before_head'))
        {
            echo $before_head . "\n";
        }


        ?>

        <script>
        jQuery(function($){
            $('#offcanvas-toggler').prepend('<span>menu </span>');

            var p = $('.pineClink');

            p.each(function(){

            pl = $(this).attr('class').split(' ');
            inPl = pl.indexOf('pineClink');

            mUrl = pl[inPl+1];
            mUrl = mUrl.split(':t');

            mLink = mUrl[0];
            mTarget = mUrl[1] ? 'target="_'+mUrl[1]+'"' : '';


            delete pl[inPl];
            delete pl[inPl+1];

            classReturn = pl.join(' ');

            $(this).attr('class',classReturn)

            $(this).html('<a href="'+mLink+'" ' +mTarget+ ' class="pineLink">'+$(this).html()+'</a>');

            })
           
        })
        </script>

        <style type="text/css">
            .floating-button {
              background-color: #28a745;
              color: #ffffff;
              padding: 15px 25px;
              text-decoration: none;
              cursor: pointer;
              border-radius: 50%;
              box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
              transition: transform 0.2s;
              position: fixed;
              bottom: 20px;
              right: 20px;
              animation: floating-animation 2s ease-in-out infinite;
              z-index: 99
            }

            .floating-button:hover {
              transform: translateY(-5px);
              box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            }

            @keyframes floating-animation {
              0% {
                transform: translateY(0);
              }
              50% {
                transform: translateY(-10px);
              }
              100% {
                transform: translateY(0);
              }
            }
        </style>
    </head>
    <body class="<?php echo $theme->bodyClass(); ?>">
    <?php if($this->params->get('preloader')) : ?>
        <div class="sp-preloader"><div></div></div>
    <?php endif; ?>

    <div class="body-wrapper">
        <div class="body-innerwrapper">
            <?php echo $theme->getHeaderStyle(); ?>
            <?php $theme->render_layout(); ?>
        </div>
    </div>

    <!-- Off Canvas Menu -->
    <div class="offcanvas-overlay"></div>
    <div class="offcanvas-menu">
        <a href="#" class="close-offcanvas"><span class="fa fa-remove"></span></a>
        <div class="offcanvas-inner">
            <?php if ($this->countModules('offcanvas')) : ?>
                <jdoc:include type="modules" name="offcanvas" style="sp_xhtml" />
            <?php else: ?>
                <p class="alert alert-warning">
                    <?php echo JText::_('HELIX_ULTIMATE_NO_MODULE_OFFCANVAS'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <?php $theme->after_body(); ?>

    <jdoc:include type="modules" name="debug" style="none" />
    
    <!-- Go to top -->
    <?php if ($this->params->get('goto_top', 0)) : ?>
        <a href="#" class="sp-scroll-up" aria-label="Scroll Up"><span class="fa fa-chevron-up" aria-hidden="true"></span></a>
    <?php endif; ?>
    <?php if( $app->input->get('view') == 'article' && $this->params->get('reading_time_progress', 0) ): ?>
        <div data-position="<?php echo $progress_bar_position; ?>" class="sp-reading-progress-bar"></div>
    <?php endif; ?>

    <?php if ($this->countModules('floating')) : ?>
        <a href="https://wa.me/556199093099?text=Ol%C3%A1%2C%20gostaria%20de%20saber%20mais%20sobre%20a%20col%C3%B4nia." target="_blank" class="floating-button">
          Para mais informações, entre em contato conosco!
        </a>
    <?php endif;?>
   

    </body>
</html>