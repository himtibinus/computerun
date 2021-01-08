@component('mail::message')

Hi **{{$data["user_name"]}}**,

Wish you have a good day!

Thank you for your interest to join Webinar Computerun 2020. We have a great event planned and know that you will receive much informative knowledge through the webinar.

This email is just a friendly reminder about our upcoming webinar **{{$data["event_name"]}}**, which will be held on:

+ **Date:** {{$data["event_date"]}}
+ **Time:** {{$data["event_time"]}} 

Make sure to attend on time and don't forget to use the virtual background for participants, which you can download on https://tinyurl.com/VBPeserta.

On {{$data["event_time"]}}, you will be able to obtain the Zoom meeting link via our website at https://computerun.id/home. Sign in to your account (or go into the "Profile" tab if logged in), then click on the **Join Event** button next to the chosen webinar.

If you haven't installed Zoom yet, please install them first from https://zoom.us/download (under "Zoom Client for Meetings").

**We recommend you to join at least 15 minutes before the start of the event** to avoid network traffic congestions on our website.

Thank you and see you at the event!

Sincerely,
COMPUTERUN 2020.
@endcomponent
