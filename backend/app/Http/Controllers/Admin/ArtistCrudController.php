<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionCode;
use App\Models\Artist;
use App\Models\Country;
use App\Utilities\AdminField;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ArtistCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ArtistCrudController extends CrudController
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
        $this->crud->setModel(Artist::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/artist');
        $this->crud->setEntityNameStrings('запись', 'Исполнители');

        if (!backpack_user()->canAny([PermissionCode::FULL_ACCESS, PermissionCode::CONTENT])) {
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
        $this->crud->column([
            'name' => Artist::ID,
            'type' => 'number',
            'label' => 'ID',
            'thousands_sep' => '',
        ]);

        $this->crud->column([
            'name' => Artist::TITLE,
            'type' => 'text',
            'label' => 'Название',
        ]);

        $this->crud->column([
            'name' => 'country.' . Country::NAME,
            'type' => 'text',
            'label' => 'Страна',
        ]);

        $this->crud->column([
            'name' => Artist::GENRES,
            'type' => 'text',
            'label' => 'Жанры',
        ]);

        $this->crud->query->withCount('albums');
        $this->crud->column([
            'label' => 'Альбомов',
            'type' => 'number',
            'name' => 'albums_count',
            'wrapper' => backpack_pro() ? [
                'href' => function ($crud, $column, $entry) {
                    return backpack_url('album?artist_id=' . $entry->getKey());
                },
            ] : [],
        ]);

        $this->crud->column([
            'name' => Artist::CREATED_AT,
            'type' => 'date',
            'label' => 'Дата добавления',
            'format' => 'DD.MM.YYYY HH:mm'
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
            'name' => Artist::TITLE,
            'type' => 'text',
            'label' => 'Название',
            'attributes' => AdminField::INPUT_MAX_LENGTH_255,
        ]);

        $this->crud->addField([
            'name' => Artist::COUNTRY_ID,
            'label' => 'Страна',
            'type' => 'select',
            'entity' => 'country',
            'model' => Country::class,
            'attribute' => Country::NAME,
            'options' => fn (Builder $query) => $query->orderBy(Country::NAME)->get(),
        ]);

        $this->crud->addField([
            'name' => Artist::DESCRIPTION,
            'type' => 'textarea',
            'label' => 'Описание',
            'attributes' => AdminField::TEXTAREA_ROWS_3,
        ]);

        $this->crud->addField([
            'name' => Artist::GENRES,
            'type' => 'text',
            'label' => 'Жанры',
        ]);
    }

    private function setupValidation(): void
    {
        $this->crud->setValidation([
            Artist::TITLE => 'required|string|max:255',
            Artist::COUNTRY_ID => 'nullable|exists:countries,id',
            Artist::DESCRIPTION => 'nullable|string',
            Artist::GENRES => 'nullable|string',
        ], [
            Artist::TITLE . '.required' => 'Не заполнено название.',
            Artist::TITLE . '.max' => 'Слишком длинное название (не более :max символов).',
            Artist::COUNTRY_ID . '.exists' => 'Выбранная страна не существует.',
        ]);
    }
}
