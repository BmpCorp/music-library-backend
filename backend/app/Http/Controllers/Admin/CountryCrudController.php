<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionCode;
use App\Models\Country;
use App\Utilities\AdminField;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CountryCrudController
 * @property-read CrudPanel $crud
 */
class CountryCrudController extends CrudController
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
        CRUD::setModel(Country::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/country');
        CRUD::setEntityNameStrings('запись', 'Страны');

        if (!backpack_user()->canAny([PermissionCode::FULL_ACCESS, PermissionCode::DICTIONARIES])) {
            $this->crud->denyAllAccess();
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => Country::CODE,
            'type' => 'text',
            'label' => trans('columns.country-code'),
        ]);

        $this->crud->addColumn([
            'name' => Country::NAME,
            'type' => 'text',
            'label' => trans('columns.country-name'),
        ]);

        $this->crud->query->withCount('artists');
        $this->crud->addColumn([
            'name' => 'artists_count',
            'type' => 'number',
            'label' => trans('columns.country-artists-count'),
            'wrapper' => backpack_pro() ? [
                'href' => function ($crud, $column, $entry) {
                    return backpack_url('artist?country_id=' . $entry->getKey());
                },
            ] : [],
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->setupFields();
        $this->setupValidation();
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupFields();
        $this->setupValidation();
    }

    private function setupFields(): void
    {
        $this->crud->addField([
            'name' => Country::CODE,
            'type' => 'text',
            'label' => trans('fields.country-code'),
            'attributes' => [
                'maxlength' => 2,
                'pattern' => '[A-Z]{2}',
            ],
            'wrapper' => AdminField::WRAPPER_QUARTER,
            'hint' => trans('fields.country-code-hint'),
        ]);

        $this->crud->addField([
            'name' => Country::NAME,
            'type' => 'text',
            'label' => trans('fields.country-name'),
            'attributes' => AdminField::INPUT_MAX_LENGTH_255,
            'wrapper' => AdminField::WRAPPER_THREE_QUARTERS,
        ]);
    }

    private function setupValidation(): void
    {
        $id = $this->crud->getCurrentEntryId() ?? 'NULL';
        $this->crud->setValidation([
            Country::CODE => 'required|string|size:2|unique:countries,' . Country::CODE . ",{$id},id,deleted_at,NULL",
            Country::NAME => 'required|string|max:255',
        ], [
            Country::CODE . '.required' => trans('validation.country_code_required'),
            Country::CODE . '.size' => trans('validation.country_code_size'),
            Country::CODE . '.unique' => trans('validation.country_code_unique'),
            Country::NAME . '.required' => trans('validation.artist_title_required'),
            Country::NAME . '.max' => trans('validation.artist_title_max'),
        ]);
    }
}
