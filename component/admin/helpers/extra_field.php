<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHTML::_('behavior.tooltip');
jimport('joomla.filesystem.file');
JLoader::load('RedshopHelperHelper');
JLoader::load('RedshopHelperAdminImages');
JLoader::load('RedshopHelperExtra_Field');

class extra_field
{
	public function list_all_field_in_product($section = extraField::SECTION_PRODUCT)
	{
		$db = JFactory::getDbo();

		$query = "SELECT * FROM #__redshop_fields "
			. "WHERE field_section = " . (int) $section . " "
			. "AND display_in_product=1 "
			. "AND `published`=1 "
			. "ORDER BY ordering ";

		$db->setQuery($query);
		$row_data = $db->loadObjectlist();

		return $row_data;
	}

	public function list_all_field($field_section = "", $section_id = 0, $field_name = "", $table = "", $template_desc = "")
	{
		$db     = JFactory::getDbo();

		$uri    = JURI::getInstance();
		$url    = $uri->root();
		$q      = "SELECT * FROM #__redshop_fields WHERE field_section = " . (int) $field_section . " AND published=1 ";

		if ($field_name != '')
		{
			$field_name = redhelper::quote(explode(',', $field_name));
			$q .= 'AND field_name IN (' . implode(',', $field_name) . ') ';
		}

		$q .= " ORDER BY ordering";
		$db->setQuery($q);
		$row_data = $db->loadObjectlist();
		$ex_field = '';

		if (count($row_data) > 0 && $table == "")
		{
			$ex_field = '<table class="admintable" border="0" >';
		}

		for ($i = 0, $in = count($row_data); $i < $in; $i++)
		{
			$type = $row_data[$i]->field_type;
			$data_value = $this->getSectionFieldDataList($row_data[$i]->field_id, $field_section, $section_id);
			$ex_field .= '<tr>';
			$extra_field_value = "";
			$extra_field_label = JText::_($row_data[$i]->field_title);

			$required = '';
			$reqlbl = ' reqlbl="" ';
			$errormsg = ' errormsg="" ';

			if ($field_section == extraField::SECTION_QUOTATION && $row_data[$i]->required == 1)
			{
				$required = ' required="1" ';
				$reqlbl = ' reqlbl="' . $extra_field_label . '" ';
				$errormsg = ' errormsg="' . JText::_('COM_REDSHOP_THIS_FIELD_IS_REQUIRED') . '" ';
			}

			switch ($type)
			{
				case extraField::TYPE_TEXT:
					$text_value = ($data_value && $data_value->data_txt) ? $data_value->data_txt : '';
					$size = ($row_data[$i]->field_size > 0) ? $row_data[$i]->field_size : 20;
					$extra_field_value = '<input
											class="' . $row_data[$i]->field_class . '"
											type="text"
											maxlength="' . $row_data[$i]->field_maxlength . '" '
											. $required
											. $reqlbl
											. $errormsg
											. ' name="' . $row_data[$i]->field_name . '"
											id="' . $row_data[$i]->field_name . '"
											value="' . htmlspecialchars($text_value) . '"
											size="' . $size . '"
										/>';
					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_TEXT_AREA:
					$textarea_value = ($data_value && $data_value->data_txt) ? $data_value->data_txt : '';
					$extra_field_value = '<textarea class="' . $row_data[$i]->field_class . '"  name="' . $row_data[$i]->field_name . '" ' . $required . $reqlbl . $errormsg . ' id="' . $row_data[$i]->field_name . '" cols="' . $row_data[$i]->field_cols . '" rows="' . $row_data[$i]->field_rows . '" >' . htmlspecialchars($textarea_value) . '</textarea>';
					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_CHECK_BOX:
					$field_chk = $this->getFieldValue($row_data[$i]->field_id);
					$chk_data = @explode(",", $data_value->data_txt);

					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$extra_field_value = '';

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						$checked = (@in_array(urlencode($field_chk[$c]->field_value), $chk_data)) ? ' checked="checked" ' : '';
						$extra_field_value .= '<input  class="' . $row_data[$i]->field_class . '" type="checkbox" ' . $required . $reqlbl . $errormsg . ' ' . $checked . ' name="' . $row_data[$i]->field_name . '[]"  id="' . $row_data[$i]->field_name . "_" . $field_chk[$c]->value_id . '" value="' . urlencode($field_chk[$c]->field_value) . '" />' . $field_chk[$c]->field_value . '<br />';
					}

					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_RADIO_BUTTON:
					$field_chk = $this->getFieldValue($row_data[$i]->field_id);
					$chk_data = @explode(",", $data_value->data_txt);

					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$extra_field_value = '';

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						$checked = (@in_array(urlencode($field_chk[$c]->field_value), $chk_data)) ? ' checked="checked" ' : '';
						$extra_field_value .= '<input class="' . $row_data[$i]->field_class . '" type="radio" ' . $checked . ' ' . $required . $reqlbl . $errormsg . ' name="' . $row_data[$i]->field_name . '"  id="' . $row_data[$i]->field_name . "_" . $field_chk[$c]->value_id . '" value="' . urlencode($field_chk[$c]->field_value) . '" />' . $field_chk[$c]->field_value . '<br />';
					}

					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_SELECT_BOX_SINGLE:
					$field_chk = $this->getFieldValue($row_data[$i]->field_id);
					$chk_data = @explode(",", $data_value->data_txt);

					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$extra_field_value = '<select name="' . $row_data[$i]->field_name . '">';
					$extra_field_value .= '<option value="">' . JText::_('COM_REDSHOP_SELECT') . '</option>';

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						$selected = (isset($data_value->data_txt) && ($field_chk[$c]->field_value == $data_value->data_txt)) ? ' selected="selected" ' : '';
						$extra_field_value .= '<option value="' . $field_chk[$c]->field_value . '" ' . $selected . ' ' . $required . $reqlbl . $errormsg . '>' . $field_chk[$c]->field_name . '</option>';
					}

					$extra_field_value .= '</select>';
					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_SELECT_BOX_MULTIPLE:
					$field_chk = $this->getFieldValue($row_data[$i]->field_id);
					$chk_data = @explode(",", $data_value->data_txt);

					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$extra_field_value = '<select multiple size=10 name="' . $row_data[$i]->field_name . '[]">';

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						$selected = (@in_array(urlencode($field_chk[$c]->field_value), $chk_data)) ? ' selected="selected" ' : '';
						$extra_field_value .= '<option value="' . urlencode($field_chk[$c]->field_value) . '" ' . $selected . ' ' . $required . $reqlbl . $errormsg . '>' . $field_chk[$c]->field_name . '</option>';
					}

					$extra_field_value .= '</select>';
					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_SELECT_COUNTRY_BOX:
					$q = "SELECT * FROM #__redshop_country";
					$db->setQuery($q);
					$field_chk = $db->loadObjectlist();
					$chk_data = @explode(",", $data_value->data_txt);

					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$extra_field_value = '<select name="' . $row_data[$i]->field_name . '">';

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						$selected = (@in_array($field_chk[$c]->country_id, $chk_data)) ? ' selected="selected" ' : '';
						$extra_field_value .= '<option value="' . $field_chk[$c]->country_id . '" ' . $selected . ' '
							. $required . $reqlbl . $errormsg . '>' . $field_chk[$c]->country_name . '</option>';
					}

					$extra_field_value .= '</select>';
					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_WYSIWYG:
					$editor = JFactory::getEditor();
					$document = JFactory::getDocument();
					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$textarea_value = ($data_value && $data_value->data_txt) ? $data_value->data_txt : '';
					$extra_field_value = $editor->display($row_data[$i]->field_name, $textarea_value, '200', '50', '100', '20');
					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_DOCUMENTS:

					$document = JFactory::getDocument();
					JHtml::_('redshopjquery.ui');
					$document->addScriptDeclaration('
						jQuery(function($){
							var remove_a = null;
							$(\'a#add_' . $row_data[$i]->field_name . '\').on(\'click\', function(e){
								e.preventDefault();
								var extra_field_name = $(this).attr(\'title\'), extra_field_doc_html = "";
								var html_acceptor = $(\'#html_\'+extra_field_name);
								var total_elm = html_acceptor.children(\'div\').length;

								extra_field_doc_html = \'<div id="div_\'+extra_field_name+total_elm+\'" class="ui-helper-clearfix">\';
									extra_field_doc_html += \'<input type="text" value="" id="text_\'+extra_field_name+total_elm+\'" errormsg="" reqlbl="" name="text_\'+extra_field_name+\'[]">\';
									extra_field_doc_html += \'<input type="file" id="file_\'+extra_field_name+total_elm+\'" name="\'+extra_field_name+\'[]" class="">\';
									extra_field_doc_html += \'<a href="#" class="rsDocumentDelete" style="float:left;" title="\'+extra_field_name+\'" id="remove_\'+extra_field_name+total_elm+\'">' . JText::_('COM_REDSHOP_DELETE') . '</a>\';
								extra_field_doc_html += \'</div>\';

								html_acceptor.append(extra_field_doc_html);
								$(\'#div_\'+extra_field_name+total_elm).effect( \'highlight\');
							});
							$(\'#html_' . $row_data[$i]->field_name . '\').on(\'click\', \'a.rsDocumentDelete\', function(e){
								e.preventDefault();
								$(this).parent(\'div\').effect(\'highlight\',{},500,function(){
									$(this).remove();
								});
							});
						});
					');

					if (is_object($data_value) && property_exists($data_value, 'data_txt'))
					{
						// Support Legacy string.
						if (preg_match('/\n/', $data_value->data_txt))
						{
							$document_explode = explode("\n", $data_value->data_txt);
							$data_txt = array($document_explode[0] => $document_explode[1]);
						}
						else
						{
							// Support for multiple file upload using JSON for better string handling
							$data_txt = json_decode($data_value->data_txt);
						}
					}

					if (isset($data_txt) && count($data_txt) > 0)
					{
						$extra_field_value = "";
						$index = 0;

						foreach ($data_txt as $text_area_value_text => $text_area_value)
						{
							$extra_field_value .= '<div id="div_' . $row_data[$i]->field_name . $index . '">
											<input type="text" name="text_' . $row_data[$i]->field_name . '[]" ' . $required . $reqlbl . $errormsg . ' id="text_' . $row_data[$i]->field_name . $index . '" value="' . $text_area_value_text . '" />&nbsp;';
							$extra_field_value .= '<input class="' . $row_data[$i]->field_class . '"  name="' . $row_data[$i]->field_name . '[]"  id="file_' . $row_data[$i]->field_name . $index . '" type="file"  />';

							$destination_prefix = REDSHOP_FRONT_DOCUMENT_ABSPATH . 'extrafields/';
							$destination_prefix_phy = REDSHOP_FRONT_DOCUMENT_RELPATH . 'extrafields/';
							$destination_prefix_del = '/components/com_redshop/assets/document/extrafields/';
							$media_image = $destination_prefix_phy . $text_area_value;

							if (is_file($media_image))
							{
								$media_image = $destination_prefix . $text_area_value;
								$media_type = strtolower(JFile::getExt($text_area_value));

								if ($media_type == 'jpg' || $media_type == 'jpeg' || $media_type == 'png' || $media_type == 'gif')
								{
									$extra_field_value .= '<div id="docdiv' . $index . '"><img width="100"  src="' . $media_image . '" border="0" />&nbsp;<a href="#123"   onclick="delimg(\'' . $text_area_value . '\', \'div_' . $row_data[$i]->field_name . $index . '\',\'' . $destination_prefix_del . '\', \'' . $data_value->data_id . ':document\');"> Remove Media</a>&nbsp;<input class="' . $row_data[$i]->field_class . '"  name="' . $row_data[$i]->field_name . '[]"  id="' . $row_data[$i]->field_name . '" value="' . $text_area_value . '" type="hidden"  /></div>';
								}
								else
								{
									$extra_field_value .= '<div id="docdiv' . $index . '"><a href="' . $media_image . '" target="_blank">' . $text_area_value . '</a>&nbsp;<a href="#123"   onclick="delimg(\'' . $text_area_value . '\', \'div_' . $row_data[$i]->field_name . $index . '\',\'' . $destination_prefix_del . '\', \'' . $data_value->data_id . ':document\');"> Remove Media</a>&nbsp;<input class="' . $row_data[$i]->field_class . '"  name="' . $row_data[$i]->field_name . '[]"  id="' . $row_data[$i]->field_name . '" value="' . $text_area_value . '" type="hidden"  /></div>';
								}
							}
							else
							{
								$extra_field_value .= JText::_('COM_REDSHOP_FILE_NOT_EXIST');
							}

							$extra_field_value .= '</div>';

							$index++;
						}
					}

					$ex_field .= '<td width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$ex_field .= '<td><a href="#" title="' . $row_data[$i]->field_name . '" id="add_' . $row_data[$i]->field_name . '">' . JText::_('COM_REDSHOP_ADD') . '</a><div id="html_' . $row_data[$i]->field_name . '">' . $extra_field_value . '</div>';
					break;

				case extraField::TYPE_IMAGE_SELECT:
					$field_chk = $this->getFieldValue($row_data[$i]->field_id);
					$data_value = $this->getSectionFieldDataList($row_data[$i]->field_id, $field_section, $section_id);
					$value = '';

					if ($data_value)
					{
						$value = $data_value->data_txt;
					}

					$chk_data = @explode(",", $data_value->data_txt);
					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$extra_field_value = '<table><tr>';

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						if (in_array($field_chk[$c]->value_id, $chk_data))
						{
							$class = ' class="pointer imgClass_' . $section_id . ' selectedimg" ';
						}
						else
						{
							$class = ' class="pointer imgClass_' . $section_id . '"';
						}

						$extra_field_value .= '<td><div class="userfield_input"><img id="' . $field_chk[$c]->value_id . '" name="imgField[]" ' . $class . ' src="' . REDSHOP_FRONT_IMAGES_ABSPATH . 'extrafield/' . $field_chk[$c]->field_name . '" title="' . $field_chk[$c]->field_value . '" alt="' . $field_chk[$c]->field_value . '" onclick="javascript:setProductUserFieldImage(\'' . $field_chk[$c]->value_id . '\',\'' . $section_id . '\',\'' . $field_chk[$c]->field_id . '\',this);" /></div></td>';
					}

					$extra_field_value .= '<input type="hidden" name="imgFieldId' . $row_data[$i]->field_id . '" id="imgFieldId' . $row_data[$i]->field_id . '" value="' . $value . '"/>';
					$extra_field_value .= '</tr></table>';
					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_DATE_PICKER:

					if ($row_data[$i]->field_section != 17)
					{
						$date = date("d-m-Y", time());
					}
					else
					{
						$date = '';
					}

					if ($data_value)
					{
						if ($data_value->data_txt)
						{
							$date = date("d-m-Y", strtotime($data_value->data_txt));
						}
					}

					$size = ($row_data[$i]->field_size > 0) ? $row_data[$i]->field_size : 20;
					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$extra_field_value = JHTML::_('calendar', $date, $row_data[$i]->field_name, $row_data[$i]->field_name, $format = '%d-%m-%Y', array('class' => 'inputbox', 'size' => $size, 'maxlength' => '15'));
					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_IMAGE_WITH_LINK:

					$field_chk = $this->getFieldValue($row_data[$i]->field_id);
					$data_value = $this->getSectionFieldDataList($row_data[$i]->field_id, $field_section, $section_id);
					$value = ($data_value) ? $data_value->data_txt : '';
					$tmp_image_hover = array();
					$tmp_image_link = array();
					$chk_data = @explode(",", $data_value->data_txt);

					if ($data_value->alt_text)
					{
						$tmp_image_hover = explode(',,,,,', $data_value->alt_text);
					}

					if ($data_value->image_link)
					{
						$tmp_image_link = @explode(',,,,,', $data_value->image_link);
					}

					$chk_data = @explode(",", $data_value->data_txt);
					$image_link = array();
					$image_hover = array();

					for ($ch = 0; $ch < count($chk_data); $ch++)
					{
						$image_link[$chk_data[$ch]] = $tmp_image_link[$ch];
						$image_hover[$chk_data[$ch]] = $tmp_image_hover[$ch];
					}

					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$extra_field_value = '<table>';
					$c = 0;

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						$alt_text = '';
						$str_image_link = '';
						$extra_field_value .= '<tr>';

						if (in_array($field_chk[$c]->value_id, $chk_data))
						{
							$class = ' class="pointer imgClass_' . $section_id . ' selectedimg" ';
							$style1 = "display:block;";
							$str_image_link = $image_link[$field_chk[$c]->value_id];
							$alt_text = $image_hover[$field_chk[$c]->value_id];
						}
						else
						{
							$style1 = "display:none;";
							$class = ' class="pointer imgClass_' . $section_id . '"';
						}

						$extra_field_value .= '<td><div class="userfield_input"><img id="' . $field_chk[$c]->value_id . '" name="imgField[]" ' . $class . ' src="' . REDSHOP_FRONT_IMAGES_ABSPATH . 'extrafield/' . $field_chk[$c]->field_name . '" title="' . $field_chk[$c]->field_value . '" alt="' . $field_chk[$c]->field_value . '" onclick="javascript:setProductImageLink(\'' . $field_chk[$c]->value_id . '\',\'' . $section_id . '\',\'' . $field_chk[$c]->field_id . '\',this);" /></div></td>';
						$extra_field_value .= '<td><div id="hover_link' . $field_chk[$c]->value_id . '" style="' . $style1 . '">';
						$extra_field_value .= '<table><tr><td valign="top" width="100" align="right" class="key">' . JText::_('COM_REDSHOP_IMAGE_HOVER') . '</td><td><input type="text" name="image_hover' . $field_chk[$c]->value_id . '"  value="' . $alt_text . '"/></td></tr>';
						$extra_field_value .= '<tr><td valign="top" width="100" align="right" class="key">' . JText::_('COM_REDSHOP_IMAGE_LINK') . '</td><td><input type="text" name="image_link' . $field_chk[$c]->value_id . '" value="' . $str_image_link . '"/></td></tr>';
						$extra_field_value .= '</table></div></td>';
						$extra_field_value .= '</tr>';
					}

					$extra_field_value .= '<input type="hidden" name="imgFieldId' . $row_data[$i]->field_id . '" id="imgFieldId' . $row_data[$i]->field_id . '" value="' . $value . '"/>';
					$extra_field_value .= '</table>';
					$ex_field .= '<td>' . $extra_field_value;
					break;

				case extraField::TYPE_SELECTION_BASED_ON_SELECTED_CONDITIONS:

					$date = date("d-m-Y", time());

					if ($data_value)
					{
						if ($data_value->data_txt)
						{
							$mainsplit_date_total = preg_split(" ", $data_value->data_txt);
							$mainsplit_date = preg_split(":", $mainsplit_date_total[0]);
							$mainsplit_date_extra = preg_split(":", $mainsplit_date_total[1]);
							$date_publish = date("d-m-Y", $mainsplit_date[0]);
							$date_expiry = date("d-m-Y", $mainsplit_date[1]);
						}
						else
						{
							$date_publish = date("d-m-Y");
							$date_expiry = date("d-m-Y");
							$mainsplit_date_extra = array();
						}
					}
					else
					{
						$date_publish = date("d-m-Y");
						$date_expiry = date("d-m-Y");
						$mainsplit_date_extra = array();
					}

					if ($row_data[$i]->field_size > 0)
					{
						$size = $row_data[$i]->field_size;
					}
					else
					{
						$size = '20';
					}

					$ex_field .= '<td valign="top" width="100" align="right" class="key">' . $extra_field_label . '</td>';
					$extra_field_value = 'Publish Date: ';

					$extra_field_value .= "<input type='text' name='" . $row_data[$i]->field_name . "' value='" . $date_publish . "'>";
					$extra_field_value .= '&nbsp;&nbsp;&nbsp;&nbsp; Expiry Date: ';
					$extra_field_value .= "<input type='text' name='" . $row_data[$i]->field_name . "_expiry' value='" . $date_expiry . "'>";
					$extra_field_value .= '</td></tr><tr><td>&nbsp;</td><td>';
					$extra_field_value .= "<div class='col50' id='field_data'>";
					$extra_field_value .= "Enter Available Dates: <input type='button' name='addvalue' id='addvalue' class='button'  Value='" . JText::_('COM_REDSHOP_ADD_VALUE') . "' onclick='addNewRowcustom(" . $row_data[$i]->field_name . ");'/>";
					$extra_field_value .= "<fieldset class='adminform'>";
					$extra_field_value .= "<legend>'" . JText::_('COM_REDSHOP_VALUE') . "'</legend>";
					$extra_field_value .= "<table cellpadding='0' cellspacing='5' border='0' id='extra_table' width='95%'>";
					$extra_field_value .= "<tr><th width='20%'>'" . JText::_('COM_REDSHOP_OPTION_VALUE') . "'</th>
						<th>&nbsp;</th></tr>";

					if (count($mainsplit_date_extra) > 0)
					{
						for ($k = 0, $kn = count($mainsplit_date_extra); $k < $kn; $k++)
						{
							if ($mainsplit_date_extra[$k] != "")
							{
								$extra_field_value .= "<tr><td><div id='divfieldText'><input type='text' name='" . $row_data[$i]->field_name . "_extra_name[]'  value='" . date("d-m-Y", $mainsplit_date_extra[$k]) . "' name='" . $row_data[$i]->field_name . "_extra_name[]'></div></td><td><input value='Delete' onclick='deleteRow(this)' class='button' type='button' /></td>";
								$extra_field_value .= "</tr>";
							}
						}
					}
					else
					{
						$k = 1;
						$extra_field_value .= "<tr><td><div id='divfieldText'><input type='text' name='" . $row_data[$i]->field_name . "_extra_name[]' value='" . date('d-m-Y') . "' name='" . $row_data[$i]->field_name . "_extra_name[]'></div>
						</td>
						</tr>";
					}

					$extra_field_value .= "</table></fieldset></div><input type='hidden' value='" . $k . "' name='total_extra' id='total_extra'>";
					$ex_field .= '<td>' . $extra_field_value;
					break;
			}

			if (trim($template_desc) != '')
			{
				if (strstr($template_desc, "{" . $row_data[$i]->field_name . "}"))
				{
					$template_desc = str_replace("{" . $row_data[$i]->field_name . "}", $extra_field_value, $template_desc);
					$template_desc = str_replace("{" . $row_data[$i]->field_name . "_lbl}", $extra_field_label, $template_desc);
				}

				$template_desc = str_replace("{" . $row_data[$i]->field_name . "}", "", $template_desc);
				$template_desc = str_replace("{" . $row_data[$i]->field_name . "_lbl}", "", $template_desc);
			}
			else
			{
				if (trim($row_data[$i]->field_desc) == '')
				{
					$ex_field .= '</td><td valign="top">';
				}
				else
				{
					$ex_field .= '</td><td valign="top">&nbsp; ' . JHTML::tooltip($row_data[$i]->field_desc, $row_data[$i]->field_name, 'tooltip.png', '', '', false);
				}
			}

			$ex_field .= '</td></tr>';
		}

		if (count($row_data) > 0 && $table == "")
		{
			$ex_field .= '</table>';
		}

		if (trim($template_desc) != '')
		{
			return $template_desc;
		}

		return $ex_field;
	}

	public function extra_field_save($data, $field_section, $section_id = "", $user_email = "")
	{
		$db = JFactory::getDbo();



		$q = "SELECT * FROM #__redshop_fields "
			. "WHERE field_section IN (" . (int) $field_section . ") "
			. "AND published=1 ";
		$db->setQuery($q);
		$row_data = $db->loadObjectlist();

		for ($i = 0, $in = count($row_data); $i < $in; $i++)
		{
			$data_txt = '';

			if (isset($data[$row_data[$i]->field_name]))
			{
				if ($row_data[$i]->field_type == 8 || $row_data[$i]->field_type == 1 || $row_data[$i]->field_type == 2)
				{
					$data_txt = JRequest::getVar($row_data[$i]->field_name, '', ' ', 'string', JREQUEST_ALLOWRAW);
				}
				else
				{
					$data_txt = $data[$row_data[$i]->field_name];
				}
			}

			// Save Document Extra Field
			if ($row_data[$i]->field_type == extraField::TYPE_DOCUMENTS)
			{
				$files = $_FILES[$row_data[$i]->field_name]['name'];
				$texts = $data['text_' . $row_data[$i]->field_name];

				$documents_value = array();

				if (isset($data[$row_data[$i]->field_name]))
				{
					$documents_value = $data[$row_data[$i]->field_name];
				}

				$total = count($files);

				if (is_array($files) && $total > 0)
				{
					$documents = array();

					for ($ij = 0; $ij < $total; $ij++)
					{
						$file = $files[$ij];

						// Editing uploaded file
						if (isset($documents_value[$ij]) && $documents_value[$ij] != "")
						{
							if (trim($texts[$ij]) != '')
							{
								$documents[trim($texts[$ij])] = $documents_value[$ij];
							}
							else
							{
								$documents[$ij] = $documents_value[$ij];
							}
						}

						if ($file != "")
						{
							$name = RedShopHelperImages::cleanFileName($file);

							$src = $_FILES[$row_data[$i]->field_name]['tmp_name'][$ij];
							$destination = REDSHOP_FRONT_DOCUMENT_RELPATH . 'extrafields/' . $name;

							JFile::upload($src, $destination);

							if (trim($texts[$ij]) != '')
							{
								$documents[trim($texts[$ij])] = $name;
							}
							else
							{
								$documents[$ij] = $name;
							}
						}
					}

					// Convert array into JSON string for better handler.
					$data_txt = json_encode($documents);
				}
			}

			if ($row_data[$i]->field_type == extraField::TYPE_SELECTION_BASED_ON_SELECTED_CONDITIONS)
			{
				if ($data[$row_data[$i]->field_name] != "" && $data[$row_data[$i]->field_name . "_expiry"] != "")
				{
					$data_txt = strtotime($data[$row_data[$i]->field_name]) . ":" . strtotime($data[$row_data[$i]->field_name . "_expiry"]) . " ";

					if (count($data[$row_data[$i]->field_name . "_extra_name"]) > 0)
					{
						for ($r = 0; $r < count($data[$row_data[$i]->field_name . "_extra_name"]); $r++)
						{
							$data_txt .= strtotime($data[$row_data[$i]->field_name . "_extra_name"][$r]) . ":";
						}
					}
				}
			}

			if (is_array($data_txt))
			{
				$data_txt = implode(",", $data_txt);
			}

			$sect = explode(",", $field_section);

			if ($row_data[$i]->field_type == extraField::TYPE_IMAGE_SELECT || $row_data[$i]->field_type == extraField::TYPE_IMAGE_WITH_LINK)
			{
				$list = $this->getSectionFieldDataList($row_data[$i]->field_id, $field_section, $section_id, $user_email);

				if ($row_data[$i]->field_type == extraField::TYPE_IMAGE_WITH_LINK)
				{
					$field_value_array = explode(',', $data['imgFieldId' . $row_data[$i]->field_id]);
					$image_hover = array();
					$image_link = array();

					for ($fi = 0; $fi < count($field_value_array); $fi++)
					{
						$image_hover[$fi] = $data['image_hover' . $field_value_array[$fi]];
						$image_link[$fi] = $data['image_link' . $field_value_array[$fi]];
					}

					$str_image_hover = implode(',,,,,', $image_hover);
					$str_image_link = implode(',,,,,', $image_link);

					$sql = "UPDATE #__redshop_fields_data "
						. "SET alt_text = " . $db->quote($str_image_hover) . " , image_link = " . $db->quote($str_image_link) . " "
						. "WHERE itemid = " . (int) $section_id . " "
						. "AND section = " . $db->quote($field_section) . " "
						. "AND user_email = " . $db->quote($user_email) . " "
						. "AND fieldid = " . (int) $row_data[$i]->field_id . " ";
					$db->setQuery($sql);
					$db->execute();
				}

				if (count($list) > 0)
				{
					$sql = "UPDATE #__redshop_fields_data "
						. "SET data_txt = " . $db->quote($data['imgFieldId' . $row_data[$i]->field_id]) . " "
						. "WHERE itemid = " . (int) $section_id . " "
						. "AND section = " . $db->quote($field_section) . " "
						. "AND user_email = " . $db->quote($user_email) . " "
						. "AND fieldid = " . (int) $row_data[$i]->field_id . " ";
				}
				else
				{
					$sql = "INSERT INTO #__redshop_fields_data "
						. "(fieldid, data_txt, itemid, section, alt_text, image_link, user_email) "
						. "VALUE "
						. "(" . (int) $row_data[$i]->field_id . "," . $db->quote($data['imgFieldId' . $row_data[$i]->field_id])
						. "," . (int) $section_id . "," . $db->quote($field_section)
						. "," . $db->quote($str_image_hover) . "," . $db->quote($str_image_link)
						. ", " . $db->quote($user_email) . ")";
				}

				$db->setQuery($sql);
				$db->execute();
			}
			else
			{
				for ($h = 0, $hn = count($sect); $h < $hn; $h++)
				{
					$list = $this->getSectionFieldDataList($row_data[$i]->field_id, $sect[$h], $section_id, $user_email);

					if ($data_txt != '' || (count($list) > 0 && $list->data_txt != ''))
					{
						if (count($list) > 0)
						{
							$sql = "UPDATE #__redshop_fields_data "
								. "SET data_txt = " . $db->quote($data_txt) . " "
								. "WHERE itemid = " . (int) $section_id . " "
								. "AND section = " . (int) $sect[$h] . " "
								. "AND user_email = " . $db->quote($user_email) . " "
								. "AND fieldid = " . (int) $row_data[$i]->field_id . " ";
						}
						else
						{
							$sql = "INSERT INTO #__redshop_fields_data "
								. "(fieldid, data_txt, itemid, section, user_email) "
								. "VALUE "
								. "(" . (int) $row_data[$i]->field_id . "," . $db->quote($data_txt)
								. "," . (int) $section_id . "," . (int) $sect[$h] . ", " . $db->quote($user_email) . ")";
						}

						$db->setQuery($sql);
						$db->execute();
					}
				}
			}
		}
	}

	public function chk_extrafieldValidation($field_section = "", $section_id = 0)
	{
		$row_data = $this->getSectionFieldList($field_section);

		for ($i = 0, $in = count($row_data); $i < $in; $i++)
		{
			$required = $row_data[$i]->required;
			$data_value = $this->getSectionFieldDataList($row_data[$i]->field_id, $field_section, $section_id);

			if (empty($data_value) && $required)
			{
				return $row_data[$i]->field_title;
			}
		}

		return false;
	}

	public function list_all_field_display($field_section = "", $section_id = 0, $flag = 0, $user_email = "", $template_desc = "")
	{
		$db = JFactory::getDbo();

		$row_data = $this->getSectionFieldList($field_section);

		$ex_field = '';

		for ($i = 0, $in = count($row_data); $i < $in; $i++)
		{
			$type = $row_data[$i]->field_type;
			$extra_field_value = "";
			$extra_field_label = $row_data[$i]->field_title;

			if ($flag == 1)
			{
				if ($i > 0)
				{
					$ex_field .= "<br />";
				}

				$ex_field .= JText::_($extra_field_label) . ' : ';
			}
			else
			{
				$ex_field .= '<tr><td valign="top" align="left">' . JText::_($extra_field_label) . ' : </td><td>';
			}

			$data_value = $this->getSectionFieldDataList($row_data[$i]->field_id, $field_section, $section_id, $user_email);

			switch ($type)
			{
				case extraField::TYPE_TEXT:
					$extra_field_value = ($data_value && $data_value->data_txt) ? $data_value->data_txt : '';
					$ex_field .= $extra_field_value;
					break;

				case extraField::TYPE_TEXT_AREA:
					$extra_field_value = ($data_value && $data_value->data_txt) ? $data_value->data_txt : '';
					$ex_field .= $extra_field_value;
					break;

				case extraField::TYPE_CHECK_BOX:
					$field_chk = $this->getFieldValue($row_data[$i]->field_id);
					$chk_data = @explode(",", $data_value->data_txt);

					$extra_field_value = '';

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						if (@in_array($field_chk[$c]->field_value, $chk_data))
						{
							$extra_field_value .= $field_chk[$c]->field_value;
						}
					}

					$ex_field .= $extra_field_value;
					break;

				case extraField::TYPE_RADIO_BUTTON:
					$field_chk = $this->getFieldValue($row_data[$i]->field_id);
					$chk_data = @explode(",", $data_value->data_txt);

					$extra_field_value = '';

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						if (@in_array($field_chk[$c]->field_value, $chk_data))
						{
							$extra_field_value .= $field_chk[$c]->field_value;
						}
					}

					$ex_field .= $extra_field_value;
					break;

				case extraField::TYPE_SELECT_BOX_SINGLE:
					$field_chk = $this->getFieldValue($row_data[$i]->field_id);
					$chk_data = @explode(",", $data_value->data_txt);

					$extra_field_value = '';

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						if (@in_array($field_chk[$c]->field_value, $chk_data))
						{
							$extra_field_value .= $field_chk[$c]->field_value;
						}
					}

					$ex_field .= $extra_field_value;
					break;

				case extraField::TYPE_SELECT_BOX_MULTIPLE:
					$field_chk = $this->getFieldValue($row_data[$i]->field_id);
					$chk_data = @explode(",", $data_value->data_txt);

					$extra_field_value = '';

					for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
					{
						if (@in_array($field_chk[$c]->field_value, $chk_data))
						{
							if ($c > 0)
							{
								$extra_field_value .= "," . $field_chk[$c]->field_value;
							}
							else
							{
								$extra_field_value .= $field_chk[$c]->field_value;
							}
						}
					}

					$ex_field .= $extra_field_value;
					break;

				case extraField::TYPE_SELECT_COUNTRY_BOX:
					$extra_field_value = "";

					if ($data_value && $data_value->data_txt)
					{
						$q = "SELECT country_name FROM #__redshop_country "
							. "WHERE country_id = " . $db->quote($data_value->data_txt);
						$db->setQuery($q);
						$field_chk = $db->loadObject();
						$extra_field_value = $field_chk->country_name;
					}

					$ex_field .= $extra_field_value;
					break;

				// 12 :- Date Picker
				case extraField::TYPE_DATE_PICKER:
					$extra_field_value = ($data_value && $data_value->data_txt) ? $data_value->data_txt : '';
					$ex_field .= $extra_field_value;
					break;
			}

			if ($flag == 0)
			{
				$ex_field .= '</td></tr>';
			}

			if (trim($template_desc) != '')
			{
				if (strstr($template_desc, "{" . $row_data[$i]->field_name . "}"))
				{
					$template_desc = str_replace("{" . $row_data[$i]->field_name . "}", $extra_field_value, $template_desc);
					$template_desc = str_replace("{" . $row_data[$i]->field_name . "_lbl}", $extra_field_label, $template_desc);
				}

				$template_desc = str_replace("{" . $row_data[$i]->field_name . "}", "", $template_desc);
				$template_desc = str_replace("{" . $row_data[$i]->field_name . "_lbl}", "", $template_desc);
			}
		}

		if (trim($template_desc) != '')
		{
			return $template_desc;
		}

		return $ex_field;
	}


	public function list_all_user_fields($field_section = "", $section_id = extraField::SECTION_PRODUCT_USERFIELD, $field_type = '', $unique_id)
	{
		$db = JFactory::getDbo();
		$url = JURI::base();

		$document = JFactory::getDocument();
		JHtml::script('com_redshop/attribute.js', false, true);

		$q = "SELECT * FROM #__redshop_fields "
			. "WHERE field_section = " . (int) $section_id . " "
			. "AND field_name = " . $db->quote($field_section) . " "
			. "AND published=1 ";
		$db->setQuery($q);
		$row_data = $db->loadObjectlist();
		$ex_field = '';
		$ex_field_title = '';

		for ($i = 0, $in = count($row_data); $i < $in; $i++)
		{
			$type = $row_data[$i]->field_type;
			$asterisk = $row_data[$i]->required > 0 ? '* ' : '';

			if ($field_type != 'hidden')
			{
				$ex_field_title .= '<div class="userfield_label">' . $asterisk . $row_data[$i]->field_title . '</div>';
			}

			$data_value = $this->getSectionFieldDataList($row_data[$i]->field_id, $field_section, $section_id);

			$text_value = '';

			if ($field_type == 'hidden')
			{
				$ex_field .= '<input type="hidden" name="extrafieldId' . $unique_id . '[]"  value="' . $row_data[$i]->field_id . '" />';
			}
			else
			{
				$req = ' required = "' . $row_data[$i]->required . '"';

				switch ($type)
				{
					case extraField::TYPE_TEXT:
						$onkeyup = '';
						$ex_field .= '<div class="userfield_input"><input class="' . $row_data[$i]->field_class . '" type="text" maxlength="' . $row_data[$i]->field_maxlength . '" onkeyup="var f_value = this.value;' . $onkeyup . '" name="extrafieldname' . $unique_id . '[]"  id="' . $row_data[$i]->field_name . '" ' . $req . ' userfieldlbl="' . $row_data[$i]->field_title . '" value="' . $text_value . '" size="' . $row_data[$i]->field_size . '" /></div>';
						break;

					case extraField::TYPE_TEXT_AREA:
						$onkeyup = '';
						$ex_field .= '<div class="userfield_input"><textarea class="' . $row_data[$i]->field_class . '"  name="extrafieldname' . $unique_id . '[]"  id="' . $row_data[$i]->field_name . '" ' . $req . ' userfieldlbl="' . $row_data[$i]->field_title . '" cols="' . $row_data[$i]->field_cols . '" onkeyup=" var f_value = this.value;' . $onkeyup . '" rows="' . $row_data[$i]->field_rows . '" >' . $text_value . '</textarea></div>';
						break;

					case extraField::TYPE_CHECK_BOX:
						$field_chk = $this->getFieldValue($row_data[$i]->field_id);
						$chk_data = @explode(",", $cart[$idx][$row_data[$i]->field_name]);

						for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
						{
							$checked = (@in_array($field_chk[$c]->field_value, $chk_data)) ? ' checked="checked" ' : '';
							$ex_field .= '<div class="userfield_input"><input  class="' . $row_data[$i]->field_class . '" type="checkbox"  ' . $checked . ' name="extrafieldname' . $unique_id . '[]" id="' . $row_data[$i]->field_name . "_" . $field_chk[$c]->value_id . '" userfieldlbl="' . $row_data[$i]->field_title . '" value="' . $field_chk[$c]->field_value . '" ' . $req . ' />' . $field_chk[$c]->field_value . '</div>';
						}
						break;

					case extraField::TYPE_RADIO_BUTTON:
						$field_chk = $this->getFieldValue($row_data[$i]->field_id);
						$chk_data = @explode(",", $cart[$idx][$row_data[$i]->field_name]);

						for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
						{
							$checked = (@in_array($field_chk[$c]->field_value, $chk_data)) ? ' checked="checked" ' : '';
							$ex_field .= '<div class="userfield_input"><input class="' . $row_data[$i]->field_class . '" type="radio" ' . $checked . ' name="extrafieldname' . $unique_id . '[]" userfieldlbl="' . $row_data[$i]->field_title . '"  id="' . $row_data[$i]->field_name . "_" . $field_chk[$c]->value_id . '" value="' . $field_chk[$c]->field_value . '" ' . $req . ' />' . $field_chk[$c]->field_value . '</div>';
						}
						break;

					case extraField::TYPE_SELECT_BOX_SINGLE:
						$field_chk = $this->getFieldValue($row_data[$i]->field_id);
						$chk_data = @explode(",", $cart[$idx][$row_data[$i]->field_name]);
						$ex_field .= '<div class="userfield_input"><select name="extrafieldname' . $unique_id . '[]" ' . $req . ' id="' . $row_data[$i]->field_name . '" userfieldlbl="' . $row_data[$i]->field_title . '">';
						$ex_field .= '<option value="">' . JText::_('COM_REDSHOP_SELECT') . '</option>';

						for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
						{
							if ($field_chk[$c]->field_value != "" && $field_chk[$c]->field_value != "-" && $field_chk[$c]->field_value != "0" && $field_chk[$c]->field_value != "select")
							{
								$selected = (@in_array($field_chk[$c]->field_value, $chk_data)) ? ' selected="selected" ' : '';
								$ex_field .= '<option value="' . $field_chk[$c]->field_value . '" ' . $selected . '   >' . $field_chk[$c]->field_value . '</option>';
							}
						}

						$ex_field .= '</select></div>';
						break;

					case extraField::TYPE_SELECT_BOX_MULTIPLE:
						$field_chk = $this->getFieldValue($row_data[$i]->field_id);
						$chk_data = @explode(",", $cart[$idx][$row_data[$i]->field_name]);
						$ex_field .= '<div class="userfield_input"><select multiple="multiple" size=10 name="extrafieldname' . $unique_id . '[]" ' . $req . ' id="' . $row_data[$i]->field_name . '" userfieldlbl="' . $row_data[$i]->field_title . '">';

						for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
						{
							$selected = (@in_array($field_chk[$c]->field_value, $chk_data)) ? ' selected="selected" ' : '';
							$ex_field .= '<option value="' . $field_chk[$c]->field_value . '" ' . $selected . ' >' . $field_chk[$c]->field_value . '</option>';
						}

						$ex_field .= '</select></div>';
						break;

					case extraField::TYPE_DOCUMENTS:
						JHtml::_('redshopjquery.framework');
						JHtml::script('com_redshop/ajaxupload.js', false, true);
						$ajax = "";
						$ex_field .= '<div class="userfield_input"><input class="' . $row_data[$i]->field_class . '" type="button" value="' . JText::_('COM_REDSHOP_UPLOAD') . '" name="file' . $row_data[$i]->field_name . '_' . $unique_id . '"  id="file' . $row_data[$i]->field_name . '_' . $unique_id . '" ' . $req . ' userfieldlbl="' . $row_data[$i]->field_title . '" size="' . $row_data[$i]->field_size . '" /><p>' . JText::_('COM_REDSHOP_UPLOADED_FILE') . ':<ol id="ol_' . $row_data[$i]->field_name . '"></ol></p></div>';
						$ex_field .= '<input type="hidden" name="extrafieldname' . $unique_id . '[]" id="' . $row_data[$i]->field_name . '_' . $unique_id . '" ' . $req . ' userfieldlbl="' . $row_data[$i]->field_title . '"  />';

						$ex_field .= '<script>jQuery.noConflict();new AjaxUpload("file' . $row_data[$i]->field_name . '_' . $unique_id . '",{action:"index.php?tmpl=component&option=com_redshop&view=product&task=ajaxupload",data :{mname:"file' . $row_data[$i]->field_name . '_' . $unique_id . '"}, name:"file' . $row_data[$i]->field_name . '_' . $unique_id . '",onSubmit : function(file , ext){jQuery("' . $row_data[$i]->field_name . '").text("' . JText::_('COM_REDSHOP_UPLOADING') . '" + file);this.disable();}, onComplete :function(file,response){jQuery("<li></li>").appendTo(jQuery("#ol_' . $row_data[$i]->field_name . '")).text(response);var uploadfiles = jQuery("#ol_' . $ajax . $row_data[$i]->field_name . ' li").map(function() {return jQuery(this).text();}).get().join(",");jQuery("#' . $row_data[$i]->field_name . '_' . $unique_id . '").val(uploadfiles);jQuery("#' . $row_data[$i]->field_name . '").val(uploadfiles);this.enable();}});</script>';

						break;

					case extraField::TYPE_IMAGE_SELECT:
						$field_chk = $this->getFieldValue($row_data[$i]->field_id);
						$chk_data = @explode(",", $cart[$idx][$row_data[$i]->field_name]);
						$ex_field .= '<table><tr>';

						for ($c = 0, $cn = count($field_chk); $c < $cn; $c++)
						{
							$ex_field .= '<td><div class="userfield_input"><img id="' . $row_data[$i]->field_name . "_" . $field_chk[$c]->value_id . '" class="pointer imgClass_' . $unique_id . '" src="' . REDSHOP_FRONT_IMAGES_ABSPATH . 'extrafield/' . $field_chk[$c]->field_name . '" title="' . $field_chk[$c]->field_value . '" alt="' . $field_chk[$c]->field_value . '" onclick="javascript:setProductUserFieldImage(\'' . $row_data[$i]->field_name . '\',\'' . $unique_id . '\',\'' . $field_chk[$c]->field_value . '\',this);"/></div></td>';
						}

						$ex_field .= '</tr></table>';
						$ajax = '';

						$ex_field .= '<input type="hidden" name="extrafieldname' . $unique_id . '[]" id="' . $ajax . $row_data[$i]->field_name . '_' . $unique_id . '" userfieldlbl="' . $row_data[$i]->field_title . '" ' . $req . '  />';
						break;

					case extraField::TYPE_DATE_PICKER:
						$ajax = '';
						$req = $row_data[$i]->required;

						$ex_field .= '<div class="userfield_input">' . JHTML::_('calendar', $text_value, 'extrafieldname' . $unique_id . '[]', $ajax . $row_data[$i]->field_name . '_' . $unique_id, $format = '%d-%m-%Y', array('class' => $row_data[$i]->field_class, 'size' => $row_data[$i]->field_size, 'maxlength' => $row_data[$i]->field_maxlength, 'required' => $req, 'userfieldlbl' => $row_data[$i]->field_title, 'errormsg' => '')) . '</div>';
						break;
				}
			}

			if (trim($row_data[$i]->field_desc) != '' && $field_type != 'hidden')
			{
				$ex_field .= '<div class="userfield_tooltip">&nbsp; ' . JHTML::tooltip($row_data[$i]->field_desc, $row_data[$i]->field_name, 'tooltip.png', '', '', false) . '</div>';
			}
			else
			{
			}
		}

		$ex = array();
		$ex[0] = $ex_field_title;
		$ex[1] = $ex_field;

		return $ex;
	}

	public function booleanlist($name, $attribs = null, $selected = null, $yes = 'yes', $no = 'no', $id = false)
	{
		$arr = array(
			JHTML::_('select.option', "Days", JText::_($yes)),
			JHTML::_('select.option', "Weeks", JText::_($no))
		);

		return JHTML::_('select.radiolist', $arr, $name, $attribs, 'value', 'text', $selected, $id);
	}

	public function rs_booleanlist($name, $attribs = null, $selected = null, $yes = 'yes', $no = 'no', $id = false, $yes_value, $no_value)
	{
		$arr = array(
			JHTML::_('select.option', $yes_value, JText::_($yes)),
			JHTML::_('select.option', $no_value, JText::_($no))
		);

		return JHTML::_('redshopselect.radiolist', $arr, $name, $attribs, 'value', 'text', $selected, $id);
	}

	public function getFieldValue($id)
	{
		$db = JFactory::getDbo();

		$q = "SELECT * FROM #__redshop_fields_value "
			. "WHERE field_id = " . (int) $id . " "
			. "ORDER BY value_id ASC ";
		$db->setQuery($q);
		$list = $db->loadObjectlist();

		return $list;
	}

	public function getSectionFieldList($section = extraField::SECTION_PRODUCT_USERFIELD, $front = 1)
	{
		$db = JFactory::getDbo();

		$query = "SELECT * FROM #__redshop_fields "
			. "WHERE published=1 "
			. "AND field_show_in_front = " . (int) $front . " "
			. "AND field_section = " . (int) $section . "  ORDER BY ordering";
		$db->setQuery($query);
		$list = $db->loadObjectlist();

		return $list;
	}

	public function getSectionFieldDataList($fieldid, $section = 0, $orderitemid = 0, $user_email = "")
	{
		$db = JFactory::getDbo();

		$query = "SELECT * FROM #__redshop_fields_data "
			. "WHERE itemid = " . (int) $orderitemid . " "
			. "AND fieldid = " . (int) $fieldid . " "
			. "AND user_email = " . $db->quote($user_email) . " "
			. "AND section = " . (int) $section . " ";
		$db->setQuery($query);
		$list = $db->loadObject();

		return $list;
	}

	public function copy_product_extra_field($oldproduct_id, $newPid)
	{
		$db = JFactory::getDbo();

		$query = "SELECT * FROM #__redshop_fields_data "
			. "WHERE itemid = " . (int) $oldproduct_id . " "
			. "AND (section='1' or section = '12' or section = '17') ";
		$db->setQuery($query);
		$list = $db->loadObjectList();

		for ($i = 0, $in = count($list); $i < $in; $i++)
		{
			$sql = "INSERT INTO #__redshop_fields_data "
				. "(fieldid, data_txt, itemid, section, alt_text, image_link, user_email) "
				. "VALUE "
				. "(" . (int) $list[$i]->fieldid . "," . $db->quote($list[$i]->data_txt)
				. "," . (int) $newPid . "," . (int) $list[$i]->section . "," . $db->quote($list[$i]->alt_text)
				. "," . $db->quote($list[$i]->image_link) . ", " . $db->quote($list[$i]->user_email) . ")";

			$db->setQuery($sql);
			$db->execute();
		}
	}

	public function deleteExtraFieldData($data_id)
	{
		$db = JFactory::getDbo();

		$query = "DELETE FROM #__redshop_fields_data "
			. "WHERE data_id = " . (int) $data_id . " ";
		$db->setQuery($query);
		$db->execute();
	}
}

