@if(!empty($table))
    <x-moonshine::divider />

    <x-moonshine::title class="mb-6">
        {{ $label }}
    </x-moonshine::title>

    <div class="pt-2 mb-6">
        @lang('moonshine-changelog::ui.last_changes')
    </div>

    {{ $table->render() }}
@endif
