<a href="javascript:;" class="yellow-btn filter-show-hide" onClick="$('.product-form-filters').toggle()">Open filter</a>
<form class="product-form-filters">
    <div class="row row-filter">
        <div class="col-lg-3 filter-option">
            <select class="select-css">
                <option>Choose Brand</option>
                @foreach (\App\Brand::orderBy('name')->get() as $brand) {
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 filter-option">
            <select class="select-css">
                <option>Choose Designer</option>
                @foreach (\App\Designer::orderBy('name')->get() as $designer) {
                    <option value="{{ $designer->id }}">{{ $designer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 filter-option">
            <select class="select-css">
                <option>Choose Room</option>
                @foreach (\App\Room::orderBy('name')->get() as $room) {
                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 filter-option">
            <select class="select-css">
                <option>Choose Style</option>
                @foreach (\App\Style::orderBy('name')->get() as $style) {
                    <option value="{{ $style->id }}">{{ $style->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row row-filter">
        <div class="col-lg-3 filter-option">
            <select class="select-css">
                <option>Choose Color</option>
            </select>
        </div>
        <div class="col-lg-3 filter-option">
            <select class="select-css">
                <option>Light sources</option>
            </select>
        </div>
        <div class="col-lg-3 filter-option">
            <select class="select-css">
                <option>Price range</option>
            </select>
        </div>
        <div class="col-lg-3 filter-option">
            <div class="custom-control custom-checkbox filters-status-reset">
                <input type="checkbox" class="custom-control-input" id="customCheck1">
                <label class="custom-control-label" for="customCheck1">In stock</label>
                <a href="#" class="reset-filters">Resert filters</a>
            </div>
        </div>
    </div>
</form>
