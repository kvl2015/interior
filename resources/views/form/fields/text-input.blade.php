<div class="floating-label">
    <input name="{{ $name }}" id="{{ $name }}" value="{{ $value }}" class="floating-input" placeholder=" " type="{{ $type }}" />
    <label class="{{ $required ? 'required' : '' }}">{{ $label }}</label>
    <span class="error" id="error-{{ $name }}"></span>
</div>
