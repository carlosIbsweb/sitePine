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
		'addon_name'=>'sp_pie_progress',
		'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS'),
		'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_DESC'),
		'category'=>'Content',
		'attr'=>array(
			'general' => array(

				'admin_label'=>array(
					'type'=>'text',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL_DESC'),
					'std'=> ''
				),

				'percentage'=>array(
					'type'=>'slider',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_PERCENTAGE'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_PERCENTAGE_DESC'),
					'min'=>1,
					'max'=>100,
					'std'=>75
				),

				'size'=>array(
					'type'=>'slider',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_SIZE'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_SIZE_DESC'),
					'min'=>50,
					'max'=>1000,
					'std'=>110
				),

				'border_color'=>array(
					'type'=>'color',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_BORDER_COLOR'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_BORDER_COLOR_DESC'),
					'std'=>'rgba(48, 113, 255, 0.10)',
				),

				'border_active_color'=>array(
					'type'=>'color',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_BORDER_COLOR_ACTIVE'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_BORDER_COLOR_ACTIVE_DESC'),
					'std'=>'#3071FF',
				),

				'border_width'=>array(
					'type'=>'slider',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_BORDER_WIDTH'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_BORDER_WIDTH_DESC'),
					'min'=>1,
					'max'=>100,
					'std'=>5
				),

				'separator_icon'=>array(
					'type'=>'separator',
					'title'=>JText::_('Icon'),
				),

				'icon_name'=>array(
					'type'=>'icon',
					'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_ICON_NAME'),
					'std'=> ''
				),

				'icon_size'=>array(
					'type'=>'select',
					'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_ICON_SIZE'),
					'values'=>array(
						'fa-fw'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_ICON_SIZE_STANDARD'),
						'fa-lg fa-fw'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_ICON_SIZE_TINY'),
						'fa-2x fa-fw'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_ICON_SIZE_SMALL'),
						'fa-3x fa-fw'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_ICON_SIZE_MEDIUM'),
						'fa-4x fa-fw'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_ICON_SIZE_LARGE'),
						'fa-5x fa-fw'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_ICON_SIZE_EXTRA_LARGE'),
					),
					'std'=>'fa-3x fa-fw',
				),

				'separator1'=>array(
					'type'=>'separator',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_PIE_PROGRESS_CONTENT'),
				),

				'title'=>array(
					'type'=>'text',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_DESC'),
					'std'=>  'Pie Progress'
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

				'text'=>array(
					'type'=>'editor',
					'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_CONTENT'),
					'std'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer adipiscing erat eget risus sollicitudin pellentesque et non erat.'
				),

				'text_font_family'=>array(
					'type'=>'fonts',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_CONTENT_FONT_FAMILY'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_CONTENT_FONT_FAMILY_DESC'),
					'depends'=>array(array('text', '!=', '')),
					'selector'=> array(
						'type'=>'font',
						'font'=>'{{ VALUE }}',
						'css'=>'.sppb-addon-text { font-family: {{ VALUE }}; }'
					)
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
