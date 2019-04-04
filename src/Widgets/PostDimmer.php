<?php

namespace hlaCk\ezCP\Widgets;

use Illuminate\Support\Str;
use hlaCk\ezCP\Facades\ezCP;

class PostDimmer extends BaseDimmer
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
        $count = ezCP::model('Post')->count();
        $string = trans_choice('ezcp::dimmer.post', $count);

        return view('ezcp::dimmer', array_merge($this->config, [
            'icon'   => 'ezcp-news',
            'title'  => "{$count} {$string}",
            'text'   => __('ezcp::dimmer.post_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => __('ezcp::dimmer.post_link_text'),
                'link' => route('ezcp.posts.index'),
            ],
            'image' => ezcp_asset('images/widget-backgrounds/02.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return app('ezCPAuth')->user()->can('browse', ezCP::model('Post'));
    }
}
