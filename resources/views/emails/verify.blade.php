@component('mail::message')

Please click the button below to verify your email address.

@component('mail::button', ['url' => $url, 'color' => 'yellow'])
Verify Email
@endcomponent



@slot('subcopy')
@lang(
    "If you’re having trouble clicking the \"Verify email\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'url' => $url,
    ]
) 
@endslot


Regards,<br>
select-interiorworld.com<br/> 
By<br/> 
SELECT Baubedarf und Handelsges.m.b.H. <br/>
More info on: https://www.select-interiorworld.com/at_en/robers


@endcomponent