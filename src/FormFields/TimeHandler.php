<?php

namespace hlaCk\ezCP\FormFields;

class TimeHandler extends AbstractHandler
{
    protected $codename = 'time';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('ezcp::formfields.time', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
