<?php

namespace hlaCk\ezCP\FormFields;

class TextAreaHandler extends AbstractHandler
{
    protected $codename = 'text_area';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('ezcp::formfields.text_area', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
