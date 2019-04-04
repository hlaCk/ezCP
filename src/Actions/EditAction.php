<?php

namespace hlaCk\ezCP\Actions;

class EditAction extends AbstractAction
{
    public function getTitle()
    {
        return __('ezcp::generic.edit');
    }

    public function getIcon()
    {
        return 'ezcp-edit';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary pull-right edit',
        ];
    }

    public function getDefaultRoute()
    {
        return route('ezcp.'.$this->dataType->slug.'.edit', $this->data->{$this->data->getKeyName()});
    }
}
