<div>
    <h1 class="text-xl font-semibold mb-3">OPD/IPD Dashboard</h1>
    <div class="flex gap-2 mb-3">
        <input type="date" wire:model="from" class="border px-2 py-1 rounded">
        <input type="date" wire:model="to" class="border px-2 py-1 rounded">
        <input type="text" placeholder="Department" wire:model="department" class="border px-2 py-1 rounded">
        <input type="text" placeholder="Doctor ID" wire:model="doctor" class="border px-2 py-1 rounded">
        <input type="text" placeholder="Location ID" wire:model="location" class="border px-2 py-1 rounded">
    </div>
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="p-3 border rounded">OPD in range: <strong>{{ $opd }}</strong></div>
        <div class="p-3 border rounded">IPD in range: <strong>{{ $ipd }}</strong></div>
    </div>
    <canvas id="visitsChart" height="120"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const data = {
            labels: @json($daily->pluck('d')),
            datasets: [{label: 'Visits', data: @json($daily->pluck('c'))}]
        };
        const ctx = document.getElementById('visitsChart').getContext('2d');
        new Chart(ctx, {type: 'line', data});
    </script>
    <h3 class="mt-6 font-semibold">Per-Department (Top 10)</h3>
    <canvas id="deptChart" height="120"></canvas>
    <script>
        const deptLabels = @json($dept->pluck('d'));
        const deptData = @json($dept->pluck('c'));
        new Chart(document.getElementById('deptChart').getContext('2d'), {type:'bar', data: {labels: deptLabels, datasets:[{label:'Visits', data: deptData}]} });
    </script>
</div>
