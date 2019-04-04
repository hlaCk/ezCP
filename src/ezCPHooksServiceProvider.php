<?php

namespace hlaCk\ezCP;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use hlaCk\ezCP\Events\Setup;
use hlaCk\ezCP\HooksServiceProvider;
use hlaCk\ezCP\Models\Menu;
use hlaCk\ezCP\Models\MenuItem;
use hlaCk\ezCP\Models\Permission;

class ezCPHooksServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $configPath = dirname(__DIR__).'/publishable/config/ezcp-hooks.php';

        $this->mergeConfigFrom($configPath, 'ezcp-hooks');

        // Register the HooksServiceProvider
        $this->app->register(HooksServiceProvider::class);

        if (!$this->enabled()) {
            return;
        }

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [$configPath => config_path('ezcp-hooks.php')],
                'ezcp-hooks-config'
            );
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'ezcp-hooks');
    }

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function boot(Dispatcher $events)
    {
        if (!$this->enabled()) {
            return;
        }

        if (config('ezcp-hooks.add-route', true)) {
            $events->listen('ezcp.admin.routing', [$this, 'addHookRoute']);
        }

        if (config('ezcp-hooks.add-hook-menu-item', true)) {
            $events->listen(Setup::class, [$this, 'addHookMenuItem']);
        }

        if (config('ezcp-hooks.add-hook-permissions', true)) {
            $events->listen(Setup::class, [$this, 'addHookPermissions']);
        }

        if (config('ezcp-hooks.publish-vendor-files', true)) {
            $events->listen(Setup::class, [$this, 'publishVendorFiles']);
        }
    }

    public function addHookRoute($router)
    {
        $namespacePrefix = '\\hlaCk\\ezCP\\Http\\Controllers\\';

        $router->get('hooks', ['uses' => $namespacePrefix.'HooksController@index', 'as' => 'hooks']);
        $router->get('hooks/{name}/enable', ['uses' => $namespacePrefix.'HooksController@enable', 'as' => 'hooks.enable']);
        $router->get('hooks/{name}/disable', ['uses' => $namespacePrefix.'HooksController@disable', 'as' => 'hooks.disable']);
        $router->get('hooks/{name}/update', ['uses' => $namespacePrefix.'HooksController@update', 'as' => 'hooks.update']);
        $router->post('hooks', ['uses' => $namespacePrefix.'HooksController@install', 'as' => 'hooks.install']);
        $router->delete('hooks/{name}', ['uses' => $namespacePrefix.'HooksController@uninstall', 'as' => 'hooks.uninstall']);
    }

    public function addHookMenuItem()
    {
        $menu = Menu::where('name', 'admin')->first();

        if (is_null($menu)) {
            return;
        }

        $parentId = null;

        $toolsMenuItem = MenuItem::where('menu_id', $menu->id)
            ->where('title', 'Tools')
            ->first();

        if ($toolsMenuItem) {
            $parentId = $toolsMenuItem->id;
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title'   => 'Hooks',
            'url'     => '',
            'route'   => 'ezcp.hooks',
        ]);

        if (!$menuItem->exists) {
            $menuItem->fill([
                'target'     => '_self',
                'icon_class' => 'ezcp-hook',
                'color'      => null,
                'parent_id'  => $parentId,
                'order'      => 13,
            ])->save();
        }
    }

    public function addHookPermissions()
    {
        Permission::firstOrCreate([
            'key'        => 'browse_hooks',
            'table_name' => null,
        ]);
    }

    public function publishVendorFiles()
    {
        Artisan::call('vendor:publish', ['--provider' => static::class]);
    }

    public function enabled()
    {
        if (config('ezcp-hooks.enabled', true)) {
            return config('hooks.enabled', true);
        }

        return config('ezcp-hooks.enabled', true);
    }
}
