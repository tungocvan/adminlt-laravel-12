<div>
    <table class="table-bordered table-hover table">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Tiêu đề</th>
                <th>Người tạo</th>
                <th>Ưu tiên</th>
                <th>Trạng thái</th>
                <th width="160">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($proposals as $proposal)
                <tr>
                    <td>{{ $proposal->id }}</td>
                    <td>{{ $proposal->title }}</td>
                    <td>{{ $proposal->creator->name ?? '-' }}</td>
                    <td>
                        <span
                            class="badge badge-{{ $proposal->priority === 'HIGH' ? 'danger' : ($proposal->priority === 'MEDIUM' ? 'warning' : 'secondary') }}">
                            {{ $proposal->priority }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $proposal->status }}</span>
                    </td>
                    <td>
                        <a href="#" class="btn btn-sm btn-primary">Xem</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>