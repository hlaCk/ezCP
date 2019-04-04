<?php

namespace hlaCk\ezCP\Actions;

class ViewAction extends AbstractAction
{
    public function getTitle()
    {
        return __('ezcp::generic.view');
    }

    public function getIcon()
    {
        return 'ezcp-eye';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-warning pull-right view',
        ];
    }

    public function getDefaultRoute()
    {
        return route('ezcp.'.$this->dataType->slug.'.show', $this->data->{$this->data->getKeyName()});
    }
}
