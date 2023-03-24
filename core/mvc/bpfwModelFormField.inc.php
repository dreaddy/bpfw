<?php
/*
 *
 * Copyright (c) 2017-2023. Torsten Lüders
 *
 * Part of the BPFW project. For documentation and support visit https://bpfw.org .
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 */

/** @noinspection ALL */

// common
class VIEWSETTING
{

    const PRIMARYKEY = "primaryKey";

    const DATA_HTML = "data"; // data for label and edit/add form ... should be separated
    const FILTER_VALUE = "filterValue"; // value that is used for filtering ( get parameter filter=xxx )

    const PARENTCOMPONENT = "parentComponent";

    //const TYPE = "type"; // type (int, string etc)
    //const DISPLAY = "display"; // display as (component name)


    // const LABEL = "label"; // label in list and form

    // const FORMFIELD_SETTING_NAME = "name";

    const ENTRIES = "entries"; // entries for combobox etc
    const DEFAULTVALUE = "default";
    const DATA_MODEL = "datamodel";
    const DATA_FILTER = "datamodel_filter";

    const DATA_FILTERNAME = "datamodel_filtername";


    // Settings for complex Components that display Data

    const DATA_MODEL_SORTFIELD = "datamodel_sortfield";
    const DATA_MODEL_SORTORDER = "datamodel_sortorder";

    const DATA_MODEL_LISTFIELD = "datamodel_listfield";
    const DATA_MODEL_FIELDS = "datamodel_fields";
    const DATA_MODEL_LIST_DISPLAYHANDLER = "datamodel_list_displayhandler";

    const DATA_MODEL_SHOW_EDIT = "datamodel_show_edit";
    const DATA_MODEL_SHOW_ADD = "datamodel_show_add";
    const DATA_MODEL_SHOW_DELETE = "datamodel_show_delete";
    const DATA_MODEL_SHOW_DUPLICATE = "datamodel_show_duplicate";

    const DATA_MODEL_SHOW_MODIFICATION_BUTTONS = "datamodel_show_modification_buttons";

    const DATA_MODEL_ENABLE_SEARCH = "datamodel_enable_search";
    const DATA_MODEL_ENABLE_FILTER = "datamodel_enable_filter";
    const DATA_MODEL_ENABLE_SORTING = "datamodel_enable_sorting";
    const DATA_MODEL_DEFAULTLENGTH = "datamodel_defaultlength";

    const DATA_MODEL_SHOW_HEADER = "datamodel_show_header";

    const VIEWSETTING_CUSTOM = "viewsettings_custom";

    const LINKTABLE_VALUENAME = "linktable_valuename";
}

// form

define("HIDDEN_INPUT_CLASS", "component_hiddenInput");

class FORMSETTING
{

    const FORM_SPOILER_HINT = "form_spoiler_hint";

    const SHOW_COLON = "show_colon";

    const MAXLENGTH = "maxlength";

    const SIGNATURE_EDITABLE_ON_EDIT = "signature_EditableOnEdit";

    const COMBOBOX_PLACEHOLDER_TEXT = "comboboxPlaceholderText";
    const COMBOBOX_PLACEHOLDER_SHOW = "comboboxPlaceholderShow";

    const SHOWLABEL_IN_FORM = "showLabelInForm";
    const XTRAFORMCLASS = "xtraFormClass"; // css class for formular field

    const XTRAFORMCLASSWRAPPER = "xtraFormClassWrapper"; // css class for formular field containing Wrapper
    // const XTRAFORMCLASSFORWRAPPER = "xtraclassForWrapper"; // css class for formular field

    const POSITION = "position"; // formular position right or left
    const PAGE = "page"; // formpage
    const EDITPAGE = "editpage"; // page for edit
    const ADDPAGE = "addpage"; // page for add

    const FORMFIELD_SUBTYPE = "formfield_subtype"; // like email, phone, zip etc. improves verification.

    const EXTRABUTTON_EDIT = "extrabutton_edit"; // edit button to component in form.. implemented for tinymce component.
    const EXTRABUTTON_ADD = "extrabutton_add"; // add button to component in form.. implemented for tinymce component.

    // const FORMFIELD_SETTING_AUTOCOMPLETE = "autocomplete"; // add autocomplete to formfield

    const NUMBERFIELDS_STEP = "step"; // step for numeric form fields (up down buttons)
    const NUMBERFIELDS_REGEX_PATTERN = "pattern";

    const MAX = "max";
    const MIN = "min";

    const LABEL_SMALL = "label_small"; // small label in form

    const HIDDENONEDIT = "hiddenOnEdit";

    const HIDDENONADD = "hiddenOnAdd";

    const DISABLED = "disabled";


    const REQUIRED = "required";
    const DONOTEDIT = "doNotEdit"; // disabled for signature, merge?


    const BASEFORMFIELD = "baseformfield";

    const TEXTFIELD_TYPE = "textfield_type";


    const PLACEHOLDER = "placeholder";


    const HAS_BLOBDATA = "has_blobdata";

    const USE_EXTENDED_UPLOADER = "filefield_use_extended_uploader"; // upload field uses drag&drop uploader

    const FILE_FILETYPES = "file_filetypes"; // upload field uses drag&drop uploader

    const BUTTONICON_FORM = "buttonicon_form";

    const FORMSETTING_CUSTOM = "formsettings_custom";


    const IMAGECOMPONENT_SHOW_SAVEBUTTON = "imagecomponent_show_savebutton";
    const IMAGECOMPONENT_SHOW_TYPESWITCHER = "imagecomponent_show_typeswitcher";


    const IMAGECOMPONENT_VIEWPORT_WIDTH = "imagecomponent_viewport_width";
    const IMAGECOMPONENT_VIEWPORT_HEIGHT = "imagecomponent_viewport_height";
    const IMAGECOMPONENT_VIEWPORT_TYPE = "imagecomponent_viewport_type"; // (square circle)
    const IMAGECOMPONENT_BOUNDRY_WIDTH = "imagecomponent_boundry_width";
    const IMAGECOMPONENT_BOUNDRY_HEIGHT = "imagecomponent_boundry_height";

    const IMAGECOMPONENT_ENABLE_RESIZE = "imagecomponent_enable_resize";
    const IMAGECOMPONENT_ENABLE_ZOOM = "imagecomponent_enable_zoom";

    const IMAGECOMPONENT_OPTIONAL_CROP = "imagecomponent_optional_crop";
    const IMAGECOMPONENT_ENABLE_CROP = "imagecomponent_enable_crop";

    const DATETIME_MAX_DATE_DAYS = "max_date_days";
    const DATETIME_MIN_DATE_DAYS = "min_date_days";

    const COMBOBOX_LIVESEARCH = "combobox_livesearch";

    const DESCRIPTION_TEXT = "description_text";

}


class TextComponentSubtype
{
    const SUBTYPE_EMAIL = "email";
    const SUBTYPE_PHONE = "phone";
}


class IMAGECROPPER_TYPE
{
    const CIRCLE = "circle";
    const SQUARE = "square";
}

// list

class LISTSETTING
{

    const LIST_SPOILER_HINT = "list_spoiler_hint";

    const LABEL_LIST = "label_list"; // dann wie label

    const CONDITION = "condition"; // display only in list if condition met


    const HIDDENONLIST = "hiddenOnList";

    const HIDDENONPRINT = "hiddenOnPrint";

    const AJAX_SORTABLE = "ajax_sortable";

    const MAXLENGTH = "list_maxlength";

    const AJAX_SORTSEARCH = "sortsearch";

    const IS_SEARCHABLE = "is_searchable";

    const SHOWLABEL_IN_LIST = "showLabelInList";

    const BUTTONICON_LIST = "buttonicon_list";

    const LISTSETTING_CUSTOM = "listsettings_custom";

    const CURRENCY_SYMBOL = "currency_symbol";

    const BOLD_CONTENT = "bold_content";

    const BACKGROUND = "list_background";

    const BORDER = "list_border";

    const COLOR = "list_color";


    const BACKGROUND_FUNC = "list_background_func";

    const BORDER_FUNC = "list_border_func";

    const COLOR_FUNC = "list_color_func";

}


class BpfwSortsearch
{


    /**
     * Summary of $join
     * @var string
     */
    public string $join;
    /**
     * Summary of $field
     * @var string
     */
    public string $field;

    function __construct($join, $field)
    {

        $this->join = $join;
        $this->field = $field;

    }

}


/**
 * BpfwModelFormfield short summary.
 *
 * BpfwModelFormfield description.
 *
 * @version 1.0
 * @author torst
 */
class BpfwModelFormfield
{

    public int $maxlength = 0;
    public bool $signature_EditableOnEdit = false;
    /**
     * Model data is collected from
     * @var string
     */
    public ?string $datamodel = null;
    /**
     * filter of modelcontent -> ""(none) or "formid" or (numeric) key
     * @var string ""(none) or "formid" or (numeric) key
     */
    public string $datamodel_filter = "";
    /**
     * filter of modelcontent -> ""(none) or "formid" or (numeric) key
     * @var string ""(none) or "formid" or (numeric) key
     */
    public string $datamodel_filtername = "primaryKeyOfParent";
    /**
     * shown datafields of model
     * @var array
     */
    public string|array $datamodel_fields = "default";
    /**
     * entry to show in the table
     * @var string
     */
    public string $datamodel_listfield = "first_entry";
    /**
     * shown datafields of model in parent list
     * @var EnumHandlerInterface
     */
    public ?EnumHandlerInterface $datamodel_list_displayhandler = null;
    /**
     * sortorder of modellist
     * @var string 0 or fieldid
     */
    public string|int $datamodel_sortfield = 0;
    /**
     * sortorder of modellist
     * @var string asc or desc
     */
    public string $datamodel_sortorder = "asc";
    /**
     * show edit functionality in list
     * @var string
     */
    public string|bool $datamodel_show_edit = true;
    /**
     * show add functionality in List
     * @var string
     */
    public string|bool $datamodel_show_add = true;
    /**
     * show delete functionality in List
     * @var string
     */
    public string|bool $datamodel_show_delete = true;

    // const DATA_SORTFIELD = "datamodel_sortfield";
    // const DATA_SORTORDER = "datamodel_sortorder";
    /**
     * show duplicate functionality in List
     * @var string
     */
    public string|bool $datamodel_show_duplicate = true;
    /**
     * disable edit, delete, duplicate, add and custom buttons
     * @var string
     */
    public string|bool $datamodel_show_modification_buttons = true;
    /**
     * enable searching of datatable
     * @var string
     */
    public string|bool $datamodel_enable_search = false;
    /**
     * enable filter of datatable
     * @var string
     */
    public string|bool $datamodel_enable_filter = false;
    /**
     * show delete functionality in List
     * @var string
     */
    public string|bool $datamodel_enable_sorting = true;
    /**
     * show delete functionality in List
     * @var string
     */
    public string|int $datamodel_defaultlength = 100;
    /**
     * show header
     * @var string
     */
    public string|bool $datamodel_show_header = true;
    /**
     * Summary of $sortsearch
     * @var BpfwSortsearch
     */
    public BpfwSortsearch $sortsearch;
    /**
     * Summary of $sortsearch
     * @var BpfwSortsearch
     */
    public bool|BpfwSortsearch $is_searchable = true;
    /**
     * placeholder for textfields (overrides label on this position)
     * @var string
     */
    public ?string $placeholder = null;
    /**
     * step for Float number fields
     * @var string|float|int
     */
    public string|int|float $step;
    /**
     * regex pattern for Field (only supported for numeric fields right now)
     * @var string
     */
    public string $pattern;
    /**
     * regex pattern for Field (only supported for numeric fields right now)
     * @var string
     */
    public string $min = "";
    /**
     * regex pattern for Field (only supported for numeric fields right now)
     * @var string
     */
    public string $max = "";
    /**
     * enable browser autocomplete
     * @var bool
     */
    public bool $autocomplete;
    /**
     * condition for display like "condition"=>array("status"=>0)
     * @var array
     */
    public array $condition;
    /**
     * multipage forms -> on which page (on add mode)
     * @var int
     */
    public int $addpage = 1;
    /**
     * multipage forms -> on which page (on edit mode)
     * @var int
     */
    public int $editpage = 1;
    /**
     * left or right -> where to be shown on the top Form
     * @var string
     */
    public string $position = POSITION_LEFT;
    /**
     * CSS Classes attached to this Component
     * @var mixed
     */
    public mixed $xtraFormClass = "";
    /**
     * CSS Classes attached to this Component Wrapper / sourounding css container
     * @var mixed
     */
    public mixed $xtraFormClassWrapper = "";
    /**
     * axlength of listlabel
     * @var int
     */
    public int $list_maxlength = 999;
    /**
     * CSS Classes attached to this Component
     * @var mixed
     */
    public mixed $data = array();
    /**
     * Show Label in Listiew
     * @var bool
     */
    public bool $showLabelInForm = true;
    /**
     * Show Combobox Placeholder if nothing selected
     * @var mixed
     */
    public mixed $comboboxPlaceholderShow = false;
    /**
     * Default Combobox Placeholder Value
     * @var mixed
     */
    public mixed $comboboxPlaceholderText = "(Vorschlag w&auml;hlen)";
    /**
     * Summary of $combobox_livesearch
     * @var mixed
     */
    public mixed $combobox_livesearch = true;
    /**
     * Show Label in Listiew
     * @var bool
     */
    public bool $showLabelInList = false;
    /**
     * Show Label in Listiew
     * @var bool
     */
    public bool $filterValue = false;
    public string $parentComponent = "";
    /**
     * Name/ID of the field
     * @var string
     */
    public string $name;
    /**
     * Name/ID of the field
     * @var string
     */
    public string $label;
    /**
     * buttonicon of the field
     * @var string
     */
    public string $buttonicon_form;
    /**
     * buttonicon of the field
     * @var string
     */
    public string $buttonicon_list;
    /**
     * subheadline of formfield
     * @var string
     */
    public string $label_small;
    /**
     * name of the Component to display the Value
     * @var string
     */
    public string $display;
    /**
     * name of the Component to display the Value
     * @var string
     */
    public string $description_text = "";
    /**
     * max days selection datetimefield datefield -31 - +31
     * @var int
     */
    public int $max_date_days;
    /**
     * min days selection datetimefield datefield -31 - +31
     * @var int
     */
    public int $min_date_days;
    /**
     * db Type of the Component (int, string etc). Use Constants TYPE_ ... or a BpfwDbFieldType
     * @var BpfwDbFieldType
     */
    public BpfwDbFieldType $type;
    /**
     * Default Value of $default
     * @var mixed
     */
    public mixed $default = "";
    /**
     * Entries of a Combobox or other list Container (key Value pair 1=>male, 2=>female etc.)
     * @var EnumHandlerInterface|array|null
     */
    public EnumHandlerInterface|array|null $entries = array();
    /**
     * Summary of $primaryKey
     * @var bool
     */
    public bool $primaryKey = false;
    /**
     * Hidden on the print view
     * @var bool
     */
    public bool $hiddenOnPrint = false;
    /**
     * form field disabled?
     * @var bool
     */
    public bool $disabled = false;
    /**
     * Hidden on the List part of the default View
     * @var bool
     */
    public bool $hiddenOnList = false;
    /**
     * Label as shown in header of the list. defaults to the label that is used in the form
     * @var string
     */
    public string $label_list = "like_form_label";
    /**
     * (?) shown in list
     * @var mixed
     */
    public mixed $list_spoiler_hint = "";
    /**
     * (?) shown in form next to label
     * @var mixed
     */
    public mixed $form_spoiler_hint = "";
    /**
     * sortable in Ajax based data-List. true, false or use component default (null)
     * @var bool|null
     */
    public ?bool $ajax_sortable = null;
    /**
     * Hidden on the Edit part of the default View
     * @var bool
     */
    public bool $hiddenOnEdit = false;
    /**
     * Hidden on the Add part of the default View
     * @var bool
     */
    public bool $hiddenOnAdd = false;
    /**
     * Do not Edit. (not in the form instead of hidden )
     * @var bool
     */
    public bool $doNotEdit = false;
    /**
     * has to be filled out in the form Part
     * @var bool
     */
    public bool $required = false;
    public array $datetimeIntervalCombobox = array();
    public string $textfield_type = "";
    public string $baseformfield = "";
    public array $extrabutton_add = array();
    public array $extrabutton_edit = array();
    public string $formfield_subtype = "";
    public bool $filefield_use_extended_uploader = true;
    public string $file_filetypes = ".xls,.pdf,.png,.jpg,.doc,.docx,.xlsx";
    public bool $imagecomponent_show_savebutton = false;
    /**
     * Custom Settings for common View Settings
     * @var array
     */
    public array $viewsettings_custom = array();
    /**
     * Linktable value name in case of keyname and valuename being identical
     * @var string
     */
    public string $linktable_valuename = "";
    /**
     * formsetting -> show colon in label
     * @var array
     */
    public array|bool $show_colon = DEFAULT_SHOW_COLON_IN_FORM;
    /**
     * Custom Settings for Listdisplay
     * @var array
     */
    public array $listsettings_custom = array();
    /**
     * Custom Settings for Formdisplay
     * @var array
     */
    public array $formsettings_custom = array();
    public $has_blobdata;
    public bool $imagecomponent_show_typeswitcher = false;
    public int $imagecomponent_viewport_width = 250;
    public int $imagecomponent_viewport_height = 250;
    public string $imagecomponent_viewport_type = IMAGECROPPER_TYPE::SQUARE;
    public int $imagecomponent_boundry_width = 350;
    public int $imagecomponent_boundry_height = 300;
    public bool $imagecomponent_enable_resize = true;
    public bool $imagecomponent_enable_zoom = true;
    public bool $imagecomponent_optional_crop = true;
    public bool $imagecomponent_enable_crop = true;
    public string $currency_symbol = "";
    public bool $bold_content = false;
    public string $list_background = "";
    public string $list_border = "";
    public string $list_color = "";
    public string $list_background_func = "";
    public string $list_border_func = "";
    public string $list_color_func = "";

    /**
     *
     * public static function getDefaultValuesForDbType(){
     *
     * }
     *
     *
     * /**
     * Summary of __construct
     * @param string $name
     * @param string $label
     * @param mixed $dbType
     * @param string $display
     * @param array $attributes
     *
     * @throws Exception
     */
    function __construct(string $name, string $label, mixed $dbType, string $display, array $attributes)
    {

        $this->name = $name;
        $this->label = $label;
        $this->display = $display;


        if (isset($attributes[FORMSETTING::PAGE])) {
            if (!isset($attributes[FORMSETTING::ADDPAGE])) {
                $attributes[FORMSETTING::ADDPAGE] = $attributes[FORMSETTING::PAGE];
            }
            if (!isset($attributes[FORMSETTING::EDITPAGE])) {
                $attributes[FORMSETTING::EDITPAGE] = $attributes[FORMSETTING::PAGE];
            }
            unset($attributes[FORMSETTING::PAGE]);
        }

        if (is_string($dbType)) {
            $this->type = new BpfwDbFieldType($dbType);
        } else if ($dbType instanceof BpfwDbFieldType) {
            $this->type = $dbType;
        } else {
            throw new Exception("invalid type");
        }

        if (isset($attributes[FORMSETTING::HIDDENONEDIT]) && !isset($attributes[FORMSETTING::HIDDENONADD])) {
            $attributes[FORMSETTING::HIDDENONADD] = $attributes[FORMSETTING::HIDDENONEDIT];
        }

        if (!is_array($attributes)) {
            echo "No array as attributes given on: $name $label $display $dbType";
        }


        $componentinfo = bpfw_getComponentHandler()->getComponent($display);


        foreach ($componentinfo->getCustomAttributes() as $keyname => $defaultValue) {
            if (!property_exists('BpfwModelFormfield', $keyname)) {
                $this->$keyname = $defaultValue;
            } else {
                throw new Exception("component wants to override default attribute '$keyname'");
            }
        }

        foreach ($attributes as $key => $value) {
            if (!property_exists('BpfwModelFormfield', $key) && !isset($componentinfo->getCustomAttributes()[$key])) {
                echo "Warning: Attribute '$key' from $name / $label is not existing in BpfwModelFormfield.php or component (add to class first) custom attributes: " . print_r($componentinfo->getCustomAttributes(), true);
            }
            $this->$key = $value;
        }

    }

    function getSetting($settingname)
    {

        if (!empty($this->$settingname)) {
            return $this->$settingname;
        } else {

            throw new Exception("Invalid setting $settingname");

        }

    }

    public function getPlaceholder()
    {

        if ($this->placeholder !== null) {
            return $this->placeholder;
        }

        return $this->label;

    }

    /**
     * @throws Exception
     */
    public function getDefaultForMysql()
    {
        $default = $this->default;
        $componentHandler = bpfw_getComponentHandler();
        $component = $componentHandler->AddComponent($this->display, true);
        return $component->getMysqlValue($default);
    }
}
