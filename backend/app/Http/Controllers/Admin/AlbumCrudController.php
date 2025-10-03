<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionCode;
use App\Models\Album;
use App\Models\Artist;
use App\Utilities\AdminField;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AlbumCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class AlbumCrudController extends CrudController
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
        $this->crud->setModel(Album::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/album');
        $this->crud->setEntityNameStrings('альбом', 'Альбомы');

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
            'name' => Album::ID,
            'type' => 'number',
            'label' => 'ID',
            'thousands_sep' => '',
        ]);

        $this->crud->column([
            'name' => Album::TITLE,
            'type' => 'text',
            'label' => 'Название',
        ]);

        $this->crud->column([
            'name' => 'artist.' . Artist::TITLE,
            'type' => 'text',
            'label' => 'Исполнитель',
            'searchLogic' => function (Builder $query, $column, $searchTerm) {
                $query->orWhereRelation('artist', Artist::TITLE, 'LIKE', "%{$searchTerm}%");
            },
        ]);

        $this->crud->column([
            'name' => Album::YEAR,
            'type' => 'number',
            'label' => 'Год',
            'thousands_sep' => '',
        ]);

        $this->crud->column([
            'name' => Album::COVER,
            'type' => 'image',
            'label' => 'Обложка',
        ]);

        $this->crud->column([
            'name' => Album::HAS_EXPLICIT_LYRICS,
            'type' => 'boolean',
            'label' => 'Explicit',
            'options' => [
                0 => 'Нет',
                1 => 'Да',
            ],
        ]);

        $this->crud->column([
            'name' => Album::CREATED_AT,
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
            'name' => Album::TITLE,
            'type' => 'text',
            'label' => 'Название',
            'attributes' => AdminField::INPUT_MAX_LENGTH_255,
        ]);

        $this->crud->addField([
            'name' => Album::ARTIST_ID,
            'label' => 'Исполнитель',
            'type' => 'select',
            'entity' => 'artist',
            'model' => Artist::class,
            'attribute' => Artist::TITLE,
            'options' => fn (Builder $query) => $query->orderBy(Artist::TITLE)->get(),
        ]);

        $this->crud->addField([
            'name' => Album::YEAR,
            'type' => 'number',
            'label' => 'Год',
            'attributes' => [
                'min' => 1900,
                'max' => date('Y'),
            ],
            'wrapper' => AdminField::WRAPPER_QUARTER,
        ]);

        $this->crud->addField([
            'name' => Album::SONG_COUNT,
            'type' => 'number',
            'label' => 'Кол-во песен',
            'attributes' => [
                'min' => 0,
            ],
            'wrapper' => AdminField::WRAPPER_QUARTER,
        ]);

        $this->crud->addField([
            'name' => Album::HAS_EXPLICIT_LYRICS,
            'type' => 'checkbox',
            'label' => 'Explicit',
            'wrapper' => AdminField::WRAPPER_HALF,
        ]);

        $this->crud->addField([
            'name' => Album::DESCRIPTION,
            'type' => 'textarea',
            'label' => 'Описание',
            'attributes' => AdminField::TEXTAREA_ROWS_3,
        ]);

        $this->crud->addField([
            'name' => Album::GENRES,
            'type' => 'text',
            'label' => 'Жанры',
        ]);

        $this->crud->addField([
            'name' => Album::COVER,
            'type' => 'upload',
            'label' => 'Обложка',
            'upload' => true,
        ]);
    }

    private function setupValidation(): void
    {
        $this->crud->setValidation([
            Album::TITLE => 'required|string|max:255',
            Album::ARTIST_ID => 'required|exists:artists,id',
            Album::YEAR => 'nullable|integer|min:1900|max:' . date('Y'),
            Album::SONG_COUNT => 'nullable|integer|min:0',
            Album::HAS_EXPLICIT_LYRICS => 'boolean',
            Album::DESCRIPTION => 'nullable|string',
            Album::GENRES => 'nullable|string',
        ], [
            Album::TITLE . '.required' => 'Не заполнено название.',
            Album::TITLE . '.max' => 'Слишком длинное название (не более :max символов).',
            Album::ARTIST_ID . '.required' => 'Не выбран исполнитель.',
            Album::ARTIST_ID . '.exists' => 'Выбранный исполнитель не существует.',
            Album::YEAR . '.integer' => 'Год должен быть целым числом.',
            Album::YEAR . '.min' => 'Год не может быть ранее :min.',
            Album::YEAR . '.max' => 'Год не может быть позднее :max.',
            Album::SONG_COUNT . '.integer' => 'Количество песен должно быть целым числом.',
            Album::SONG_COUNT . '.min' => 'Количество песен не может быть отрицательным.',
        ]);
    }
}
