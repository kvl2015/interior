@extends('site')

@section('content')
    @include('partials.title',['title' => __('page.all_brands'), 'isH1' => @$data['isH1']])

    @include('partials.breadcrumbs',['breadcrumbs' => $data['breadcrumbs']])
    
    <div id="data-content">
        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#list">List view</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#detail">Detailed view</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="list">
                        <input type="text" class="form-control" />
                        <div class="alfabet">
                            <div class="alfa-filter">
                                <ul class="nav">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="javascript:;" onClick="changeFilterLetter('A')">A-E</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:;" onClick="changeFilterLetter('F')">F-J</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:;" onClick="changeFilterLetter('K')">K-P</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:;" onClick="changeFilterLetter('P')">Q-U</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:;" onClick="changeFilterLetter('V')">V-Z</a>
                                    </li>
                                </ul>
                                <span><a class="nav-link" href="javascript:;" onClick="changeFilterLetter('all')">All brands</a></span>
                            </div> 
                            <div class="clearfix"></div>   
                            @php 
                                $letter = "";
                                $arrSepLetter = array('F', 'K', 'P', 'V');
                            @endphp
                            <ul class="brands-by-letter">
                                <li class="letter-filter__interval-item" data-letter="A">
                                    <ul class="p-0 reset-markers row">
                                        @foreach ($data['brands'] as $brand)
                                            @if ($letter != ucfirst(substr($brand->name, 0, 1)))
                                                @php $letter = ucfirst(substr($brand->name, 0, 1)); @endphp
                                                @if (in_array($letter, $arrSepLetter))
                                                    </ul></li><li class="letter-filter__interval-item" data-letter="{{$letter}}"><ul class="p-0 reset-markers row">
                                                @endif
                                                <li class="col-12 letter-filter__separator-letter">{{$letter}}</li>
                                            @endif
                                            <li class="letter-filter__interval-letter-sub-item col-xs-6 col-md-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <a href="{{ url_local('brand', $brand->slug)}}">
                                                        <span>{{$brand->name}}</span>
                                                    </a>
                                                    </div>
                                                </div>
                                            </li>                                            
                                            @php $letter = ucfirst(substr($brand->name, 0, 1)); @endphp
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="detail">
                        <input type="text" class="form-control" />
                        <div class="alfabet">
                            <div class="alfa-filter alfa-filter-detail">
                                <ul class="nav">
                                    @foreach (range('A', 'Z') as $alphabet)
                                    <li class="nav-item">
                                        <a class="nav-link active" href="javascript:;" onClick="changeFilterLetter('A')">{{$alphabet}}</a>
                                    </li>
                                    @endforeach
                                </ul>
                                <span><a class="nav-link" href="javascript:;" onClick="changeFilterLetter('all')">All brands</a></span>
                            </div> 
                            <div class="clearfix"></div>
                                <ul class="letter-filter__single reset-markers p-0 m-0">
                                    @foreach ($data['brands'] as $brand)
                                        <li class="letter-filter__single-item">
                                            <div class="row d-flex align-items-center">
                                                <div class="col-12 col-sm-4">
                                                    <a href="{{ url_local('brand', $brand->slug)}}">
                                                        <img class="letter-filter__image" src="{{ asset('storage/'.str_replace('\\', '/', $brand->logo)) }}" alt="{{ $brand->name}}">
                                                    </a>
                                                </div>
                                                <div class="col-12 col-sm-8">
                                                    <span class="letter-filter__separator">{{ $brand->name}}</span>
                                                    <a href="{{ url_local('brand', $brand->slug)}}">Shop now</a>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

