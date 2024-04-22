<div
    id="{{ $record->getKey() }}"
    wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
    class="record bg-white dark:bg-gray-700 rounded-lg px-4 py-2 cursor-grab font-medium text-gray-600 dark:text-gray-200"
    @if($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}) < 3)
        x-data
        x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-700')
            }, 3000)
        "
    @endif
>
    <div class="flex justify-between">
        <div class="text-sm text-left">{{ $record->{static::$recordTitleAttribute} }}  <x-heroicon-s-star class="inline-block text-primary-500 w-4 h-4"/> </div>
        <div class="text-xs text-right">{{ $record->created_at->format('M d Y')}}</div>
    </div>

    <div class="text-xs">{{ $record->mobile ? $record->mobile : 'N/A' }}</div>
    <div class="text-xs">{{ $record->email ? $record->email : 'N/A' }}</div>

    <br>
    <div class="flex -space-x-2 pt-4">
        @php
            $interviewers = ['John', 'Alice', 'Bob'];
        @endphp
        <div class="flex hover:-space-x-1 -space-x-3">
            {{-- @foreach($record['interviewers'] as $interviewer) --}}
            @foreach($interviewers as $interviewer)
                <div class="w-8 h-8 transition-all rounded-full bg-gray-200 border-2 border-white">
                    {{-- if profile is true --}}
                    
                    {{-- else default--}}
                    <x-heroicon-c-user-circle/>
                </div>
            @endforeach
        </div>

    </div>

    <div class="mt-2 relative">
        <div class="absolute h-3 bg-primary-500 rounded-full" style="width:{{$record->progress}}%"></div>
        <div class="h-3 bg-gray-200 rounded-full"></div>
    </div>
</div>
