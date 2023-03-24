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

/** @noinspection PhpUnused */


/**
 * checkboxComponent short summary.
 *
 * checkboxComponent description.
 *
 * @version 1.0
 * @author torst
 */
class mailattachmentComponent extends DefaultComponent
{

    function __construct($name, $componentHandler)
    {

        parent::__construct($name, $componentHandler);

        $this->showOnList = true;

    }


    /**
     * Summary of displayAsEdit
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @param string|int $rowKey
     * @return string
     * @throws Exception
     * @throws Exception
     */
    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {
        /*
        $xtraFormClass = isset($hvalue->xtraFormClass)?$hvalue->display." ".$hvalue->xtraFormClass:$hvalue->display;
        $required = isset($hvalue->required) && $hvalue->required == true;
        $requiredTxt = ($required?"required":"");
        ob_start();

        echo "<span id='textarea_" . $hvalue->name . "'>";

        $prevalue = $value;
        if (is_array ( $prevalue )) {
            $prevalue = json_encode ( $prevalue );
        }

        $disabled = ($hvalue->disabled)?"disabled":"";

        echo '<textarea '.$disabled.' '.$this->getDataHtml($hvalue->data).' '.' rows="10" style="width:400px" name="' . $hkey . '" class = "'.$xtraFormClass.' normal_admin_form_element admin_form_element" placeholder="' . $hvalue->getPlaceholder() . '" style="margin-left:10px" >';
        echo htmlentities($prevalue);
        //echo str_replace("\\r\\n", "\r\n", $prevalue); // TODO: Funktion suchen
        //echo $prevalue;
        echo "</textarea></span>";

        return ob_get_clean();
        */

        ob_start();

        ?>
        <input type="hidden" name="<?php echo $fieldName; ?>" value='<?php echo htmlspecialchars($value); ?>'/>
        <?php

        echo $this->displayAsLabel($value, $fieldName, $fieldDbModel, $rowKey, $model);


        return ob_get_clean();

    }

    /**
     * Summary of displayAsLabel
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     * @throws Exception
     */
    protected function displayAsLabel(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        if (empty($value)) return "-";

        ob_start();

        $attachments = json_decode($value);
        if (empty($attachments)) return "-";

        $count = 0;

        foreach ($attachments as $attachment) {

            // var_dump($attachment);
            echo "<div class='component_textcontainer'>";
            //echo "<a target='_blank' href='".BASE_URI.$attachment->fullpath."'>";
            echo "<a target='_blank' href='" . BASE_URI . "?p=maileroutbox&command=dl_mailattachment&filter=$rowKey&att_nr=$count'>";
            echo '<i class="tableicon fa fa-paperclip">' . __("Attachments") . '</i>';
            echo "</a>";
            echo "</div>";

            $count++;

        }


        return ob_get_clean();


        // combobox, multiselect combobox etc.
        /*
        if(!empty($fieldDbModel->entries)){
            $entries = $fieldDbModel->entries->getValueArray();

            if(!is_array($value)){
                if(isset($entries[$value])){
                    return $entries[$value];
                }else{
                    return "-";
                }
            }else{
                $first = true;
                $retval ="";
                foreach($value as $k=>$v){

                    if(isset($entries[$k]))
                    {
                        if(!$first){
                            $retval.=",";

                        }

                        $retval.=$entries[$k];

                        $first = false;
                    }


                }


                $value = $retval;

            }

        }


        if(isset($fieldDbModel->list_maxlength) && $fieldDbModel->list_maxlength<strlen($value)){
            $value = mb_substr($value, 0, $fieldDbModel->list_maxlength);
        }

        if(!empty($fieldDbModel->currency_symbol)){
            $value .= " ".$fieldDbModel->currency_symbol;
        }

        return $value;
        */

    }

    /**
     * Summary of displayAsEdit
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @return string
     */
    protected function displayAsAdd(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {

        return "not supported yet. (TODO: File Input Field?)";

        /* $xtraFormClass = isset($hvalue->xtraFormClass)?$hvalue->display." ".$hvalue->xtraFormClass:$hvalue->display;
         $required = isset($hvalue->required) && $hvalue->required == true;
         $requiredTxt = ($required?"required":"");

         ob_start();
         $prevalue = $value;
         if (is_array ( $prevalue )) {
             $prevalue = json_encode ( $prevalue );
         }
         echo "<span id='textarea_" . $hvalue->name. "'>";


         $disabled = ($hvalue->disabled)?"disabled":"";

         echo '<textarea '.$disabled.' '.$this->getDataHtml($hvalue->data).' '.' rows="10" style="width:400px" name="' . $hkey . '" class = "'.$xtraFormClass.' normal_admin_form_element admin_form_element" placeholder="' . $hvalue ->getPlaceholder() . '" style="margin-left:10px" >';
         // echo str_replace("\\r\\n", "\r\n", $prevalue); // TODO: Funktion suchen
         echo htmlentities($prevalue);

         echo "</textarea></span>";

         return ob_get_clean();*/

    }


}