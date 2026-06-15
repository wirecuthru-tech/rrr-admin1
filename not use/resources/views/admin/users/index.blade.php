<!DOCTYPE html>
<html>
<head>
    <title>Users - MongoDB Atlas</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th { background: #4CAF50; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f1f1f1; }
        .btn-delete { background: red; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        .alert { background: #d4edda; padding: 10px; margin-bottom: 15px; border-left: 4px solid #28a745; }
    </style>
</head>
<body>
    <h2>?? Saare Users - MongoDB Atlas</h2>
    
    @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif
    
    <table>
        <tr>
            <th>#</th>
            <th>_id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        @forelse($users as $key => $user)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $user->_id }}</td>
            <td>{{ $user->name ?? 'N/A' }}</td>
            <td>{{ $user->email ?? 'N/A' }}</td>
            <td>{{ $user->phone ?? 'N/A' }}</td>
            <td>{{ $user->online_status ?? 'offline' }}</td>
            <td>
                <form action="{{ route('admin.users.delete', $user->_id) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete" onclick="return confirm('Delete karega?')">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center">Atlas me koi user nahi mila</td></tr>
        @endforelse
    </table>
</body>
</html>