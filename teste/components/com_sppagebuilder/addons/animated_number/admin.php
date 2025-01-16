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
		'addon_name'=>'sp_animated_number',
		'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER'),
		'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_DESC'),
		'category'=>'Content',
		'attr'=>array(
			'general' => array(
				'admin_label'=>array(
					'type'=>'text',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL_DESC'),
					'std'=> ''
				),

				'number'=>array(
					'type'=>'number',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_NUMBER'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_NUMBER_DESC'),
					'placeholder'=>'1000',
					'std'=>'1000',
				),

				'duration'=>array(
					'type'=>'number',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_DURATION'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_DURATION_DESC'),
					'placeholder'=>'1000',
					'std'=>'1000',
				),

				'font_size'=>array(
					'type'=>'slider',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_NUMBER_FONT_SIZE'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_NUMBER_FONT_SIZE_DESC'),
					'placeholder'=>36,
					'std'=>array(
						'md'=>36
					),
					'responsive'=>true,
					'max'=>400
				),

				'color'=>array(
					'type'=>'color',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_NUMBER_COLOR'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_NUMBER_COLOR_DESC'),
				),

				'counter_title'=>array(
					'type'=>'text',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_TITLE'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_TITLE_DESC'),
					'std'=>'Animated Number',
				),

				'title_font_size'=>array(
					'type'=>'slider',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_TITLE_FONT_SIZE'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ANIMATED_NUMBER_TITLE_FONT_SIZE_DESC'),
					'placeholder'=>18,
					'std'=>array(
						'md'=>18
					),
					'responsive'=>true,
					'max'=>400
				),

				'alignment'=>array(
					'type'=>'select',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_GLOBAL_CONTENT_ALIGNMENT'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_GLOBAL_CONTENT_ALIGNMENT_DESC'),
					'values'=>array(
						'sppb-text-left'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_LEFT'),
						'sppb-text-center'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_CENTER'),
						'sppb-text-right'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_RIGHT'),
					),
					'std'=>'sppb-text-center',
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
