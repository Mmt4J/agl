{{--
    resources/views/components/forms/checkbox.blade.php
    ------------------------------------------------------------------
    Reusable labeled checkbox, styled with the Copper accent color.
    Direct replacement for <flux:checkbox>.

    USAGE
        <x-forms.checkbox name="remember" label="Remember me" :checked="old('remember')" />

    PROPS
        name      string          required
        label     string          required
        checked   bool|null false Pre-checked state, e.g. old('remember')
--}}
@props(['name', 'label', 'checked' => false])

{{--
    Wrapping the <input> AND its label text in one <label> means
    clicking the word "Remember me" toggles the box too, not just the
    3-pixel square - a basic accessibility/usability win that's easy to
    forget when hand-rolling checkboxes.
--}}
<label for="{{ $name }}"
    class="flex items-center gap-2 text-sm text-ink-700 dark:text-linen-200 cursor-pointer select-none">
    <input id="{{ $name }}" type="checkbox" name="{{ $name }}" value="1" {{-- @checked() is a Blade directive shorthand for
             {{ $checked ? 'checked' : '' }} - prints the "checked"
             HTML attribute only when the condition is true. --}}
        @checked($checked) {{--
            accent-color is a *plain CSS property* (not a Tailwind
            trick or a custom SVG hack) that every modern browser uses
            to reskin its own native checkbox/radio rendering. That's
            why this component needs far less markup than input.blade.php -
            there's no custom box to draw, just one color to set.
        --}}
        {{ $attributes->merge([
            'class' =>
                'h-4 w-4 rounded border-ink-300 dark:border-ink-600 accent-copper-500 focus:ring-2 focus:ring-copper-400',
        ]) }} />
    {{ $label }}
</label>
