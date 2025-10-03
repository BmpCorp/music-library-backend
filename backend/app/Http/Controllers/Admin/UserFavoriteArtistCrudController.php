<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionCode;
use App\Models\Album;
use App\Models\Artist;
use App\Models\User;
use App\Models\UserFavoriteArtist;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserFavoriteArtistCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class UserFavoriteArtistCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        $this->crud->setModel(UserFavoriteArtist::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/user-favorite-artist');
        $this->crud->setEntityNameStrings('запись', 'Любимые исполнители');

        if (!backpack_user()->canAny([PermissionCode::FULL_ACCESS, PermissionCode::FEEDBACK])) {
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
        $this->setupColumns();
    }

    protected function setupShowOperation()
    {
        $this->setupColumns();
    }

    private function setupColumns(): void
    {
        $this->crud->addColumn([
            'name' => UserFavoriteArtist::CREATED_AT,
            'type' => 'datetime',
            'label' => 'Дата добавления',
            'format' => 'DD.MM.YYYY HH:mm',
            'visibleInTable' => false,
            'visibleInModal' => false,
            'visibleInShow' => true,
        ]);

        $this->crud->addColumn([
            'name' => UserFavoriteArtist::UPDATED_AT,
            'type' => 'datetime',
            'label' => 'Дата обновления',
            'format' => 'DD.MM.YYYY HH:mm',
            'visibleInTable' => false,
            'visibleInModal' => false,
            'visibleInShow' => true,
        ]);

        $this->crud->addColumn([
            'name' => 'user.' . User::NAME,
            'type' => 'text',
            'label' => 'Пользователь',
        ]);

        $this->crud->addColumn([
            'name' => 'artist.' . Artist::TITLE,
            'type' => 'text',
            'label' => 'Исполнитель',
        ]);

        $this->crud->addColumn([
            'name' => 'lastCheckedAlbum.' . Album::TITLE,
            'type' => 'text',
            'label' => 'Последний альбом',
        ]);

        $this->crud->addColumn([
            'name' => UserFavoriteArtist::LISTENING_NOW,
            'type' => 'check',
            'label' => 'Слушает сейчас',
        ]);
    }
}
