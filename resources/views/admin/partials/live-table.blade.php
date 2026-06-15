@php
    $title = $title ?? 'Live';
    $collection = $collection ?? ($deleteCollection ?? 'items');
    $items = $items ?? ($rows ?? collect());
    $fields = $fields ?? [];
    $columns = $columns ?? [];
@endphp
<div class="card">
    <div class="card-body">
        <h3 class="mb-3">{{ $title }} Management</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(!empty($fields))
        <form method="POST" action="{{ route('admin.live.store', $collection) }}" class="row g-2 mb-4">
            @csrf
            @foreach($fields as $key => $label)
                <div class="col-md-3"><input type="text" name="{{ $key }}" class="form-control" placeholder="{{ $label }}"></div>
            @endforeach
            <div class="col-md-2"><select name="status" class="form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
        </form>
        @endif

        @php
            $first = collect($items)->first();
            $autoColumns = [];
            if (empty($columns) && $first) {
                $firstArr = is_array($first) ? $first : json_decode(json_encode($first), true);
                $autoColumns = array_slice(array_filter(array_keys($firstArr), fn($k) => $k !== '_id'), 0, 8);
                $columns = array_combine($autoColumns, array_map(fn($k) => ucwords(str_replace('_',' ', $k)), $autoColumns));
            }
        @endphp

        <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark"><tr><th>ID</th>@foreach($columns as $key => $label)<th>{{ $label }}</th>@endforeach<th>Status</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($items as $item)
                @php
                    $arr = is_array($item) ? $item : json_decode(json_encode($item), true);
                    $itemId = $arr['_id']['$oid'] ?? $arr['_id'] ?? $arr['id'] ?? '';
                @endphp
                <tr>
                    <td><code>{{ is_array($itemId) ? json_encode($itemId) : $itemId }}</code></td>
                    @foreach($columns as $key => $label)
                        @php $val = $arr[$key] ?? '-'; @endphp
                        <td>{{ is_array($val) ? json_encode($val) : $val }}</td>
                    @endforeach
                    <td><span class="badge bg-{{ (($arr['status'] ?? 'active') == 'active') ? 'success' : 'secondary' }}">{{ ucfirst($arr['status'] ?? 'active') }}</span></td>
                    <td>
                        @if($itemId)
                            <form method="POST" action="{{ route('admin.live.delete', [$collection, is_array($itemId) ? ($itemId['$oid'] ?? '') : $itemId]) }}" onsubmit="return confirm('Delete karna hai?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        @else <span class="text-muted">No ID</span> @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ count($columns) + 3 }}" class="text-center">Data nahi mila</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
