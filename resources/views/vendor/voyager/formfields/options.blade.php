@php
    $options = (json_decode($dataTypeContent->{$row->field}));
@endphp
<div class="options-block">
    @if (count((array) $options))
        @foreach ($options as $option)
            <div class="product-option">
                @php 
                    $optionGroup = \App\OptionGroup::where('id', $option->group_id)->first();
                    $selectedOptions = \App\Option::whereIn('id', (array) $option->selected)->get(); 
                    $articles = @$option->article ? $option->article : array();
                    $prices = @$option->price ? $option->price : array();
                    $discounts = @$option->discount ? $option->discount : array();
                    $photos = @$option->photo ? $option->photo : array();
                @endphp
                <h4 class="option-title">{{ $optionGroup->name }} ({{ $optionGroup->code }}) <a href="javascript:;" onclick="addOptionGroup('{{ $optionGroup->id }}')">Add option</a></h4>
                <table class="table" id="group_{{ $option->group_id }}">
                    <thead>
                    <tr>
                        <th>Article</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Photo</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($selectedOptions as $t => $selected)
                        <tr>
                            <td>
                                <input type="text" name="article[{{ $selected->group_id }}][]" value="{{ @$articles[$t] ? @$articles[$t] : $selected->code }}" />
                                <input type="hidden" name="ids[{{ $selected->group_id }}][]" value="{{ $selected->id }}" />
                            </td>
                            <td>{{ $selected->name }} </td>
                            <td><input type="text" name="oprice[{{ $selected->group_id }}][]" value="{{ @$prices[$t] }}" class="form-control" style="width: 100px" /></td>
                            <td><input type="text" name="odiscount[{{ $selected->group_id }}][]" value="{{ @$discounts[$t] }}" class="form-control" style="width: 100px" /></td>
                            <td class="opt-photo"><input type="file" name="optphoto[{{ $selected->group_id }}][]">
                                <span class="opt-photo-thumb">
                                    @if (@$photos[$t])
                                     <img src="{{ asset('storage/'.get_thumbnail($photos[$t], 'small')) }}">
                                    @endif
                                </span>
                                <input type="hidden" name="optloadedphoto[{{ $selected->group_id }}][]" value="{{ @$photos[$t] }}" />

                            </td>
                            <td><a class="voyager-trash" href="javascript:;" onclick="$(this).parent().parent().remove()"></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
</div>
&nbsp;&nbsp;<a href="javascript:;" class="add-option-table btn btn-dark" onClick="addGroup()">Add group</a>


<table class="options-table-content options-table-content-clonable">
    <thead>
    <tr>
        <th>Code</th>
        <th>Value</th>
        <th><a href="javascript:;" onclick="removeOption()">Remove all</a></th>
    </tr>
    </thead>
    <tbody>
    <tr class="clonable">
        <td>
            <input type="text" class="form-control code" aria-describedby="basic-addon3">
        </td>
        <td>
            <input type="text" class="form-control value" aria-describedby="basic-addon3">
        </td>
        <td style="padding-left: 10px;">
            <a class="voyager-plus" href="javascript:;"
               onclick="$(this).parents('.options-table-content').find('tbody').append($(this).parents('.options-table-content').find('tr.clonable').clone().removeClass('clonable'));">
            </a>
            <a class="voyager-trash" href="javascript:;" onclick="$(this).parent().parent().remove()"></a>
        </td>
    </tr>
    </tbody>
</table>

