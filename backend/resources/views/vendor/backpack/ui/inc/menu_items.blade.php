@php use App\Enums\PermissionCode; @endphp
{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i
            class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@canany([PermissionCode::FULL_ACCESS, PermissionCode::USERS, PermissionCode::ROLES])
    <x-backpack::menu-dropdown :title="trans('menu.users_and_roles')" icon="la la-user-friends">
        @canany([PermissionCode::FULL_ACCESS, PermissionCode::USERS])
            <x-backpack::menu-dropdown-item :title="trans('menu.users')" icon="la la-user" :link="backpack_url('user')"/>
        @endcanany
        @canany([PermissionCode::FULL_ACCESS, PermissionCode::ROLES])
            <x-backpack::menu-dropdown-item :title="trans('menu.roles')" icon="la la-users-cog" :link="backpack_url('role')"/>
        @endcanany
    </x-backpack::menu-dropdown>
@endcanany
@canany([PermissionCode::FULL_ACCESS, PermissionCode::DICTIONARIES])
    <x-backpack::menu-dropdown :title="trans('menu.dictionaries')" icon="la la-book">
        <x-backpack::menu-dropdown-item :title="trans('menu.countries')" icon="la la-globe" :link="backpack_url('country')" />
    </x-backpack::menu-dropdown>
@endcanany
@canany([PermissionCode::FULL_ACCESS, PermissionCode::CONTENT])
    <x-backpack::menu-dropdown :title="trans('menu.content')" icon="la la-music">
        <x-backpack::menu-dropdown-item :title="trans('menu.artists')" icon="la la-guitar" :link="backpack_url('artist')" />
        <x-backpack::menu-dropdown-item :title="trans('menu.albums')" icon="la la-record-vinyl" :link="backpack_url('album')" />
    </x-backpack::menu-dropdown>
@endcanany
@canany([PermissionCode::FULL_ACCESS, PermissionCode::FEEDBACK])
    <x-backpack::menu-dropdown :title="trans('menu.feedback')" icon="la la-envelope">
        <x-backpack::menu-dropdown-item :title="trans('menu.favorite_artists')" icon="la la-headphones" :link="backpack_url('user-favorite-artist')" />
    </x-backpack::menu-dropdown>
@endcanany
@canany([PermissionCode::FULL_ACCESS, PermissionCode::MAINTENANCE])
    <x-backpack::menu-dropdown :title="trans('menu.maintenance')" icon="la la-screwdriver">
        <x-backpack::menu-dropdown-item :title="trans('menu.phpinfo')" icon="la la-info-circle" :link="backpack_url('metrics/php')" />
        <x-backpack::menu-dropdown-item :title="trans('menu.php_fpm')" icon="la la-server" :link="backpack_url('metrics/php-fpm')" />
        <x-backpack::menu-dropdown-item :title="trans('menu.mysql')" icon="la la-database" :link="backpack_url('metrics/mysql')" />
        @if(config('telescope.enabled'))
            <x-backpack::menu-dropdown-item :title="trans('menu.telescope')" icon="la la-moon" :link="backpack_url('metrics/telescope')" />
        @else
            <x-backpack::menu-dropdown-item :title="trans('menu.telescope')" icon="la la-moon" class="text-muted" />
        @endif
        <x-backpack::menu-dropdown-item :title="trans('menu.horizon')" icon="la la-sun" :link="backpack_url('metrics/horizon')" />
        <x-backpack::menu-dropdown-item :title="trans('menu.api_documentation')" icon="la la-code" :link="url('api/docs')" />
        <x-backpack::menu-dropdown-item :title="trans('menu.search_index')" icon="la la-search" :link="config('scout.meilisearch.external_host')" />
        <x-backpack::menu-dropdown-item :title="trans('menu.rabbitmq')" icon="la la-business-time" :link="config('queue.connections.rabbitmq.management_url')" />
    </x-backpack::menu-dropdown>
@endcanany
