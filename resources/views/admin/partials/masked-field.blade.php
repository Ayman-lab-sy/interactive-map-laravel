@php
    $original = $value ?? '';
    $masked = '—';

    if (!empty($original)) {
        switch ($type ?? 'text') {
            case 'email':
                $parts = explode('@', $original);
                if (count($parts) === 2) {
                    $name = substr($parts[0], 0, 2) . str_repeat('*', max(strlen($parts[0]) - 2, 3));
                    $masked = $name . '@' . $parts[1];
                }
                break;

            case 'name':
                $masked = mb_substr($original, 0, 2) . str_repeat('*', max(mb_strlen($original) - 2, 3));
                break;

            case 'phone':
                $masked = substr($original, 0, 3) . str_repeat('*', max(strlen($original) - 5, 3)) . substr($original, -2);
                break;

            default:
                $masked = str_repeat('*', 8);
        }
    }
@endphp

<div class="masked-wrapper">
    <span class="masked-value">{{ $masked }}</span>
    <button type="button"
            class="reveal-btn"
            onclick="this.previousElementSibling.innerText='{{ $original }}'; this.remove();">
        إظهار
    </button>
</div>
