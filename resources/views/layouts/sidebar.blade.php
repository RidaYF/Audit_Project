@php
    use App\Helpers\MenuHelper;
    $menuGroups = MenuHelper::getMenuGroups();

    // Get current path
    $currentPath = request()->path();
@endphp

<aside id="sidebar"
    class="fixed flex flex-col mt-0 top-0 px-5 left-0 bg-white text-gray-900 h-screen transition-all duration-300 ease-in-out z-50 border-r border-gray-200"
    x-data="{
        openSubmenus: {},
        init() {
            this.initializeActiveMenus();
        },
        initializeActiveMenus() {
            const currentPath = '{{ $currentPath }}';

            @foreach ($menuGroups as $groupIndex => $menuGroup)
                @foreach ($menuGroup['items'] as $itemIndex => $item)
                    @if (isset($item['subItems']))
                        @foreach ($item['subItems'] as $subItem)
                            if (currentPath === '{{ ltrim($subItem['path'], '/') }}' ||
                                window.location.pathname === '{{ $subItem['path'] }}') {
                                this.openSubmenus['{{ $groupIndex }}-{{ $itemIndex }}'] = true;
                            }
                        @endforeach
                    @endif
                @endforeach
            @endforeach
        },
        toggleSubmenu(groupIndex, itemIndex) {
            const key = groupIndex + '-' + itemIndex;
            const newState = !this.openSubmenus[key];

            if (newState) {
                this.openSubmenus = {};
            }

            this.openSubmenus[key] = newState;
        },
        isSubmenuOpen(groupIndex, itemIndex) {
            const key = groupIndex + '-' + itemIndex;
            return this.openSubmenus[key] || false;
        },
        isActive(path) {
            return window.location.pathname === path || '{{ $currentPath }}' === path.replace(/^\//, '');
        }
    }"
    :class="{
        'w-[290px]': $store.sidebar.isExpanded || $store.sidebar.isMobileOpen || $store.sidebar.isHovered,
        'w-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">
    
    <!-- Logo Section -->
    <div class="pt-8 pb-7 flex items-center"
        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
        'xl:justify-center' :
        'justify-start'">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" 
                 class="flex items-center gap-2">
                <span class="text-xl font-bold text-gray-900">Learn=>Audit</span>
                <span class="text-xs text-gray-500">ISO 2700x</span>
            </div>
            <div x-show="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                <span class="text-xl font-bold text-purple-600">LA</span>
            </div>
        </a>
    </div>

    <!-- Navigation Menu -->
    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar flex-1">
        <nav class="mb-6">
            <div class="flex flex-col gap-4">
                @foreach ($menuGroups as $groupIndex => $menuGroup)
                    <div>
                        <!-- Menu Group Title -->
                        <h2 class="mb-4 text-xs uppercase leading-[20px] text-gray-400"
                            :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                            'text-center' : 'text-left'">
                            <template
                                x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                                {{ $menuGroup['title'] }}
                            </template>
                            <template x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                                —
                            </template>
                        </h2>

                        <!-- Menu Items -->
                        <ul class="flex flex-col gap-1">
                            @foreach ($menuGroup['items'] as $itemIndex => $item)
                                <li>
                                    @if (isset($item['subItems']))
                                        <!-- Menu Item with Submenu -->
                                        <button @click="toggleSubmenu({{ $groupIndex }}, {{ $itemIndex }})"
                                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 cursor-pointer text-left"
                                            :class="[
                                                isSubmenuOpen({{ $groupIndex }}, {{ $itemIndex }}) ?
                                                'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-100',
                                                !$store.sidebar.isExpanded && !$store.sidebar.isHovered ?
                                                'justify-center' : 'justify-start'
                                            ]">

                                            <!-- Icon -->
                                            <span class="flex-shrink-0 inline-flex items-center justify-center w-5 h-5"
                                                  :class="isSubmenuOpen({{ $groupIndex }}, {{ $itemIndex }}) ?
                                                        'text-purple-600' : 'text-gray-500'">
                                                {!! MenuHelper::getIconSvg($item['icon']) !!}
                                            </span>

                                            <!-- Text -->
                                            <span
                                                x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                                class="flex-1 text-sm font-medium">
                                                {{ $item['name'] }}
                                            </span>

                                            <!-- Chevron -->
                                            <svg x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                                class="w-4 h-4 transition-transform duration-200 flex-shrink-0"
                                                :class="{
                                                    'rotate-180 text-purple-500': isSubmenuOpen({{ $groupIndex }}, {{ $itemIndex }}),
                                                    'text-gray-400': !isSubmenuOpen({{ $groupIndex }}, {{ $itemIndex }})
                                                }"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <!-- Submenu -->
                                        <div x-show="isSubmenuOpen({{ $groupIndex }}, {{ $itemIndex }}) && ($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen)">
                                            <ul class="mt-1 space-y-1">
                                                @foreach ($item['subItems'] as $subItem)
                                                    <li>
                                                        <a href="{{ $subItem['path'] }}" 
                                                           class="flex items-center justify-between pl-11 pr-3 py-2 text-sm rounded-lg transition-all duration-200"
                                                           :class="isActive('{{ $subItem['path'] }}') ?
                                                                'bg-purple-50 text-purple-600' :
                                                                'text-gray-600 hover:bg-gray-100 hover:text-gray-900'">
                                                            <span>{{ $subItem['name'] }}</span>
                                                            @if (!empty($subItem['new']))
                                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-purple-100 text-purple-600">new</span>
                                                            @endif
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <!-- Simple Menu Item -->
                                        <a href="{{ $item['path'] }}" 
                                           class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 cursor-pointer text-left"
                                           :class="[
                                                isActive('{{ $item['path'] }}') ?
                                                'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-100',
                                                (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                                                'justify-center' : 'justify-start'
                                           ]">

                                            <!-- Icon -->
                                            <span class="flex-shrink-0 inline-flex items-center justify-center w-5 h-5"
                                                  :class="isActive('{{ $item['path'] }}') ? 'text-purple-600' : 'text-gray-500'">
                                                {!! MenuHelper::getIconSvg($item['icon']) !!}
                                            </span>

                                            <!-- Text -->
                                            <span
                                                x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                                class="flex-1 text-sm font-medium">
                                                {{ $item['name'] }}
                                            </span>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="mt-auto pt-4 pb-6 border-t border-gray-200">
        <div x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
             class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-all cursor-pointer group">
            <img src="https://ui-avatars.com/api/?name=John+Doe&background=8B3DFF&color=fff&size=40" 
                 class="w-10 h-10 rounded-full flex-shrink-0" alt="Avatar">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">John Doe</p>
                <p class="text-xs text-gray-500 truncate">Auditeur ISO</p>
            </div>
            <i class="ti ti-logout text-gray-400 group-hover:text-red-500 transition-colors flex-shrink-0"></i>
        </div>
        <div x-show="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen"
             class="flex justify-center">
            <img src="https://ui-avatars.com/api/?name=JD&background=8B3DFF&color=fff&size=32" 
                 class="w-8 h-8 rounded-full cursor-pointer hover:opacity-80 transition-opacity" alt="Avatar">
        </div>
    </div>
</aside>

<!-- Mobile Overlay -->
<div x-show="$store.sidebar.isMobileOpen" @click="$store.sidebar.setMobileOpen(false)"
    class="fixed inset-0 z-40 bg-black/50 xl:hidden transition-opacity duration-300"
    x-cloak></div>

<!-- Styles pour garantir l'alignement -->
<style>
    /* Force l'alignement vertical */
    .menu-item, .menu-item * {
        box-sizing: border-box;
    }
    
    /* Assure que les SVG ont la bonne taille */
    .menu-item svg,
    a svg,
    button svg {
        width: 20px !important;
        height: 20px !important;
        display: block !important;
    }
    
    /* Fix pour l'alignement des textes */
    .flex-1 {
        flex: 1 1 0%;
        min-width: 0;
    }
    
    /* Scrollbar */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>