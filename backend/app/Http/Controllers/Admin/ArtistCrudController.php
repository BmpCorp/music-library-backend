<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionCode;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Country;
use App\Services\LibraryService;
use App\Utilities\AdminField;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ArtistCrudController
 * @property-read CrudPanel $crud
 */
class ArtistCrudController extends CrudController
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
            'label' => trans('columns.artist-title'),
        ]);

        $this->crud->column([
            'name' => 'country.' . Country::NAME,
            'type' => 'text',
            'label' => trans('columns.artist-country'),
        ]);

        $this->crud->column([
            'name' => Artist::LOGO,
            'type' => 'image',
            'label' => trans('columns.artist-logo'),
        ]);

        $this->crud->column([
            'name' => Artist::GENRES,
            'type' => 'text',
            'label' => trans('columns.artist-genres'),
        ]);

        $this->crud->query->withCount('albums');
        $this->crud->column([
            'label' => trans('columns.artist-albums-count'),
            'type' => 'number',
            'name' => 'albums_count',
            'wrapper' => backpack_pro() ? [
                'href' => function ($crud, $column, $entry) {
                    return backpack_url('album?artist_id=' . $entry->getKey());
                },
            ] : [],
        ]);

        $this->crud->column([
            'label' => trans('columns.artist-total-song-count'),
            'type' => 'number',
            'name' => Artist::TOTAL_SONG_COUNT,
        ]);

        $this->crud->query->withCount('favoriteOfUsers');
        $this->crud->column([
            'label' => trans('columns.artist-favorite-of-users-count'),
            'type' => 'number',
            'name' => 'favorite_of_users_count',
            'wrapper' => backpack_pro() && backpack_user()->canAny([PermissionCode::FULL_ACCESS, PermissionCode::FEEDBACK]) ? [
                'href' => function ($crud, $column, $entry) {
                    return backpack_url('user-favorite-artist?artist_id=' . $entry->getKey());
                },
            ] : [],
        ]);

        $this->crud->column([
            'name' => Artist::CREATED_AT,
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
            'name' => Artist::TITLE,
            'type' => 'text',
            'label' => trans('fields.artist-title'),
            'attributes' => AdminField::INPUT_MAX_LENGTH_255,
        ]);

        $this->crud->addField([
            'name' => Artist::COUNTRY_ID,
            'type' => 'select',
            'label' => trans('fields.artist-country'),
            'entity' => 'country',
            'model' => Country::class,
            'attribute' => Country::NAME,
            'options' => fn (Builder $query) => $query->orderBy(Country::NAME)->get(),
        ]);

        $this->crud->addField([
            'name' => Artist::DESCRIPTION,
            'type' => 'textarea',
            'label' => trans('fields.artist-description'),
            'attributes' => AdminField::TEXTAREA_ROWS_3,
        ]);

        $this->crud->addField([
            'name' => Artist::GENRES,
            'type' => 'text',
            'label' => trans('fields.artist-genres'),
        ]);

        $this->crud->addField([
            'name' => Artist::LOGO,
            'type' => 'upload',
            'label' => trans('fields.artist-logo'),
            'upload' => true,
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
            Artist::TITLE . '.required' => trans('validation.artist_title_required'),
            Artist::TITLE . '.max' => trans('validation.artist_title_max'),
            Artist::COUNTRY_ID . '.exists' => trans('validation.artist_country_exists'),
        ]);
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        $id = $this->crud->getCurrentEntryId() ?? $id;

        if (Album::whereArtistId($id)->exists()) {
            \Alert::error(trans('validation.artist_has_albums_error'))->flash();
            return false;
        }

        return (new LibraryService())->deleteArtist($id);
    }
}
