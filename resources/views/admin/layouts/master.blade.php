<!doctype html>
<html lang="en" class="remember-theme">

<head>
  <meta charset="utf-8">
  <!--
      Available classes for <html> element:

      'dark'                  Enable dark mode - Default dark mode preference can be set in app.js file (always saved and retrieved in localStorage afterwards):
                                window.One = new App({ darkMode: "system" }); // "on" or "off" or "system"
      'dark-custom-defined'   Dark mode is always set based on the preference in app.js file (no localStorage is used)
      'remember-theme'        Remembers active color theme between pages using localStorage when set through
                                - Theme helper buttons [data-toggle="theme"]
    -->
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>@yield('title')</title>

  <meta name="description" content="OneUI - Bootstrap 5 Admin Template &amp; UI Framework created by pixelcave">
  <meta name="author" content="pixelcave">
  <meta name="robots" content="index, follow">

  <!-- Open Graph Meta -->
  <meta property="og:title" content="OneUI - Bootstrap 5 Admin Template &amp; UI Framework">
  <meta property="og:site_name" content="OneUI">
  <meta property="og:description" content="OneUI - Bootstrap 5 Admin Template &amp; UI Framework created by pixelcave">
  <meta property="og:type" content="website">
  <meta property="og:url" content="">
  <meta property="og:image" content="">

  <!-- Icons -->
  <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
  <link rel="shortcut icon" href="{{ asset('assets/media/favicons/favicon.png') }}">
  <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/media/favicons/favicon-192x192.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/favicons/apple-touch-icon-180x180.png') }}">
  <!-- END Icons -->

  <!-- Stylesheets -->
  <!-- OneUI framework -->
  <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/oneui.min.css') }}">

  <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
  <!-- <link rel="stylesheet" id="css-theme" href="{{ asset('assets/css/themes/amethyst.min.css') }}"> -->
  <!-- END Stylesheets -->

  <!-- Load and set color theme + dark mode preference (blocking script to prevent flashing) -->
  <script src="{{ asset('assets/js/setTheme.js') }}"></script>
</head>

<body>
 
  <div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-boxed">
   
    @include('admin.layouts.sidebar')

    @include('admin.layouts.header')

    <!-- END Sidebar -->

    <!-- Header -->

    <!-- END Header -->

    <!-- Main Container -->
    @yield('content')
    <!-- END Main Container -->

    <!-- Footer -->
      @include('admin.layouts.footer')
    <!-- END Footer -->
  </div>
  <!-- END Page Container -->

  <!--
        OneUI JS

        Core libraries and functionality
        webpack is putting everything together at assets/_js/main/app.js
    -->
  <script src="{{ asset('assets/js/oneui.app.min.js') }}"></script>
  @yield('script')
</body>

</html>