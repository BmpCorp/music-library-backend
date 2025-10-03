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
