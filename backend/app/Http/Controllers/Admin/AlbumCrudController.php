<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionCode;
use App\Events\AlbumCreated;
use App\Events\AlbumUpdated;
use App\Models\Album;
use App\Models\Artist;
use App\Services\LibraryService;
use App\Utilities\AdminField;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AlbumCrudController
 * @property-read CrudPanel $crud
 */
class AlbumCrudController extends CrudController
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
            'label' => trans('columns.album-title'),
        ]);

        $this->crud->column([
            'name' => 'artist.' . Artist::TITLE,
            'type' => 'text',
            'label' => trans('columns.album-artist'),
            'searchLogic' => function (Builder $query, $column, $searchTerm) {
                $query->orWhereRelation('artist', Artist::TITLE, 'LIKE', "%{$searchTerm}%");
            },
        ]);

        $this->crud->column([
            'name' => Album::YEAR,
            'type' => 'number',
            'label' => trans('columns.album-year'),
            'thousands_sep' => '',
        ]);

        $this->crud->column([
            'name' => Album::COVER,
            'type' => 'image',
            'label' => trans('columns.album-cover'),
        ]);

        $this->crud->column([
            'name' => Album::HAS_EXPLICIT_LYRICS,
            'type' => 'boolean',
            'label' => trans('columns.album-has-explicit-lyrics'),
            'options' => [
                0 => 'Нет',
                1 => 'Да',
            ],
        ]);

        $this->crud->column([
            'name' => Album::CREATED_AT,
            'type' => 'date',
            'label' => trans('columns.created-at'),
            'format' => 'DD.MM.YYYY HH:mm',
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
            'label' => trans('fields.album-title'),
            'attributes' => AdminField::INPUT_MAX_LENGTH_255,
        ]);

        $this->crud->addField([
            'name' => Album::ARTIST_ID,
            'label' => trans('fields.album-artist'),
            'type' => 'select',
            'entity' => 'artist',
            'model' => Artist::class,
            'attribute' => Artist::TITLE,
            'options' => fn (Builder $query) => $query->orderBy(Artist::TITLE)->get(),
        ]);

        $this->crud->addField([
            'name' => Album::YEAR,
            'type' => 'number',
            'label' => trans('fields.album-year'),
            'attributes' => [
                'min' => 1900,
                'max' => date('Y'),
            ],
            'wrapper' => AdminField::WRAPPER_QUARTER,
        ]);

        $this->crud->addField([
            'name' => Album::SONG_COUNT,
            'type' => 'number',
            'label' => trans('fields.album-song-count'),
            'attributes' => [
                'min' => 0,
            ],
            'wrapper' => AdminField::WRAPPER_QUARTER,
        ]);

        $this->crud->addField([
            'name' => Album::HAS_EXPLICIT_LYRICS,
            'type' => 'checkbox',
            'label' => trans('fields.album-has-explicit-lyrics'),
            'wrapper' => AdminField::WRAPPER_HALF,
        ]);

        $this->crud->addField([
            'name' => Album::DESCRIPTION,
            'type' => 'textarea',
            'label' => trans('fields.album-description'),
            'attributes' => AdminField::TEXTAREA_ROWS_3,
        ]);

        $this->crud->addField([
            'name' => Album::GENRES,
            'type' => 'text',
            'label' => trans('fields.album-genres'),
        ]);

        $this->crud->addField([
            'name' => Album::COVER,
            'type' => 'upload',
            'label' => trans('fields.album-cover'),
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
            Album::TITLE . '.required' => trans('validation.artist_title_required'),
            Album::TITLE . '.max' => trans('validation.artist_title_max'),
            Album::ARTIST_ID . '.required' => trans('validation.album_artist_id_required'),
            Album::ARTIST_ID . '.exists' => trans('validation.album_artist_id_exists'),
            Album::YEAR . '.integer' => trans('validation.album_year_integer'),
            Album::YEAR . '.min' => trans('validation.album_year_min'),
            Album::YEAR . '.max' => trans('validation.album_year_max'),
            Album::SONG_COUNT . '.integer' => trans('validation.album_song_count_integer'),
            Album::SONG_COUNT . '.min' => trans('validation.album_song_count_min'),
        ]);
    }

    public function store()
    {
        $this->crud->hasAccessOrFail('create');
        $request = $this->crud->validateRequest();
        $this->crud->registerFieldEvents();

        /** @var Album $item */
        $item = $this->crud->create($this->crud->getStrippedSaveRequest($request));
        $this->data['entry'] = $this->crud->entry = $item;

        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        $this->crud->setSaveAction();
        event(new AlbumCreated($item->artist_id, $item->id));

        return $this->crud->performSaveAction($item->getKey());
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');
        $request = $this->crud->validateRequest();
        $this->crud->registerFieldEvents();

        /** @var Album $item */
        $item = $this->crud->update(
            $request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest($request),
        );
        $this->data['entry'] = $this->crud->entry = $item;

        \Alert::success(trans('backpack::crud.update_success'))->flash();

        $this->crud->setSaveAction();
        event(new AlbumUpdated($item->artist_id, $item->id));

        return $this->crud->performSaveAction($item->getKey());
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        $id = $this->crud->getCurrentEntryId() ?? $id;

        return (new LibraryService())->deleteAlbum($id);
    }

}
