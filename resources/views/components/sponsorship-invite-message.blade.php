@if (isset($is_empty) && $is_empty == true)
    @switch ($id)
        @case(0)
            <span class="h6">
                <b>Bad News:</b> Sponsors aren't available for COMPUTERUN yet.<br>
                <b>Good News:</b> Your brand can <b><a href="/sponsor-us">become the first</a></b> to appear on our event!
            </span>
            @break
        @case(1)
            <span class="display-2 font-800">404</span><br>
            <span class="h3 font-800">Sponsors Not Found</span><br>
            <span class="h6">Please try again later, or <b><a href="/sponsor-us">Contact Us</a></b> for sponsorship opportunities.</span>
            @break
        @case(2)
            <span class="h6 red-text">{"error":"NO_SPONSORS_FOUND", "message":"Oh, you've just found a bug! Want to sponsor our event too? Visit <a href="/sponsor-us">https://computerun.id/sponsor-us</a> for more details."}</span>
            @break
        @case(3)
            <span class="h6 red-text">
                Unhandled <code>ArrayIndexOutOfBoundsException</code> error at <code>utils/SponsorsList.java</code><br>
                Oh, you've just found a bug! Want to sponsor our event too? Visit <a href="/sponsor-us">https://computerun.id/sponsor-us</a> for more details.
            </span>
            @break
        @case(4)
            <span class="h4 font-800">
                Makan nasi bareng empal gentong, <sup class="h6 font-400">(Cakep...)</sup><br>
                Wah, daftar sponsornya kosong... <sup class="h6 font-400">(Yaaahhh...)</sup>
            </span><hr>
            <span class="h6">
                Kalau ada yang mau bantu sponsor atau promosiin <b>COMPUTERUN</b>,<br>jangan lupa klik <a href="/sponsor-us">https://computerun.id/sponsor-us</a> biar acaranya makin seru!!!
            </span>
            @break
        @case(5)
            <img src="/img/icons/2020/missingno.png" alt="MissingNo."><br>
            <span class="h4 font-800">
                A wild MissingNo. appeared!
            </span><br>
            <span class="h6">
                Oh, that's just a placeholder image. Why not <b><a href="/sponsor-us">sponsor our event</a> and get your brand to show up here for real?</b>.
            </span>
            @break
        @case(6)
            <span class="h6" style="font-family: 'Comic Sans MS', ChalkboardSE-Bold, Chilanka, Qanelas, Poppins, sans-serif; font-weight: 700;">
                Hello, webmaster's here,<br><br>
                We are currently experiencing an internal issue where there are no sponsors available at this moment.<br><br>
                While our <a href="/sponsor-us">Sponsorship Proposal</a> is ready to be shared, here's a picture of a cat.<br><br>
                <img src="/img/icons/loan-7AIDE8PrvA0-unsplash.jpg">
            </span>
            @break
        @case(7)
            <span class="h3 red-text font-800"><u>UNDER CONSTRUCTION</u></span><br>
            <span class="h6">
                This portion of the website is incomplete due to few or no sponsors available.<br>
                Please try again later, or <b><a href="/sponsor-us">Contact Us</a></b> for sponsorship opportunities.
            </span>
    @endswitch
@else

@endif
