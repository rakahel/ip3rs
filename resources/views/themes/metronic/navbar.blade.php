<div class="container-fluid d-flex align-items-stretch justify-content-between">
    <!--begin::Aside mobile toggle-->
    <div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
        <div class="btn btn-icon btn-active-color-white" id="kt_aside_mobile_toggle">
            <i class="bi bi-list fs-1"></i>
        </div>
    </div>
    <!--end::Aside mobile toggle-->
    <!--begin::Mobile logo-->
    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
        <a href="javascript:void(0);" class="d-lg-none">
            <img alt="Logo" src="{{ asset('/storage/assets/media/logos/logo-demo13-compact.svg') }}" class="h-25px" />
        </a>
    </div>
    <!--end::Mobile logo-->
    <!--begin::Wrapper-->
    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
        <!--begin::Navbar-->
        <div class="d-flex align-items-stretch" id="kt_header_nav">
            <!--begin::Menu wrapper-->
            @include('themes.metronic.navbar.menu')
            <!--end::Menu wrapper-->
        </div>
        <!--end::Navbar-->
        <!--begin::Topbar-->
        <div class="d-flex align-items-stretch flex-shrink-0">
            <!--begin::Toolbar wrapper-->
            <div class="topbar d-flex align-items-stretch flex-shrink-0">
                <!--begin::Search-->
                <div class="d-flex align-items-stretch">
                    @include('themes.metronic.navbar.search')
                </div>
                <!--end::Search-->
                <!--begin::Activities-->
                <div class="d-flex align-items-stretch">
                    <!--begin::drawer toggle-->
                    <div class="topbar-item px-3 px-lg-5" id="kt_activities_toggle">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>
                    <!--end::drawer toggle-->
                </div>
                <!--end::Activities-->
                <!--begin::Quick links-->
                <div class="d-flex align-items-stretch">
                    <!--begin::Menu wrapper-->
                    @include('themes.metronic.navbar.quick')
                    <!--end::Menu wrapper-->
                </div>
                <!--end::Quick links-->
                <!--begin::Chat-->
                <div class="d-flex align-items-stretch">
                    <!--begin::Menu wrapper-->
                    <div class="topbar-item position-relative px-3 px-lg-5" id="kt_drawer_chat_toggle">
                        <i class="bi bi-chat-left-text fs-3"></i>
                        <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 mt-4 start-50 animation-blink"></span>
                    </div>
                    <!--end::Menu wrapper-->
                </div>
                <!--end::Chat-->
                <!--begin::Notifications-->
                <div class="d-flex align-items-stretch">
                    <!--begin::Menu wrapper-->
                    @include('themes.metronic.navbar.notif')
                    <!--end::Menu wrapper-->
                </div>
                <!--end::Notifications-->
                <!--begin::User-->
                <div class="d-flex align-items-stretch" id="kt_header_user_menu_toggle">
                    <!--begin::Menu wrapper-->
                    @include('themes.metronic.navbar.user')
                    <!--end::Menu wrapper-->
                </div>
                <!--end::User -->
                <!--begin::Heaeder menu toggle-->
                <div class="d-flex align-items-stretch d-lg-none px-3 me-n3" title="Show header menu">
                    <div class="topbar-item" id="kt_header_menu_mobile_toggle">
                        <i class="bi bi-text-left fs-1"></i>
                    </div>
                </div>
                <!--end::Heaeder menu toggle-->
            </div>
            <!--end::Toolbar wrapper-->
        </div>
        <!--end::Topbar-->
    </div>
    <!--end::Wrapper-->
</div>
