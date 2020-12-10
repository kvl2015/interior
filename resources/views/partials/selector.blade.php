@php
    $countries = \App\Country::all();
    $homeCountry = \App\Country::where('abbr', getCountry())->first();
    $homeLanguage = \App\Language::where('abbr', App::getLocale())->first();
    $homeCurrency = \App\Currency::where('code', getCurrency())->first();
@endphp

<div class="dropdown">
    <a class="btn dropdown-toggle language-selector" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <div class="default-selected">
            <span class="abbr selected-country-toggler">{{ $homeCountry->abbr }} 
                <img class="selected-country-flag" src="{{ asset('storage/'.get_thumbnail($homeCountry->icon, 'cropped')) }}" width="17">
            </span> 
            
            <span class="selected-language-toggler">{{ $homeLanguage->abbr }}, {{ $homeCurrency->symbol}} {{ $homeCurrency->code }}</span> 
        </div>
    </a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

        <div class="selector">
            <div class="headline h3">Your settings</div>

            <div class="row">
                <div class="col-4">
                    <div class="countries">
                        <div class="country-active">
                            Selected country: <br/><div class=""><span class="abbr selected-country">{{ $homeCountry->abbr }}</span><img class="selected-country-flag" src="{{ asset('storage/'.get_thumbnail($homeCountry->icon, 'cropped')) }}"></div>
                        </div>
                        <div class="data-list" id="country-container">
                            <ul>
                            @foreach ($countries as $country)
                                <li><a href="{{ url_changed(Request::path(), $country->abbr.'_en') }}" data-id="{{ $country->id }}" data-type="country" data-abbr="{{ $country->abbr }}"><img src="{{ asset('storage/'.get_thumbnail($country->icon, 'cropped')) }}" /> {{ $country->getTranslatedAttribute('name') }}</a></li>
                            @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="languages">
                        <div class="country-active">
                            Selected language: <br/><div class=""><span class="abbr selected-language">{{ $homeLanguage->abbr }}</span><img class="selected-language-flag" src="{{ asset('storage/'.get_thumbnail($homeLanguage->icon, 'cropped')) }}"></div>
                        </div>
                        <div class="data-list" id="language-container">
                            @foreach ($countries as $key => $country)
                                <ul class="avail-languages {{ $key == 0 ? 'active' : ''}}" id="lang-{{ $country->id }}">
                                    @foreach ($country->languages as $lang)
                                        <li>
                                            <a href="{{ url_changed(Request::path(), $country->abbr.'_'.$lang->abbr) }}" data-type="language" data-country="{{ $country->abbr }}" data-abbr="{{ $lang->abbr }}">
                                                <img src="{{ asset('storage/'.get_thumbnail($lang->icon, 'cropped')) }}" /> {{ $lang->getTranslatedAttribute('name') }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="currencies">
                        <div class="country-active">
                            Selected Currency: <br/><span class="selected-currency-symbol">{{ $homeCurrency->symbol}}</span> <span class="selected-currency-code">{{ $homeCurrency->code }}</span>
                        </div>
                        <div class="data-list" id="country-container">
                            @foreach ($countries as $key => $country)
                                <ul class="avail-languages {{ $key == 0 ? 'active' : ''}}" id="currency-{{ $country->id }}">
                                    @foreach ($country->currencies as $currency)
                                        <li><a href="javascript:;" onClick="changeCurrencySetting('{{ $currency->code }}')" data-type="currency" data-country="{{ $country->abbr }}">{{ $currency->symbol }} {{ $currency->code }}</a></li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>   
</div>





