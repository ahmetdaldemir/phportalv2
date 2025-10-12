<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
              <span class="app-brand-logo demo">
               @include('layouts.components.logo')
              </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1" style="overflow: auto;">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{route('calculation.categories')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Muhasebe Kategorileri</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{route('calculation.seller')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Bayiler</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{route('calculation.staff')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Personeller</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{route('calculation.accounting')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Gelir / Gider Islemleri</div>
            </a>
        </li>


    </ul>
</aside>
