<?php

use Illuminate\Database\Seeder;
use hlaCk\ezCP\Models\DataType;

class DataTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $dataType = $this->dataType('slug', 'users');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'users',
                'display_name_singular' => __('ezcp::seeders.data_types.user.singular'),
                'display_name_plural'   => __('ezcp::seeders.data_types.user.plural'),
                'icon'                  => 'ezcp-person',
                'model_name'            => 'hlaCk\\ezCP\\Models\\User',
                'policy_name'           => 'hlaCk\\ezCP\\Policies\\UserPolicy',
                'controller'            => 'hlaCk\\ezCP\\Http\\Controllers\\ezCPUserController',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }

        $dataType = $this->dataType('slug', 'menus');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'menus',
                'display_name_singular' => __('ezcp::seeders.data_types.menu.singular'),
                'display_name_plural'   => __('ezcp::seeders.data_types.menu.plural'),
                'icon'                  => 'ezcp-list',
                'model_name'            => 'hlaCk\\ezCP\\Models\\Menu',
                'controller'            => '',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }

        $dataType = $this->dataType('slug', 'roles');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'roles',
                'display_name_singular' => __('ezcp::seeders.data_types.role.singular'),
                'display_name_plural'   => __('ezcp::seeders.data_types.role.plural'),
                'icon'                  => 'ezcp-lock',
                'model_name'            => 'hlaCk\\ezCP\\Models\\Role',
                'controller'            => '',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }
    }

    /**
     * [dataType description].
     *
     * @param [type] $field [description]
     * @param [type] $for   [description]
     *
     * @return [type] [description]
     */
    protected function dataType($field, $for)
    {
        return DataType::firstOrNew([$field => $for]);
    }
}
