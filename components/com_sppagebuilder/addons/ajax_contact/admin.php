<?php
/**
* @package SP Page Builder
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2016 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('restricted aceess');

SpAddonsConfig::addonConfig(
array(
	'type'=>'content',
	'addon_name'=>'sp_ajax_contact',
	'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT'),
	'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_DESC'),
	'category'=>'Content',
	'attr'=>array(
		'general' => array(
			'admin_label'=>array(
				'type'=>'text',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL_DESC'),
				'std'=> ''
			),

			'title'=>array(
				'type'=>'text',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_DESC'),
				'std'=>  ''
			),

			'heading_selector'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_DESC'),
				'values'=>array(
					'h1'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H1'),
					'h2'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H2'),
					'h3'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H3'),
					'h4'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H4'),
					'h5'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H5'),
					'h6'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H6'),
				),
				'std'=>'h3',
				'depends'=>array(array('title', '!=', '')),
			),

			'title_font_family'=>array(
				'type'=>'fonts',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_FONT_FAMILY'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_FONT_FAMILY_DESC'),
				'depends'=>array(array('title', '!=', '')),
				'selector'=> array(
					'type'=>'font',
					'font'=>'{{ VALUE }}',
					'css'=>'.sppb-addon-title { font-family: {{ VALUE }}; }'
				)
			),

			'title_fontsize'=>array(
				'type'=>'slider',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_FONT_SIZE'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_FONT_SIZE_DESC'),
				'std'=>'',
				'depends'=>array(array('title', '!=', '')),
				'responsive' => true,
				'max'=> 400,
			),

			'title_lineheight'=>array(
				'type'=>'slider',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_LINE_HEIGHT'),
				'std'=>'',
				'depends'=>array(array('title', '!=', '')),
				'responsive' => true,
				'max'=> 400,
			),

			'title_font_style'=>array(
				'type'=>'fontstyle',
				'title'=> JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_FONT_STYLE'),
				'depends'=>array(array('title', '!=', '')),
			),

			'title_letterspace'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_LETTER_SPACING'),
				'values'=>array(
					'0'=> 'Default',
					'1px'=> '1px',
					'2px'=> '2px',
					'3px'=> '3px',
					'4px'=> '4px',
					'5px'=> '5px',
					'6px'=>	'6px',
					'7px'=>	'7px',
					'8px'=>	'8px',
					'9px'=>	'9px',
					'10px'=> '10px'
				),
				'std'=>'0',
				'depends'=>array(array('title', '!=', '')),
			),

			'title_text_color'=>array(
				'type'=>'color',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_TEXT_COLOR'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_TEXT_COLOR_DESC'),
				'depends'=>array(array('title', '!=', '')),
			),

			'title_margin_top'=>array(
				'type'=>'slider',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_MARGIN_TOP'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_MARGIN_TOP_DESC'),
				'placeholder'=>'10',
				'depends'=>array(array('title', '!=', '')),
				'responsive' => true,
				'max'=> 400,
			),

			'title_margin_bottom'=>array(
				'type'=>'slider',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_MARGIN_BOTTOM'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_MARGIN_BOTTOM_DESC'),
				'placeholder'=>'10',
				'depends'=>array(array('title', '!=', '')),
				'responsive' => true,
				'max'=> 400,
			),

			'separator_options'=>array(
				'type'=>'separator',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_ADDON_OPTIONS')
			),

			'recipient_email'=>array(
				'type'=>'text',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_RECIPIENT_EMAIL'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_RECIPIENT_EMAIL_DESC'),
				'std'=>'email@yourdomain.com'
			),

			'from_name'=>array(
				'type'=>'text',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_FORM_NAME'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_FORM_NAME_DESC'),
				'std'=>''
			),

			'from_email'=>array(
				'type'=>'text',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_FORM_EMAIL'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_FORM_EMAIL_DESC'),
				'std'=>''
			),

			'formcaptcha'=>array(
				'type'=>'checkbox',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_SHOW_CAPTCHA'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_SHOW_CAPTCHA_DESC'),
				'values'=> array(
					'0'=>'No',
					'1'=>'Yes'
				),
				'std'=>'1'
			),

			'captcha_type'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_CAPTCHA_TYPE'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_CAPTCHA_TYPE_DESC'),
				'values'=>array(
					'default'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_CAPTCHA_TYPE_DEFAULT'),
					'gcaptcha'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_CAPTCHA_TYPE_GCHAPTCHA'),
				),
				'std'=>'default',
				'depends'=>array('formcaptcha'=>'1'),
			),

			'captcha_question'=>array(
				'type'=>'text',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_CAPTCHA_QUESTION'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_CAPTCHA_QUESTION_DESC'),
				'std'=>'3 + 4 = ?',
				'depends'=> array(
					array('formcaptcha', '=', '1'),
					array('captcha_type', '=', 'default'),
				),
			),

			'captcha_input_col'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_CAPTCHA_INPUT_COL_SIZE'),
				'values'=>array(
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'12' => '12',
				),
				'std'=>'12',
				'depends'=> array(
					array('formcaptcha', '=', '1'),
					array('captcha_type', '=', 'default'),
				),
			),

			'captcha_answer'=>array(
				'type'=>'text',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_CAPTCHA_ANSWER'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_CAPTCHA_ANSWER_DESC'),
				'std'=>'7',
				'depends'=> array(
					array('formcaptcha', '=', '1'),
					array('captcha_type', '=', 'default'),
				),
			),

			'name_input_col'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_NAME_INPUT_COL_SIZE'),
				'values'=>array(
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'12' => '12',
				),
				'std'=>'12'
			),

			'email_input_col'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_EMAIL_INPUT_COL_SIZE'),
				'values'=>array(
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'12' => '12',
				),
				'std'=>'12'
			),

			'subject_input_col'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_SUBJECT_INPUT_COL_SIZE'),
				'values'=>array(
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'12' => '12',
				),
				'std'=>'12'
			),

			'message_input_col'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_MESSAGE_INPUT_COL_SIZE'),
				'values'=>array(
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'12' => '12',
				),
				'std'=>'12'
			),

			'show_label'=>array(
				'type'=>'checkbox',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_SHOW_LABEL'),
				'std'=>0,
			),

			'show_checkbox'=>array(
				'type'=>'checkbox',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_SHOW_CHECKBOX'),
				'std'=>0,
			),
			'checkbox_title'=>array(
				'type'=>'textarea',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_CHECKBOX_TITLE'),
				'std'=>'I am agree with your <a href="#">terms & conditions</a>.',
				'depends'=>array('show_checkbox'=>1)
			),

			// Button
			'use_custom_button'=>array(
				'type'=>'checkbox',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_AJAX_CONTACT_USE_BUTTON'),
				'std'=>0,
			),

			'button_text'=>array(
				'type'=>'text',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_TEXT'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_TEXT_DESC'),
				'std'=>'Send Message',
				'depends'=>array('use_custom_button'=>1)
			),

			'button_font_family'=>array(
				'type'=>'fonts',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_GLOBAL_FONT_FAMILY'),
				'selector'=> array(
					'type'=>'font',
					'font'=>'{{ VALUE }}',
					'css'=>'.sppb-btn { font-family: {{ VALUE }}; }'
				),
				'depends'=>array('use_custom_button'=>1)
			),

			'button_font_style'=>array(
				'type'=>'fontstyle',
				'title'=> JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_FONT_STYLE'),
				'depends'=>array('use_custom_button'=>1)
			),

			'button_letterspace'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_LETTER_SPACING'),
				'values'=>array(
					'0'=> 'Default',
					'1px'=> '1px',
					'2px'=> '2px',
					'3px'=> '3px',
					'4px'=> '4px',
					'5px'=> '5px',
					'6px'=>	'6px',
					'7px'=>	'7px',
					'8px'=>	'8px',
					'9px'=>	'9px',
					'10px'=> '10px'
				),
				'std'=>'0',
				'depends'=>array('use_custom_button'=>1)
			),

			'button_type'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_STYLE'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_STYLE_DESC'),
				'values'=>array(
					'default'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_DEFAULT'),
					'primary'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_PRIMARY'),
					'secondary'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_SECONDARY'),
					'success'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_SUCCESS'),
					'info'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_INFO'),
					'warning'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_WARNING'),
					'danger'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_DANGER'),
					'dark'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_DARK'),
					'link'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_LINK'),
					'custom'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_CUSTOM'),
				),
				'std'=>'success',
				'depends'=>array('use_custom_button'=>1)
			),

			'button_appearance'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE_DESC'),
				'values'=>array(
					''=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE_FLAT'),
					'gradient'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE_GRADIENT'),
					'outline'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE_OUTLINE'),
					'3d'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_APPEARANCE_3D'),
				),
				'std'=>'',
				'depends'=>array('use_custom_button'=>1)
			),

			'button_status'=>array(
				'type'=>'buttons',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_ENABLE_BACKGROUND_OPTIONS'),
				'std'=>'normal',
				'values'=>array(
					array(
						'label' => 'Normal',
						'value' => 'normal'
					),
					array(
						'label' => 'Hover',
						'value' => 'hover'
					),
				),
				'tabs' => true,
				'depends'=>array(
					array('use_custom_button', '=', 1),
					array('button_type', '=', 'custom'),
				)
			),

			'button_background_color'=>array(
				'type'=>'color',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_BACKGROUND_COLOR'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_BACKGROUND_COLOR_DESC'),
				'std' => '#444444',
				'depends'=>array(
					array('button_appearance', '!=', 'gradient'),
					array('use_custom_button', '=', 1),
					array('button_type', '=', 'custom'),
					array('button_status', '=', 'normal'),
				),
			),

			'button_background_gradient'=>array(
				'type'=>'gradient',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BACKGROUND_GRADIENT'),
				'std'=> array(
					"color" => "#B4EC51",
					"color2" => "#429321",
					"deg" => "45",
					"type" => "linear"
				),
				'depends'=>array(
					array('use_custom_button', '=', 1),
					array('button_appearance', '=', 'gradient'),
					array('button_type', '=', 'custom'),
					array('button_status', '=', 'normal'),
				)
			),

			'button_color'=>array(
				'type'=>'color',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_COLOR'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_COLOR_DESC'),
				'std' => '#fff',
				'depends'=>array(
					array('use_custom_button', '=', 1),
					array('button_type', '=', 'custom'),
					array('button_status', '=', 'normal'),
				),
			),

			'button_background_color_hover'=>array(
				'type'=>'color',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_BACKGROUND_COLOR_HOVER'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_BACKGROUND_COLOR_HOVER_DESC'),
				'std' => '#222',
				'depends'=>array(
					array('button_appearance', '!=', 'gradient'),
					array('use_custom_button', '=', 1),
					array('button_type', '=', 'custom'),
					array('button_status', '=', 'hover'),
				),
			),

			'button_background_gradient_hover'=>array(
				'type'=>'gradient',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BACKGROUND_GRADIENT'),
				'std'=> array(
					"color" => "#429321",
					"color2" => "#B4EC51",
					"deg" => "45",
					"type" => "linear"
				),
				'depends'=>array(
					array('use_custom_button', '=', 1),
					array('button_appearance', '=', 'gradient'),
					array('button_type', '=', 'custom'),
					array('button_status', '=', 'hover'),
				)
			),

			'button_color_hover'=>array(
				'type'=>'color',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_COLOR_HOVER'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_COLOR_HOVER_DESC'),
				'std' => '#fff',
				'depends'=>array(
					array('use_custom_button', '=', 1),
					array('button_type', '=', 'custom'),
					array('button_status', '=', 'hover'),
				),
			),

			'button_size'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SIZE'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SIZE_DESC'),
				'values'=>array(
					''=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SIZE_DEFAULT'),
					'lg'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SIZE_LARGE'),
					'xlg'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SIZE_XLARGE'),
					'sm'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SIZE_SMALL'),
					'xs'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SIZE_EXTRA_SAMLL'),
				),
				'depends'=>array('use_custom_button'=>1)
			),

			'button_shape'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SHAPE'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SHAPE_DESC'),
				'values'=>array(
					'rounded'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SHAPE_ROUNDED'),
					'square'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SHAPE_SQUARE'),
					'round'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_SHAPE_ROUND'),
				),
				'depends'=>array('use_custom_button'=>1)
			),

			'button_block'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_BLOCK'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_BLOCK_DESC'),
				'values'=>array(
					''=>JText::_('JNO'),
					'sppb-btn-block'=>JText::_('JYES'),
				),
				'depends'=>array('use_custom_button'=>1)
			),

			'button_icon'=>array(
				'type'=>'icon',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_ICON'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_ICON_DESC'),
				'depends'=>array('use_custom_button'=>1)
			),

			'button_icon_position'=>array(
				'type'=>'select',
				'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_ICON_POSITION'),
				'values'=>array(
					'left'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_LEFT'),
					'right'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_RIGHT'),
				),
				'depends'=>array('use_custom_button'=>1)
			),

			'class'=>array(
				'type'=>'text',
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_CLASS'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_CLASS_DESC'),
				'std'=>''
			),

		),
	),
	)
);
