<!DOCTYPE html>
<html>
    <head>
        @component("components.meta", ["title" => "Contact"])
        @endcomponent
    </head>
    <body>
        <div class="container-2 content-top bg-event">
            @component("components.navbar")
            @endcomponent
            @component("components.navbar-mobile", ['template' => 'contact-us'])
            @endcomponent
            <div class="margin-2 text-center content-divider">
                <span class="h4 font-700">KEEP IN TOUCH WITH US</span><br>
                <p class="h1 font-800 gradient-text">Contact Person</p>
            </div>
        </div>
        <div class="container-0">
            <img class="container-clip" src="/img/backgrounds/2.png">
            <div class="margin-2 content-top text-center h5">
                <br>
                <p class="font-800 h4">
                    <u>OUR SOCIAL MEDIA</u>
                </p>
                <div class="h5 contact-us">
                    <a href="https://www.instagram.com/computerun" class="navbar-link"><img src="img/icons/2020/Instagram.png"></a>
                    <a href="https://computerun.id/line" class="navbar-link"><img src="img/icons/2020/LINE.png"></a>
                    <a href="https://computerun.id/youtube" class="navbar-link"><img src="img/icons/2020/YouTube.png"></a>
                    <a href="https://www.facebook.com/computerun.computerun.9" class="navbar-link"><img src="img/icons/2020/Facebook.png"></a>
                    <a href="https://twitter.com/Computerun2020" class="navbar-link"><img src="img/icons/2020/Twitter.png"></a>
                </div>
                <br>
                <p>SPONSORSHIP INQUIRIES</p>
                <p>
                    <h3>David</h3>
                    <b>E-mail: davidyohaness14@gmail.com</b><br>
                    <b>WhatsApp: +62-812-8634-9820</b><br>
                    <b>LINE ID: davidsetyawann</b>
                </p>
                <br>
                <p>MEDIA AND PROMOTION</p>
                <p>
                    <h3>Vonny</h3>
                    <b>E-mail: vonnymelinda01@gmail.com</b><br>
                    <b>WhatsApp: +62-812-8737-8769</b><br>
                    <b>LINE ID: vonnymelindaa</b>
                </p>
            </div>
            @component ('components.sponsors')
            @endcomponent
            <img class="container-clip for-footer" src="/img/backgrounds/7.png">
        </div>
        @component('components.footer')
        @endcomponent
    </body>
</html>
