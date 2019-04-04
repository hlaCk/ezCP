<?php

namespace hlaCk\ezCP\FormFields;

class MarkdownEditorHandler extends AbstractHandler
{
    protected $codename = 'markdown_editor';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('ezcp::formfields.markdown_editor', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
