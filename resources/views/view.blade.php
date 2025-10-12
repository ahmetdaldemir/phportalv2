<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Page Title -->
    <title>Phone Hospital</title>
    <meta name="robots" content="noindex">

    <!-- Favicon -->
    <link rel="icon" href="{{asset('view/parallax/img/favicon.ico')}}">
    <!-- Bundle -->
    <link rel="stylesheet" href="{{asset('view/vendor/css/bundle.min.css')}}">
    <!-- Plugin Css -->
    <link rel="stylesheet" href="{{asset('view/vendor/css/revolution-settings.min.css')}}">
    <link rel="stylesheet" href="{{asset('view/vendor/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('view/vendor/css/swiper.min.css')}}">
    <link rel="stylesheet" href="{{asset('view/vendor/css/LineIcons.min.css')}}">
    <!-- Style Sheet -->
    <link rel="stylesheet" href="{{asset('view/parallax/css/pagepiling.css')}}">
    <link rel="stylesheet" href="{{asset('view/parallax/css/style.css')}}">

    <script src="{{asset('assets/vendor/js/helpers.js')}}"></script>
    <script src="{{asset('assets/vendor/js/config.js')}}"></script>

</head>

<body>

<script>

    window.addEventListener('load', function() {
        var internetCheckDiv = document.getElementById('internet-check');
        var notAvailableDiv = document.getElementById('internet-not-available');
        var pageScroll = document.getElementById('page-scroll');

        function checkInternetConnection() {
            if (navigator.onLine) {
                internetCheckDiv.style.display = 'block';
                notAvailableDiv.style.display = 'none';
                pageScroll.style.display = 'block';
                console.log('İnternet bağlantısı var.');
            } else {
                internetCheckDiv.style.display = 'none';
                notAvailableDiv.style.display = 'block';
                pageScroll.style.display = 'none';

                console.log('İnternet bağlantısı yok.');
            }
        }

        checkInternetConnection();

        window.addEventListener('online', checkInternetConnection);
        window.addEventListener('offline', checkInternetConnection);
    });

</script>
<div id="internet-check"></div>

<div id="internet-not-available" style="display: none;">
    <img src="{{asset('img/not-connection.jpg')}}" />
</div>
<!--Preloader-->

<div class="loader" id="loader-fade">
    <div class="linear-activity">
        <div class="indeterminate"></div>
    </div>
</div>

<!--End Preloader-->

<!--Header Start-->
<header>

    <!--Navigation-->
    <nav class="navbar navbar-top-default navbar-expand-lg navbar-standard">
        <div class="container">
            <a href="#page1" title="Logo" class="link logo scroll">
                <!--Logo Default-->
                <img src="{{asset('img/logo.png')}}" class="logo-dark" style="width: 200px" alt="logo">
            </a>


            <!--Side Menu Button-->
            <div class="side-nav-btn animated-wrap" id="sidemenu_toggle">
                <div class="animated-element">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- side menu -->
    <div class="side-menu">
        <div class="inner-wrapper">
            <span class="btn-close" id="btn_sideNavClose"><i></i><i></i></span>
            <nav class="side-nav text-center">
                <div class="signin" >
                    <a style="width: 100%; margin-bottom: 15px;" href="{{ route('login') }}" class="btn btn-danger">Giriş YAP</a>
                </div>
              <div class="sorgula">
                  <input class="sorgulaInput" type="text"   name="">
                  <button class="btn btn-danger sorgulabutton">Sorgula</button>
              </div>

            </nav>

            <div class="side-footer text-white w-100">
                <ul class="social-icons-simple">
                    <li><a href="#." class="facebook_bg_hvr2"><i class="fab fa-facebook-f" aria-hidden="true"></i></a> </li>
                    <li><a href="#." class="twitter_bg_hvr2"><i class="fab fa-twitter" aria-hidden="true"></i></a> </li>
                    <li><a href="#." class="linkdin_bg_hvr2"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a> </li>
                    <li><a href="#." class="instagram_bg_hvr2"><i class="fab fa-instagram" aria-hidden="true"></i></a> </li>
                </ul>
                <p class="whitecolor">&copy; 2018 Phone Hospital. Made With Love by </p>
            </div>
        </div>
    </div>
    <!-- End side menu -->


    <!--slider social-->
    <div class="slider-social d-md-block d-none">
        <ul class="list-unstyled">
            <li class="animated-wrap"><a class="animated-element" href="javascript:void(0);"><i class="fab fa-facebook-f" aria-hidden="true"></i></a></li>
            <li class="animated-wrap"><a class="animated-element" href="javascript:void(0);"><i class="fab fa-twitter" aria-hidden="true"></i></a></li>
            <li class="animated-wrap"><a class="animated-element" href="javascript:void(0);"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a></li>
            <li class="animated-wrap"><a class="animated-element" href="javascript:void(0);"><i class="fab fa-instagram" aria-hidden="true"></i></a></li>
        </ul>
    </div>

</header>


<div id="page-scroll">

    <!--Slider-->
    <section class="section" id="page1">
        <div id="rev_slider_346_1_wrapper" class="rev_slider_wrapper fullscreen-container" data-alias="beforeafterslider1" data-source="gallery" style="background:#252525;padding:0px;">
            <!-- START REVOLUTION SLIDER 5.4.3.3 fullscreen mode -->
            <div id="main-slider-four" class="rev_slider fullscreenbanner" style="display:none;" data-version="5.4.3.3">
                <ul>
                    <!-- Slide -->
                    <li data-index="rs-964" data-transition="fade" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="off" data-easein="default" data-easeout="default" data-masterspeed="default" data-thumb="../../assets/parallax/img/night-100x50.jpg" data-rotate="0" data-saveperformance="off" data-title="Slide" data-param1="" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="" data-beforeafter='{"moveto":"50%|50%|50%|50%","bgColor":"","bgFit":"cover","bgPos":"center center","bgRepeat":"no-repeat","direction":"horizontal","easing":"Power2.easeInOut","delay":"500","time":"750","out":"fade","carousel":false}'>
                        <!-- MAIN IMAGE -->
                        <h2 class="d-none">sd</h2>
                        <img src="{{asset('view/parallax/img/banner-12.jpg')}}" alt="" data-bgposition="center center" data-kenburns="on" data-duration="5000" data-ease="Power4.easeOut" data-scalestart="150" data-scaleend="100" data-rotatestart="0" data-rotateend="0" data-blurstart="30" data-blurend="0" data-offsetstart="0 0" data-offsetend="0 0" data-bgparallax="off" class="rev-slidebg" data-no-retina>
                        <!-- LAYERS -->
                        <div class="tp-caption tp-resizeme rs-parallaxlevel-5"
                             data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                             data-y="['middle','middle','middle','middle']" data-voffset="['-140','-140','-140','-140']"
                             data-fontsize="['60','60','60','50']"
                             data-whitespace="nowrap" data-responsive_offset="on"
                             data-width="['none','none','none','none']" data-type="text"
                             data-textalign="['center','center','center','center']"
                             data-beforeafter="before"
                             data-transform_idle="o:1;"
                             data-transform_in="x:-50px;opacity:0;s:2000;e:Power3.easeOut;"
                             data-transform_out="s:1000;e:Power3.easeInOut;s:1000;e:Power3.easeInOut;"
                             data-start="1000" data-splitin="none" data-splitout="none"
                             style="z-index:1; font-weight: 100; color: #ffffff; font-family: 'Poppins', sans-serif;text-transform:capitalize">SİZ NEREDE
                        </div>
                        <div class="tp-caption tp-resizeme rs-parallaxlevel-5"
                             data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                             data-y="['middle','middle','middle','middle']" data-voffset="['-140','-140','-140','-140']"
                             data-fontsize="['60','60','60','50']"
                             data-whitespace="nowrap" data-responsive_offset="on"
                             data-width="['none','none','none','none']" data-type="text"
                             data-textalign="['center','center','center','center']"
                             data-beforeafter="after"
                             data-transform_idle="o:1;"
                             data-transform_in="x:-50px;opacity:0;s:2000;e:Power3.easeOut;"
                             data-transform_out="s:1000;e:Power3.easeInOut;s:1000;e:Power3.easeInOut;"
                             data-start="1000" data-splitin="none" data-splitout="none"
                             style="z-index:1; font-weight: 100; color: #ffffff; font-family: 'Poppins', sans-serif;text-transform:capitalize">SİZ NEREDE
                        </div>
                        <div class="tp-caption tp-resizeme rs-parallaxlevel-5"
                             data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                             data-y="['middle','middle','middle','middle']" data-voffset="['-70','-70','-70','-70']"
                             data-fontsize="['60','60','60','50']"
                             data-whitespace="nowrap" data-responsive_offset="on"
                             data-width="['none','none','none','none']" data-type="text"
                             data-textalign="['center','center','center','center']"
                             data-beforeafter="before"
                             data-transform_idle="o:1;" data-transform_in="z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;s:1500;e:Power3.easeInOut;" data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;"
                             data-start="1200" data-splitin="none" data-splitout="none"
                             style="z-index:2; font-weight: 700; letter-spacing: 1px; color: #ffffff; font-family: 'Poppins', sans-serif;text-transform:capitalize">PHONE HOSPITAL
                        </div><div class="tp-caption tp-resizeme rs-parallaxlevel-5"
                                   data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                                   data-y="['middle','middle','middle','middle']" data-voffset="['-70','-70','-70','-70']"
                                   data-fontsize="['60','60','60','50']"
                                   data-whitespace="nowrap" data-responsive_offset="on"
                                   data-width="['none','none','none','none']" data-type="text"
                                   data-textalign="['center','center','center','center']"
                                   data-beforeafter="after"
                                   data-transform_idle="o:1;" data-transform_in="z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;s:1500;e:Power3.easeInOut;" data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;"
                                   data-start="1200" data-splitin="none" data-splitout="none"
                                   style="z-index:2; font-weight: 700; letter-spacing: 1px; color: #ffffff; font-family: 'Poppins', sans-serif;text-transform:capitalize">PHONE HOSPITAL
                        </div>
                        <div class="tp-caption tp-resizeme rs-parallaxlevel-5"
                             data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                             data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']"
                             data-fontsize="['60','60','60','50']"
                             data-whitespace="nowrap" data-responsive_offset="on"
                             data-width="['none','none','none','none']" data-type="text"
                             data-textalign="['center','center','center','center']"
                             data-beforeafter="before"
                             data-transform_idle="o:1;"
                             data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;"
                             data-transform_out="s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                             data-start="1000" data-splitin="none" data-splitout="none"
                             style="z-index:3; font-weight: 100; color: #ffffff; font-family: 'Poppins', sans-serif;text-transform:capitalize">ORADA
                        </div>
                        <div class="tp-caption tp-resizeme rs-parallaxlevel-5"
                             data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                             data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']"
                             data-fontsize="['60','60','60','50']"
                             data-whitespace="nowrap" data-responsive_offset="on"
                             data-width="['none','none','none','none']" data-type="text"
                             data-textalign="['center','center','center','center']"
                             data-beforeafter="after"
                             data-transform_idle="o:1;"
                             data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;"
                             data-transform_out="s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                             data-start="1000" data-splitin="none" data-splitout="none"
                             style="z-index:3; font-weight: 100; color: #ffffff; font-family: 'Poppins', sans-serif;text-transform:capitalize">ORADA
                        </div>
                        <div class="tp-caption tp-resizeme"
                             data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                             data-y="['middle','middle','middle','middle']" data-voffset="['70','70','70','70']"
                             data-fontsize="['22','22','18','18']"
                             data-whitespace="nowrap" data-responsive_offset="on"
                             data-width="['none','none','none','none']" data-type="text"
                             data-textalign="['center','center','center','center']"
                             data-beforeafter="before"
                             data-transform_idle="o:1;"
                             data-transform_in="z:0;rX:0deg;rY:0;rZ:0;sX:2;sY:2;skX:0;skY:0;opacity:0;s:1000;e:Power2.easeOut;"
                             data-transform_out="s:1000;e:Power3.easeInOut;s:1000;e:Power3.easeInOut;"
                             data-mask_in="x:0px;y:0px;s:inherit;e:inherit;"
                             data-start="1500" data-splitin="none" data-splitout="none"
                             style="z-index:4; font-weight: 100; color: #ffffff; line-height:30px;  font-family: 'Open Sans', sans-serif;text-transform:capitalize">Telefonlarınızın garantili olarak en kısa sürede arıza tesbiti ve onarımı sağlanır
                        </div>
                        <div class="tp-caption tp-resizeme"
                             data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                             data-y="['middle','middle','middle','middle']" data-voffset="['70','70','70','70']"
                             data-fontsize="['22','22','18','18']"
                             data-whitespace="nowrap" data-responsive_offset="on"
                             data-width="['none','none','none','none']" data-type="text"
                             data-textalign="['center','center','center','center']"
                             data-beforeafter="after"
                             data-transform_idle="o:1;"
                             data-transform_in="z:0;rX:0deg;rY:0;rZ:0;sX:2;sY:2;skX:0;skY:0;opacity:0;s:1000;e:Power2.easeOut;"
                             data-transform_out="s:1000;e:Power3.easeInOut;s:1000;e:Power3.easeInOut;"
                             data-mask_in="x:0px;y:0px;s:inherit;e:inherit;"
                             data-start="1500" data-splitin="none" data-splitout="none"
                             style="z-index:4; font-weight: 100; color: #ffffff; line-height:30px;  font-family: 'Open Sans', sans-serif;text-transform:capitalize">Telefonlarınızın garantili olarak en kısa sürede arıza tesbiti ve onarımı sağlanır
                        </div>
                        <div class="tp-caption tp-resizeme"
                             id="slide-24-layer-129" data-x="['center','center','center','center']"
                             data-hoffset="['0','0','0','0']" data-y="['bottom','bottom','bottom','bottom']"
                             data-voffset="['190','70','70','130']"
                             data-width="['160','160','160','160']"
                             data-frames='[{"delay":600,"speed":2000,"frame":"0","from":"sX:1;sY:1;opacity:0;fb:40px;","to":"o:1;fb:0;","ease":"Power4.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;fb:0;","ease":"Power3.easeInOut"}]'
                             data-textAlign="['center','center','center','center']"
                             style="z-index:99; max-width: 960px">
                            <a href="https://phonehospital.com.tr" class="scroll btn btn-large btn-rounded btn-blue color-white link">E - TİCARET</a>
                        </div>

                    </li>
                </ul>
                <div class="tp-bannertimer tp-bottom" style="visibility: hidden !important;"></div>
            </div>
        </div>
    </section>
    <!--Slider End-->



</div>

<!--Animated Cursor-->
<div id="aimated-cursor">
    <div id="cursor">
        <div id="cursor-loader"></div>
    </div>
</div>
<!--Animated Cursor End-->


<!-- JavaScript -->
<script src="{{asset('view/vendor/js/bundle.min.js')}}"></script>

<!-- Plugin Js -->
<script src="{{asset('view/vendor/js/owl.carousel.min.js')}}"></script>
<script src="{{asset('view/vendor/js/swiper.min.js')}}"></script>
<script src="{{asset('view/vendor/js/jquery.appear.js')}}"></script>
<script src="{{asset('view/vendor/js/TweenMax.min.js')}}"></script>
<script src="{{asset('view/vendor/js/parallaxie.min.js')}}"></script>
<!-- REVOLUTION JS FILES -->
<script src="{{asset('view/vendor/js/jquery.themepunch.tools.min.js')}}"></script>
<script src="{{asset('view/vendor/js/jquery.themepunch.revolution.min.js')}}"></script>
<!-- SLIDER REVOLUTION EXTENSIONS -->
<script src="{{asset('view/vendor/js/extensions/revolution.extension.actions.min.js')}}"></script>
<script src="{{asset('view/vendor/js/extensions/revolution.extension.carousel.min.js')}}"></script>
<script src="{{asset('view/vendor/js/extensions/revolution.extension.kenburn.min.js')}}"></script>
<script src="{{asset('view/vendor/js/extensions/revolution.extension.layeranimation.min.js')}}"></script>
<script src="{{asset('view/vendor/js/extensions/revolution.extension.migration.min.js')}}"></script>
<script src="{{asset('view/vendor/js/extensions/revolution.extension.navigation.min.js')}}"></script>
<script src="{{asset('view/vendor/js/extensions/revolution.extension.parallax.min.js')}}"></script>
<script src="{{asset('view/vendor/js/extensions/revolution.extension.slideanims.min.js')}}"></script>
<script src="{{asset('view/vendor/js/extensions/revolution.extension.video.min.js')}}"></script>
<script src="{{asset('view/vendor/js/extensions/revolution.extension.beforeafter.min.js')}}"></script>

<script src="{{asset('view/parallax/js/pagepiling.min.js')}}"></script>
<script src="{{asset('view/parallax/js/swiper-thumbnail.js')}}"></script>
<script src="{{asset('view/vendor/js/contact_us.js')}}"></script>
<script src="{{asset('view/parallax/js/script.js')}}"></script>
<style>
    .signin{
        width: 20%;
        margin: 0 auto;
    }
    .sorgula{
        width: 20%;
        margin: 0 auto;
    }
    .sorgulabutton{

    }
    .sorgulaInput{
        width: 73%;
    }
    @media only screen and (max-width: 600px) {
        .signin {
            width: 100%;
        }
        .sorgula{
            width: 100%;
        }
        .sorgulaInput{
            width: 100%;
        }
        .sorgulabutton{
            width: 100%;
        }
    }
</style>

</body>
</html>
