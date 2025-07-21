{{-- <div class="bg-gray-200">
    <div>
        <div wire:poll.1s class="text-center"> 
            {{ $currentDateTime }}
        </div>
    </div> --}}

<div class="p-4 h-full">
    {{-- Success is as dangerous as failure. --}}
    <div class="text-center" id="currentDateTime" style="color: red;">
        <!-- Current date and time will be updated by JavaScript -->
    </div>
</div>

<script>
    // Function to update date and time
    function updateDateTime() {
        var currentDate = new Date();
        var options = { month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        var currentDateTime = currentDate.toLocaleString('en-US', options);
        document.getElementById('currentDateTime').innerText = currentDateTime;
    }

    // Update date and time initially
    updateDateTime();

    // Update date and time every second
    setInterval(updateDateTime, 1000);
</script>