@php
//setCurrency('USD');
    $countries = \App\Country::all();
    $homeCountry = \App\Country::where('abbr', getCountry())->first();
    $homeLanguage = \App\Language::where('abbr', App::getLocale())->first();
    $homeCurrency = \App\Currency::where('code', getCurrency())->first();
    //echo Session::get('currency');
@endphp

<div class="selector-dropdown position-relative">
    <div class="headline h3">Your settings</div>
    <a class="close close-menu" href="javascript:;" onclick="$('#selector-modal').modal('hide')"></a>
    <div class="default-selected">
            <span class="abbr selected-country-toggler">{{ $homeCountry->abbr }} 
                <img class="selected-country-flag" src="{{ asset('storage/'.get_thumbnail($homeCountry->icon, 'cropped')) }}" width="17">
            </span> 
            
            <span class="selected-language-toggler">{{ $homeLanguage->abbr }}, {{ $homeCurrency->symbol}} {{ $homeCurrency->code }}</span> 
    </div>    
    
    <select class="select-css" name="country" id="country-selector-dropdown">
        @foreach ($countries as $country)
            <option value="{{ $country->abbr }}" {{ $country->abbr == $homeCountry->abbr ? "selected='selected'" : ''}}>&emsp;{{ $country->getTranslatedAttribute('name') }}</option>
        @endforeach
    </select>

    @foreach ($countries as $key => $country)
        <select class="select-css avail-languages-select {{ $key == 0 ? 'active' : ''}}" id="selectlang-{{ $country->abbr }}">
            @foreach ($country->languages as $lang)
                <option value="{{ $lang->abbr }}" data-url="{{ url_changed(Request::path(), $lang->abbr) }}" {{ $lang->abbr == $homeLanguage->abbr ? "selected='selected'" : ''}}>
                    {{ $lang->getTranslatedAttribute('name') }}
                </option>
            @endforeach
        </select>
    @endforeach

    @foreach ($countries as $key => $country)
        <select class="select-css avail-currency-select {{ $key == 0 ? 'active' : ''}}" id="selectcurrency-{{ $country->abbr }}">
            @foreach ($country->currencies as $currency)
                <option value="{{ $currency->code }}" {{ $currency->code == $homeCurrency->code ? "selected='selected'" : ''}}>{{ $currency->symbol }} {{ $currency->code }}</option>
            @endforeach
        </select>
    @endforeach

    <a href="javascript:;" class="yellow-btn" onClick="setupSettings()">Submit</a>
</div>