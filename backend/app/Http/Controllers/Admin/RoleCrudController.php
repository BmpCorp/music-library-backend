<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Enums\PermissionCode;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\PermissionManager\app\Http\Requests\RoleStoreCrudRequest as StoreRequest;
use Backpack\PermissionManager\app\Http\Requests\RoleUpdateCrudRequest as UpdateRequest;
use Backpack\PermissionManager\app\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class RoleCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class RoleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        $this->crud->setModel(Role::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/role');
        $this->crud->setEntityNameStrings('роль', 'Роли');

        if (!backpack_user()->canAny([PermissionCode::FULL_ACCESS, PermissionCode::ROLES])) {
            CRUD::denyAllAccess();
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    public function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'name',
            'label' => trans('backpack::permissionmanager.name'),
            'type' => 'text',
        ]);

        /**
         * Show a column with the number of users that have that particular role.
         *
         * Note: To account for the fact that there can be thousands or millions
         * of users for a role, we did not use the `relationship_count` column,
         * but instead opted to append a fake `user_count` column to
         * the result, using Laravel's `withCount()` method.
         * That way, no users are loaded.
         */
        $this->crud->query->withCount('users');
        $this->crud->addColumn([
            'label' => 'Пользователей',
            'type' => 'text',
            'name' => 'users_count',
            'wrapper' => [
                'href' => function ($crud, $column, $entry) {
                    return backpack_url('user?role_id=' . $entry->getKey());
                },
            ],
        ]);

        /**
         * Show the exact permissions that role has.
         */
        $this->crud->addColumn([
            // n-n relationship (with pivot table)
            'label' => mb_ucfirst(trans('backpack::permissionmanager.permission_plural')),
            'type' => 'select_multiple',
            'name' => 'permissions', // the method that defines the relationship in your Model
            'entity' => 'permissions', // the method that defines the relationship in your Model
            'attribute' => 'admin_name', // foreign key attribute that is shown to user
            'model' => config('permission.models.permission'), // foreign key model
            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
        ]);
    }

    public function setupCreateOperation()
    {
        $this->setupFields();
        $this->crud->setValidation(StoreRequest::class);

        //otherwise, changes won't have effect
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function setupUpdateOperation()
    {
        $this->setupFields();
        $this->crud->setValidation(UpdateRequest::class);

        //otherwise, changes won't have effect
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function setupFields(): void
    {
        $this->crud->addField([
            'name' => 'name',
            'label' => trans('backpack::permissionmanager.name'),
            'type' => 'text',
        ]);

        if (config('backpack.permissionmanager.multiple_guards')) {
            $this->crud->addField([
                'name' => 'guard_name',
                'label' => trans('backpack::permissionmanager.guard_type'),
                'type' => 'select_from_array',
                'options' => $this->getGuardTypes(),
            ]);
        }

        $this->crud->addField([
            'label' => mb_ucfirst(trans('backpack::permissionmanager.permission_plural')),
            'type' => 'checklist',
            'name' => 'permissions',
            'entity' => 'permissions',
            'attribute' => 'admin_name',
            'model' => config('permission.models.permission'),
            'pivot' => true,
        ]);
    }
}
