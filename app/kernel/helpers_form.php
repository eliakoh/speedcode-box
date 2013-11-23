<?php

/*
  speedcode-box - Laurent ASENSIO - laurent@mindescape.eu
  Copyright (C) 2011  Laurent ASENSIO

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * app/kernel/helpers_form.php
 * 
 * Form helpers functions
 * 
 * All functions are prefixed by "form_"
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Helpers
 * @version         1.0
 * @copyright       Copyright (c) 2011
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */

/**
 * Return an HTML representation of a radiobox
 * @param string $name The name of the radiobox
 * @param string $value The value of the radiobox
 * @param string $label The text to print next to the radiobox
 * @param boolean $checked True to check the radiobox, false to uncheck
 * @param string $class A CSS class
 * @return string An HTML representation of a radiobox
 */
function form_radio($name, $value, $label, $checked = false, $class = '') {
    $attributes = array('type' => 'radio', 'name' => $name, 'value' => $value, 'class' => $class);
    if ($checked) {
        $attributes['checked'] = 'checked';
    }
    return '<span class="label-form-radio">' . $label . '</span> ' . form_input($attributes);
}

/**
 * Return an HTML representation of a group of radioboxes
 * @param string $name The name of the radioboxes
 * @param array $values The values of the radioboxes
 * @param string $current_value Needed to check the correct radiobox
 * @param string $separator The separator to be used between elements
 * @param string $class A CSS class
 * @return string An HTML representation of a group of radioboxes
 */
function form_radioboxes($name, $values, $current_value, $separator = '<br />', $class = '') {
    $out = '';
    foreach ($values as $value => $label) {
        $out .= form_radio($name, $value, $label, ($value == $current_value), $class) . $separator;
    }

    return $out;
}

/**
 * Return an HTML representation of a group of radioboxes (Yes / No)
 * @param string $name The name of the radioboxes
 * @param integer $value The current value of the radioboxes (1 / 0)
 * @param string $class A CSS class
 * @return string An HTML representation of a radiobox
 * @uses $_lang The global Language object
 */
function form_radioboxes_yn($name, $value, $class = '') {
    return form_radioboxes($name, array('1' => t('system.yes'), '0' => t('system.no')), $value, '&nbsp;&nbsp;', $class);
}

/**
 * Return an HTML representation of a checkbox
 * @param string $name The name of the checkbox
 * @param string $value The value of the checkbox
 * @param string $label The text to print next to the checkbox
 * @param boolean $checked True to check the checkbox, false to uncheck
 * @param string $class A CSS class
 * @return string An HTML representation of a checkbox
 */
function form_checkbox($name, $value, $label, $checked = false, $class = '', $id = '') {
    if (empty($id)) {
        $id = $name;
    }
    $attributes = array('type' => 'checkbox', 'name' => $name, 'value' => $value, 'class' => $class, 'id' => $id);
    if ($checked) {
        $attributes['checked'] = 'checked';
    }
    return form_input($attributes) . ' <label for="' . $attributes['id'] . '">' . $label . '</label>';
}

/**
 * Return an HTML representation of a group of checkboxes
 * @param string $name The name of the checkboxes
 * @param array $values The values of the checkboxes
 * @param string $current_value Needed to check the correct checkbox
 * @param string $separator The separator to be used between elements
 * @param string $class A CSS class
 * @return string An HTML representation of a group of checkboxes
 */
function form_checkboxes($name, $values, $current_values, $separator = '<br />', $class = '') {
    $out = '';
    $i = 0;
    if (!is_array($current_values)) {
        $current_values = array($current_values);
    }
    foreach ($values as $value => $label) {
        $out .= form_checkbox($name . '[]', $value, $label, in_array($value, $current_values), $class, $name . $i) . $separator;
        $i++;
    }

    return $out;
}

/**
 * Return an HTML representation of a text field
 * @param string $name The name of the field
 * @param array $values The value of the field
 * @param string $class A CSS class
 * @return string An HTML representation of a text field
 */
function form_text($name, $value, $class = '') {
    $attributes = array('type' => 'text', 'name' => $name, 'id' => $name, 'value' => $value, 'class' => $class);
    return form_input($attributes);
}

/**
 * Return an HTML representation of an hidden field
 * @param string $name The name of the field
 * @param array $values The value of the field
 * @return string An HTML representation of an hidden field
 */
function form_hidden($name, $value) {
    $attributes = array('type' => 'hidden', 'name' => $name, 'id' => $name, 'value' => $value);
    return form_input($attributes);
}

/**
 * Return an HTML representation of a file field
 * @param string $name The name of the field
 * @param string $class A CSS class
 * @return string An HTML representation of a file field
 */
function form_file($name, $class = '') {
    $attributes = array('type' => 'file', 'name' => $name, 'id' => $name, 'class' => $class);
    return form_input($attributes);
}

/**
 * Return an HTML representation of a textarea
 * @param string $name The name of the textarea
 * @param array $value The value of the textarea
 * @param string $class A CSS class
 * @param integer $rows The number of lines
 * @param integer $cols The number of columns
 * @return string An HTML representation of a textarea
 */
function form_textarea($name, $value, $class = '', $rows = 10, $cols = 80) {
    return '<textarea id="' . $name . '" name="' . $name . '" class="' . $class . '" rows="' . $rows . '" cols="' . $cols . '">' . $value . '</textarea>';
}

/**
 * Return an HTML representation of a select
 * @param string $name The name of the select
 * @param array $values The values to use. Keys goes into options value, values goes into inner option.
 * @param string $current_value The current value to select
 * @param string $class A CSS class
 * @return string An HTML representation of a select
 * Default class : styledselect_form_1
 */
function form_select($name, $values, $current_value = '', $class = '') {
    $out = '<select name="' . $name . '" class="' . $class . '" id="' . $name . '">' . "\n";
    foreach ($values as $key => $value) {
        $selected = '';
        if (is_array($current_value)) {
            foreach ($current_value as $v) {
                if ($v == $key || $v == $value) {
                    $selected = ' selected="selected"';
                    break;
                }
            }
        } elseif ($current_value == $key || $current_value === $value) {
            $selected = ' selected="selected"';
        }

        $out .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>' . "\n";
    }
    $out .= '</select>' . "\n";
    return $out;
}

/**
 * Return an HTML representation of an input
 * Helper for other inputs generation
 * @param array $attributes Attributes of the input element
 * @return string An HTML representation of the input
 */
function form_input($attributes) {
    $str_attributes = '';
    foreach ($attributes as $name => $value) {
        if (!empty($value)) {
            $str_attributes .= ' ' . $name . '="' . $value . '"';
        }
    }
    return '<input' . $str_attributes . ' />';
}

?>