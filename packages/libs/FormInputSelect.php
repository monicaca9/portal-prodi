<?php
if( !function_exists('FormInputSelect') ){
    function FormInputSelect($fieldname, $label, $required=false, $with_default_select=true,  $list = [], $data=null, $attr='', $etc=[]){
        return view('__partial.form.form_input_select', [
            'fieldname' => $fieldname,
            'label'     => $label,
            'list'      => $list,
            'required'  => $required,
            'default'   => $with_default_select,
            'data'      => $data,
            'attr'      => $attr,
            'helper'    => isset($etc['helper']) ?$etc['helper']:null,
            'column'    => isset($etc['column']) ?$etc['column']:null,
        ]);
    }
}
