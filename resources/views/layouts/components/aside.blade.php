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
        <li class="menu-item active">
            <a href="{{route('home')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Anasayfa</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Kategoriler</span>
        </li>
        <li class="menu-item">
            <a href="{{route('phone.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Telefon</div>
            </a>
        </li>

        <?php
        $category = \App\Models\Category::where('company_id', auth()->user()->company_id)->where('parent_id', 0)->get(); ?>
        @foreach($category as $item)
            <li class="menu-item">
                <a href="{{route('stockcard.list',['category_id' => $item->id])}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">{{$item->name}}</div>
                </a>
            </li>
        @endforeach
        <!-- Layouts -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Stok İşlemleri</span>
        </li>
        <li class="menu-item">
            <a href="{{route('stockcard.serialList')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Seri Numaraları</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('stockcard.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Stok Kartları</div>
            </a>
        </li>
        @role(['Depo Sorumlusu','super-admin'])
        <li class="menu-item">
            <a href="{{route('invoice.index',['type' => 1])}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Gelen Faturalar</div>
            </a>
        </li>
        <!--li class="menu-item">
            <a href="{{route('invoice.index',['type' => 2])}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Giden Faturalar</div>
            </a>
        </li -->
        @endrole
        <li class="menu-item">
            <a href="{{route('sale.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Satış</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('transfer.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Sevk <b style="color: #f00">({{sevkcount()}})</b></div>
            </a>
        </li>
        @role(['Bayi Yetkilisi','Depo Sorumlusu','super-admin'])
        <li class="menu-item">
            <a href="{{route('stockcard.refundlist')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">İADE <b style="color: #f00"></b></div>
            </a>
        </li>
        @endrole
        <li class="menu-item">
            <a href="{{route('demand.list')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">TALEPLER <b style="color: #f00"></b></div>
            </a>
        </li>
        @role(['super-admin'])
        <li class="menu-item">
            <a href="{{route('report')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Raporlar</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('personelsellerreport')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Personel ve Bayi Raporu</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('personelsellernewreport')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Teknik Servis Raporu</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('report.excelReport')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Excel Rapor</div>
            </a>
        </li>

        @endrole
        <!-- Components -->

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Muhasebe İşlemleri</span></li>
        @role(['Depo Sorumlusu','super-admin','Bayi Yetkilisi'])
        <li class="menu-item">
            <a href="{{route('safe.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Kasa</div>
            </a>
        </li>
        @endrole

        @role(['Depo Sorumlusu','super-admin'])
        <li class="menu-item">
            <a href="{{route('bank.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Banka</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('category.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Kategoriler</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('customer.index',['type' => 'customer'])}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Müşteriler</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('customer.index',['type' => 'account'])}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Cari</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{route('accounting_category.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Muhasebe Kategorileri</div>
            </a>
        </li>


        <li class="menu-item">
            <a href="{{route('brand.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Markalar</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{route('color.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Renkler</div>
            </a>
        </li>
        @endrole

        <!-- Forms & Tables -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Teknik Servis İşlemleri</span></li>
        <li class="menu-item">
            <a href="{{route('technical_service.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Teknik Servis</div>
            </a>
        </li>

        @role('super-admin')
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Kullanıcı İşlemleri</span></li>
        <li class="menu-item">
            <a href="{{route('user.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Kullanıcılar</div>
            </a>
        </li>
        @if(\Illuminate\Support\Facades\Auth::user()->company_id == 1)

            <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Log</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{route('role.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Roller</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{route('permission.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Yetkiler</div>
                </a>
            </li>

        @endif
        @endrole
        @if(\Illuminate\Support\Facades\Auth::user()->company_id == 1)
            @role(['Depo Sorumlusu','super-admin'])
            <li class="menu-item">
                <a href="{{route('enumeration.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Stok Sayım</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{route('blog.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Blog Yönetimi</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{route('site_customer.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Site Müşterileri</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{route('site.technical_service_category.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Teknik Servis Kategorileme</div>
                </a>
            </li>
            @endrole
        @endif
        @role(['Depo Sorumlusu','super-admin'])
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Ayarlar</span></li>
        @if(\Illuminate\Support\Facades\Auth::user()->company_id == 1)

            <li class="menu-item">
                <a href="{{route('company.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Firmalar</div>
                </a>
            </li>
        @endif
        <li class="menu-item">
            <a href="{{route('seller.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Şubeler</div>
            </a>
        </li>
        @if(\Illuminate\Support\Facades\Auth::user()->company_id == 1)

            <li class="menu-item">
                <a href="{{route('warehouse.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Depolar</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{route('reason.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Nedenler</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{route('technical_service.category')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Teknik Servis Kategorileri</div>
                </a>
            </li>
        @endif
        @endrole
    </ul>
</aside>
