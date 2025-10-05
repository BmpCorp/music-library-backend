<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionCode;
use App\Models\User;
use App\Utilities\AdminField;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;

/**
 * Class UserCrudController
 * @property-read CrudPanel $crud
 */
class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        $this->crud->setModel(User::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/user');
        $this->crud->setEntityNameStrings('запись', 'Пользователи');

        if (!backpack_user()->canAny([PermissionCode::FULL_ACCESS, PermissionCode::USERS])) {
            $this->crud->denyAllAccess();
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
            'name' => User::ID,
            'type' => 'number',
            'label' => 'ID',
        ]);

        $this->crud->addColumn([
            'name' => User::NAME,
            'type' => 'text',
            'label' => 'Имя',
        ]);

        $this->crud->addColumn([
            'name' => User::EMAIL,
            'type' => 'email',
            'label' => 'Email',
        ]);

        $this->crud->addColumn([
            'name' => User::CREATED_AT,
            'type' => 'date',
            'label' => 'Дата регистрации',
            'format' => 'DD.MM.YYYY HH:mm',
        ]);

        $this->crud->addColumn([
            'name' => 'role_name',
            'type' => 'text',
            'label' => 'Роль',
            'value' => function (User $entry) {
                $str = '';
                $role = $entry->roles()->first();

                if ($role) {
                    $str = $role->name;
                    $roleCount = $entry->roles()->count();

                    if ($roleCount > 1) {
                        $str .= ' +' . ($roleCount - 1);
                    }
                }

                return $str;
            },
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    public function setupCreateOperation()
    {
        $this->setupValidation();
        $this->setupFields();
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    public function setupUpdateOperation()
    {
        $this->setupValidation();
        $this->setupFields();
    }

    private function setupFields(): void
    {
        $this->crud->addField([
            'name' => User::NAME,
            'type' => 'text',
            'label' => 'Имя',
            'attributes' => AdminField::INPUT_MAX_LENGTH_255,
            'wrapper' => AdminField::WRAPPER_HALF,
        ]);

        $this->crud->addField([
            'name' => User::EMAIL,
            'type' => 'email',
            'label' => 'Email',
            'attributes' => AdminField::INPUT_MAX_LENGTH_255,
            'wrapper' => AdminField::WRAPPER_HALF,
        ]);

        // Both roles and permissions checklist
        $this->crud->addField([
            'name' => 'roles,permissions',
            'type' => 'checklist_dependency',
            'label' => trans('backpack::permissionmanager.user_role_permission'),
            'field_unique_name' => 'user_role_permission',
            'subfields' => [
                'primary' => [
                    'label' => trans('backpack::permissionmanager.roles'),
                    'name' => 'roles',
                    'entity' => 'roles', // the method that defines the relationship in your Model
                    'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                    'attribute' => 'name', // foreign key attribute that is shown to user
                    'model' => config('permission.models.role'), // foreign key model
                    'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                    'number_columns' => 3, // can be 1,2,3,4,6
                ],
                'secondary' => [
                    'label' => mb_ucfirst(trans('backpack::permissionmanager.permission_plural')),
                    'name' => 'permissions',
                    'entity' => 'permissions', // the method that defines the relationship in your Model
                    'entity_primary' => 'roles', // the method that defines the relationship in your Model
                    'attribute' => 'admin_name', // foreign key attribute that is shown to user
                    'model' => config('permission.models.permission'), // foreign key model
                    'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                    'number_columns' => 3, // can be 1,2,3,4,6
                ],
            ],
        ]);

        $inEditingMode = !empty($this->crud->getCurrentEntryId());
        $this->crud->addField([
            'name' => User::PLAIN_PASSWORD,
            'type' => 'password',
            'label' => 'Пароль',
            'attributes' => AdminField::INPUT_MAX_LENGTH_255,
            'wrapper' => AdminField::WRAPPER_HALF,
            'hint' => $inEditingMode ? 'Оставьте поле пустым, чтобы не менять пароль' : null,
        ]);

        $this->crud->addField([
            'name' => 'password_confirm',
            'type' => 'password',
            'label' => 'Повторите пароль',
            'attributes' => AdminField::INPUT_MAX_LENGTH_255,
            'wrapper' => AdminField::WRAPPER_HALF,
        ]);
    }

    private function setupValidation(): void
    {
        $id = $this->crud->getCurrentEntryId();
        if (!$id) {
            // create mode
            $rules = [
                User::NAME => 'required|string|max:255',
                User::EMAIL => 'required|email:rfc|max:255|unique:users,' . User::EMAIL . ',NULL,id,deleted_at,NULL',
                User::PLAIN_PASSWORD => 'required|min:3',
                'password_confirm' => 'required|same:' . User::PLAIN_PASSWORD,
            ];
        } else {
            // edit mode
            $rules = [
                User::NAME => 'required|string|max:255',
                User::EMAIL => 'required|email:rfc|max:255|unique:users,' . User::EMAIL . ",{$id},id,deleted_at,NULL",
                User::PLAIN_PASSWORD => 'nullable|min:3',
                'password_confirm' => 'nullable|same:' . User::PLAIN_PASSWORD,
            ];
        }

        $this->crud->setValidation($rules, [
            User::NAME . '.required' => 'Не заполнено имя.',
            User::NAME . '.max' => 'Слишком длинный текст в поле имени (не более :max символов).',
            User::EMAIL . '.required' => 'Не заполнен Email',
            User::EMAIL . '.email' => 'Введённый Email некорректен.',
            User::EMAIL . '.max' => 'Слишком длинный текст в поле Email (не более :max символов).',
            User::EMAIL . '.unique' => 'Такой Email уже существует.',
            User::PLAIN_PASSWORD . '.required' => 'Не заполнен пароль.',
            User::PLAIN_PASSWORD . '.min' => 'Слишком короткий пароль (минимум :min символов).',
            'password_confirm.required' => 'Повторите пароль.',
            'password_confirm.same' => 'Пароли не совпадают.',
        ]);
    }
}
