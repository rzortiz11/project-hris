<div>
    <form wire:submit="save">
        {{ $this->form }}

        <br>
        <button type="submit">
            Update
        </button>
    </form>

    <x-filament-actions::modals />
</div>
