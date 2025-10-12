@php use App\Enums\PermissionCode; @endphp
{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i
            class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@canany([PermissionCode::FULL_ACCESS, PermissionCode::USERS, PermissionCode::ROLES])
    <x-backpack::menu-dropdown title="Пользователи" icon="la la-user-friends">
        @canany([PermissionCode::FULL_ACCESS, PermissionCode::USERS])
            <x-backpack::menu-dropdown-item title="Пользователи" icon="la la-user" :link="backpack_url('user')"/>
        @endcanany
        @canany([PermissionCode::FULL_ACCESS, PermissionCode::ROLES])
            <x-backpack::menu-dropdown-item title="Роли" icon="la la-users-cog" :link="backpack_url('role')"/>
        @endcanany
    </x-backpack::menu-dropdown>
@endcanany
@canany([PermissionCode::FULL_ACCESS, PermissionCode::DICTIONARIES])
    <x-backpack::menu-dropdown title="Справочники" icon="la la-book">
        <x-backpack::menu-dropdown-item title="Страны" icon="la la-globe" :link="backpack_url('country')" />
    </x-backpack::menu-dropdown>
@endcanany
@canany([PermissionCode::FULL_ACCESS, PermissionCode::CONTENT])
    <x-backpack::menu-dropdown title="Контент" icon="la la-music">
        <x-backpack::menu-dropdown-item title="Исполнители" icon="la la-guitar" :link="backpack_url('artist')" />
        <x-backpack::menu-dropdown-item title="Альбомы" icon="la la-record-vinyl" :link="backpack_url('album')" />
    </x-backpack::menu-dropdown>
@endcanany
@canany([PermissionCode::FULL_ACCESS, PermissionCode::FEEDBACK])
    <x-backpack::menu-dropdown title="Обратная связь" icon="la la-envelope">
        <x-backpack::menu-dropdown-item title="Любимые исполнители" icon="la la-headphones" :link="backpack_url('user-favorite-artist')" />
    </x-backpack::menu-dropdown>
@endcanany
@canany([PermissionCode::FULL_ACCESS, PermissionCode::MAINTENANCE])
    <x-backpack::menu-dropdown title="Обслуживание" icon="la la-screwdriver">
        <x-backpack::menu-dropdown-item title="phpinfo" icon="la la-info-circle" :link="backpack_url('metrics/php')" />
        <x-backpack::menu-dropdown-item title="php-fpm" icon="la la-server" :link="backpack_url('metrics/php-fpm')" />
        <x-backpack::menu-dropdown-item title="MySQL" icon="la la-database" :link="backpack_url('metrics/mysql')" />
        @if(config('telescope.enabled'))
            <x-backpack::menu-dropdown-item title="Telescope" icon="la la-moon" :link="backpack_url('metrics/telescope')" />
        @else
            <x-backpack::menu-dropdown-item title="Telescope" icon="la la-moon" class="text-muted" />
        @endif
        <x-backpack::menu-dropdown-item title="Horizon" icon="la la-sun" :link="backpack_url('metrics/horizon')" />
        <x-backpack::menu-dropdown-item title="Документация API" icon="la la-code" :link="url('api/docs')" />
        <x-backpack::menu-dropdown-item title="Поисковый индекс" icon="la la-search" :link="config('scout.meilisearch.external_host')" />
        <x-backpack::menu-dropdown-item title="RabbitMQ" icon="la la-business-time" :link="config('queue.connections.rabbitmq.management_url')" />
    </x-backpack::menu-dropdown>
@endcanany
