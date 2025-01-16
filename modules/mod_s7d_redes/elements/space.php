<?php
/**
 * @package     S7d Redes
 * @subpackage  Form
 *
 * @copyright   Site 7 Dias - Todos os direitos reservados
 * @license     S7D Uso Exclusivo por Site 7 Dias
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class para S7D Descrições.
 *
 * @since  11.1
 */
class JFormFieldSpacer extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Spacer';

	/**
	 * Method to get the field input markup for a spacer.
	 * The spacer does not have accept input.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		return ' ';
	}

	/**
	 * Method to get the field label markup for a spacer.
	 * Use the label text or name from the XML element as the spacer or
	 * Use a hr="true" to automatically generate plain hr markup
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel()
	{
		$html = array();
		$class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$html[] = '<span class="spacer">';
		$html[] = '<span class="before"></span>';
		$html[] = '<span' . $class . '>';

		if ((string) $this->element['hr'] == 'true')
		{
			$html[] = '<hr' . $class . ' />';
		}
		else
		{
			$label = '';

			// Get the label text from the XML element, defaulting to the element name.
			$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
			$text = $this->translateLabel ? JText::_($text) : $text;

			// Build the class for the label.
			$class = !empty($this->description) ? 'hasTooltip' : '';
			$class = $this->required == true ? $class . ' required' : $class;

			// Add the opening label tag and main attributes attributes.
			$label .= '<label id="' . $this->id . '-lbl" class="' . $class . '"';

			// If a description is specified, use it to build a tooltip.
			if (!empty($this->description))
			{
				JHtml::_('bootstrap.tooltip');
				$label .= ' title="' . JHtml::tooltipText(trim($text, ':'), JText::_($this->description), 0) . '"';
			}

			// Add the label text and closing tag.
			$label .= '>' . $text . '</label>';
			$html[] = $label;
		}

		$html[] = '</span>';
		$html[] = '<span class="after"></span>';
		$html[] = '</span>';
		$html[] = '<style>.lap label { 
		background-color: rgb(89, 179, 89);
    	display: block;
    	max-width: none;
    	color: rgb(255, 255, 255);
    	font-weight: bold;
    	width: 980px;
    	padding-left: 10px;
    	height: 30px;
    	line-height: 30px;}</style>';

		return implode('', $html);
	}

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 *
	 * @since   11.1
	 */
	protected function getTitle()
	{
		return $this->getLabel();
	}
}
