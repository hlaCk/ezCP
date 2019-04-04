<?php

namespace hlaCk\ezCP\Widgets;

use Illuminate\Support\Str;
use hlaCk\ezCP\Facades\ezCP;

class PageDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = ezCP::model('Page')->count();
        $string = trans_choice('ezcp::dimmer.page', $count);

        return view('ezcp::dimmer', array_merge($this->config, [
            'icon'   => 'ezcp-file-text',
            'title'  => "{$count} {$string}",
            'text'   => __('ezcp::dimmer.page_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => __('ezcp::dimmer.page_link_text'),
                'link' => route('ezcp.pages.index'),
            ],
            'image' => ezcp_asset('images/widget-backgrounds/03.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return app('ezCPAuth')->user()->can('browse', ezCP::model('Page'));
    }
}
