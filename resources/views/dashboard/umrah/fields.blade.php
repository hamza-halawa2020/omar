<div class="row g-3 mb-3">
    @foreach($fields as $field)
        @php
            $name = $field[0];
            $label = $field[1];
            $type = $field[2] ?? 'text';
            $value = $field[3] ?? '';
            $required = $field[4] ?? false;
            $value = $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value;
        @endphp
        <div class="col-md-{{ $type === 'textarea' ? '12' : '6' }}">
            <label class="form-label" for="{{ $formKey ?? 'form' }}-{{ $name }}">{{ $label }} @if($required)<span
            aria-hidden="true">*</span>@endif</label>
            @if($type === 'select')
                <select id="{{ $formKey ?? 'form' }}-{{ $name }}" name="{{ $name }}" class="form-select" @required($required)>
                    @foreach(($field[5] ?? []) as $key => $option)
                    <option value="{{ $key }}" @selected((string) $value === (string) $key)>{{ $option }}</option>@endforeach
                </select>
            @elseif($type === 'textarea')
                <textarea id="{{ $formKey ?? 'form' }}-{{ $name }}" name="{{ $name }}" class="form-control" rows="3"
                    @required($required)>{{ $value }}</textarea>
            @else
                <input id="{{ $formKey ?? 'form' }}-{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}"
                    class="form-control" @required($required) @if($type === 'number') min="0"
                    step="{{ in_array($name, ['amount', 'total_amount', 'agreed_amount']) ? '0.01' : '1' }}" inputmode="{{ in_array($name, ['amount', 'total_amount', 'agreed_amount']) ? 'decimal' : 'numeric' }}" @endif>
            @endif
        </div>
    @endforeach
</div>
