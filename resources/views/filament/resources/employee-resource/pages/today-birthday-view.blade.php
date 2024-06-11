<div x-data="birthdayComponent()" class="relative">
    <div class="flex items-center justify-center bg-cover" style="background-image: url('{{ asset('/images/birthday_card.jpg') }}'); height: 450px;">
        <template x-if="records.length > 0">
            <div class="text-center max-w-lg w-full">
                <div class="mb-4">
                    <template x-if="currentRecord.picture">
                        <img :src="'{{ asset('storage/') }}' + '/' + currentRecord.picture" alt="Employee Picture" class="mx-auto w-32 h-32 rounded-full">
                    </template>
                    <template x-if="!currentRecord.picture">
                        <div class="mx-auto w-32 h-32 rounded-full bg-gray-200"></div>
                    </template>
                </div>
                <h1 class="text-3xl font-bold text-primary-600">Happy Birthday</h1>
                <p class="text-xl text-gray-700" x-text="currentRecord.name"></p>
                <div class="flex items-center justify-center"> 
                    <img src="{{ asset('/images/cupcake.png') }}" alt="Birthday Sticker" class="w-20 h-20 ml-2"> <!-- Updated -->
                </div>
                
                <p class="text-gray-500" x-text="currentRecord.name + ' celebrates ' + (currentRecord.gender === 'M' ? 'his' : 'her') + ' birthday today'"></p>
            </div>
        </template>
        <template x-if="records.length === 0">
            <p class="text-2xl text-gray-700">No birthdays today.</p>
        </template>
    
        <!-- Adjusted button container positioning -->
        <div class="absolute top-1/2 transform -translate-y-1/2 flex justify-between w-full px-4 z-50">
            <button @click="previousRecord" class="text-primary-600 bg-secondary-600 p-4 rounded-full hover:bg-primary-dark">&lt;</button>
            <button @click="nextRecord" class="text-primary-600 bg-secondary-600 p-4 rounded-full hover:bg-primary-dark">&gt;</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function birthdayComponent() {
        return {
            records: @json($records),
            currentRecordIndex: 0,
            get currentRecord() {
                return this.records.length > 0 ? this.records[this.currentRecordIndex] : {};
            },
            nextRecord() {
                if (this.records.length > 0) {
                    this.currentRecordIndex = (this.currentRecordIndex + 1) % this.records.length;
                }
            },
            previousRecord() {
                if (this.records.length > 0) {
                    this.currentRecordIndex = (this.currentRecordIndex - 1 + this.records.length) % this.records.length;
                }
            },
            init() {
                setInterval(() => {
                    this.nextRecord();
                }, 5000);
            }
        }
    }

    function initializeBirthdayComponent() {
        Alpine.data('birthdayComponent', birthdayComponent);
        // Alpine.initTree(document.body);
    }

    document.addEventListener('alpine:init', () => {
        if (!window.birthdayComponentInitialized) {
            initializeBirthdayComponent();
            window.birthdayComponentInitialized = true;
        }
    });

    document.addEventListener('livewire:navigated', () => {
        if (!window.birthdayComponentInitialized) {
            initializeBirthdayComponent();
            window.birthdayComponentInitialized = true;
        }
    });
</script>
@endpush
