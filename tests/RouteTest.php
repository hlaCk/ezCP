<?php

namespace hlaCk\ezCP\Tests;

class RouteTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testGetRoutes()
    {
        $this->disableExceptionHandling();

        $this->visit(route('ezcp.login'));
        $this->type('admin@admin.com', 'email');
        $this->type('password', 'password');
        $this->press(__('ezcp::generic.login'));

        $urls = [
            route('ezcp.dashboard'),
            route('ezcp.media.index'),
            route('ezcp.settings.index'),
            route('ezcp.roles.index'),
            route('ezcp.roles.create'),
            route('ezcp.roles.show', ['role' => 1]),
            route('ezcp.roles.edit', ['role' => 1]),
            route('ezcp.users.index'),
            route('ezcp.users.create'),
            route('ezcp.users.show', ['user' => 1]),
            route('ezcp.users.edit', ['user' => 1]),
            route('ezcp.posts.index'),
            route('ezcp.posts.create'),
            route('ezcp.posts.show', ['post' => 1]),
            route('ezcp.posts.edit', ['post' => 1]),
            route('ezcp.pages.index'),
            route('ezcp.pages.create'),
            route('ezcp.pages.show', ['page' => 1]),
            route('ezcp.pages.edit', ['page' => 1]),
            route('ezcp.categories.index'),
            route('ezcp.categories.create'),
            route('ezcp.categories.show', ['category' => 1]),
            route('ezcp.categories.edit', ['category' => 1]),
            route('ezcp.menus.index'),
            route('ezcp.menus.create'),
            route('ezcp.menus.show', ['menu' => 1]),
            route('ezcp.menus.edit', ['menu' => 1]),
            route('ezcp.database.index'),
            route('ezcp.bread.edit', ['table' => 'categories']),
            route('ezcp.database.edit', ['table' => 'categories']),
            route('ezcp.database.create'),
        ];

        foreach ($urls as $url) {
            $response = $this->call('GET', $url);
            $this->assertEquals(200, $response->status(), $url.' did not return a 200');
        }
    }
}
