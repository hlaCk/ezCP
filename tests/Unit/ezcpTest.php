<?php

namespace hlaCk\ezCP\Tests\Unit;

use Illuminate\Support\Facades\Config;
use hlaCk\ezCP\Facades\ezCP;
use hlaCk\ezCP\Tests\TestCase;

class ezCPTest extends TestCase
{
    /**
     * Dimmers returns collection of widgets.
     *
     * This test will make sure that the dimmers method will give us a
     * collection of the configured widgets.
     */
    public function testDimmersReturnsCollectionOfConfiguredWidgets()
    {
        Config::set('ezcp.dashboard.widgets', [
            'hlaCk\\ezCP\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'hlaCk\\ezCP\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
        ]);

        $dimmers = ezCP::dimmers();

        $this->assertEquals(2, $dimmers->count());
    }

    /**
     * Dimmers returns collection of widgets which should be displayed.
     *
     * This test will make sure that the dimmers method will give us a
     * collection of the configured widgets which also should be displayed.
     */
    public function testDimmersReturnsCollectionOfConfiguredWidgetsWhichShouldBeDisplayed()
    {
        Config::set('ezcp.dashboard.widgets', [
            'hlaCk\\ezCP\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'hlaCk\\ezCP\\Tests\\Stubs\\Widgets\\InAccessibleDimmer',
            'hlaCk\\ezCP\\Tests\\Stubs\\Widgets\\InAccessibleDimmer',
        ]);

        $dimmers = ezCP::dimmers();

        $this->assertEquals(1, $dimmers->count());
    }
}
