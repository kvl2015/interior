<div class="variant-info">
    @if (count((array)($options)))
        <div class="options">
            @foreach ($options as $key => $option)
                <div class="option">
                    @php 
                    //dd($option);
                        $prices = @$option->price ? $option->price : array(); 
                        $articles = @$option->article ? $option->article : array(); 
                        $ids = @$option->selected ? $option->selected : array(); 
                        $optVal = \App\Option::withTranslations(['en'])->whereIn('id', $option->selected)->get();
                        $optImages = array();
                        $isThumb = '';
                    @endphp

                    @if (@in_array(@$optS->id, $ids))
                        @php $isThumb = 1 @endphp
                        <div class="thumb-selected thumb-selected-no-empty thumb-{{ $key }} {{ $hide }}">
                            <img src="{{ asset('storage/'.get_thumbnail($optS->image, 'cropped')) }}" />
                            <br/><a href="{{ asset('storage/'.$optS->image) }}" 
                                data-lightbox="{{ $optS->name }}" 
                                data-title="{{ $optS->name }}">Enlarge</a>
                        </div>
                    @else
                        <div class="thumb-selected thumb-{{ $key }} {{ $hide }}"></div>
                    @endif

                    @if (count(@$optVal) <= 1)
                        <div class="singl-option-data"><span>{{ $option->label }}</span> {{ $optVal[0]->name}}</div>
                    @elseif (count(@$optVal) > 1)
                    <div class="select-option-container {{ $isThumb == 1 ? 'select-option-container-thumb' : '' }}">
                        <select page-reload="{{ $optVal[0]->group->is_sku == 1  && $allowReload == 1 }}" class="select-css sc-product-option" data-key="{{ $key }}" name="option_{{ $key }}" opt-lable-name="{{ $option->label }}"  opt_product_id="{{ $id }}">
                            <option value="">Select option {{ $option->label }}</option>
                            @foreach ($optVal as $t => $optValue)
                                @php $selected = @$articles[$t] == @$selectedOption['article'] ? 'selected="selected"' : '' @endphp
                                <option value="{{ $optValue->code }}" 
                                    {{ $selected  }}
                                    opt-price-value="{{ @$prices[$t] ? $prices[$t] : 0 }}" 
                                    opt-article="{{ @$articles[$t] }}"
                                    opt-id="{{ $optValue->id }}">{{ $optValue->name }}
                                </option>
                                @if ($optValue->image)
                                    @php $optImages[] = $optValue; @endphp
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="error option_error_{{ $key }}_{{ $id }}"></div>
                    <div class="clearfix"></div>
                    @if (count($optImages))
                        <div class="options-image {{ $hide }} horizontal-images">
                            @foreach ($optImages as $t => $tData)
                                @if (is_file('storage/'.$tData->image))
                                    <img class="option-thumb {{ @$optS->id == @$tData->id ? 'opt-thumb-selected': ''}}" 
                                    data-toggle="tooltip"
                                    data-original-title="<img src='{{ asset('storage/'.$tData->image) }}'>"
                                    data-option-key="{{ $key }}"
                                    data-opt-article="{{ @$articles[$t] }}"
                                    data-opt-name="{{ $tData->getTranslatedAttribute('name') }}"
                                    data-big-src="{{ asset('storage/'.$tData->image) }}"
                                    page-reload="{{ $optVal[0]->group->is_sku == 1  && $allowReload == 1 }}"
                                    data-option-id="{{ $tData->code }}" 
                                    src="{{ asset('storage/'.$tData->getThumbnail($tData->image, 'cropped')) }}" />
                                @else
                                    <p>no photo</p>
                                @endif
                            @endforeach
                        </div>
                    @endif                    
                </div>
            @endforeach
        </div>
    @endif
</div>
<div class="clearfix"></div>
                        

