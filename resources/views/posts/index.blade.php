<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <h3 class="mb-3">Import CSV with Maatwebsite Package PHP Laravel</h3>
        <form action="{{ route('posts.import') }}" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">CSV File</label>
                <input type="file" name="csv_file" accept=".csv" class="form-control" id="file">
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>

        <div class="card p-4 shadow-sm mt-4">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: none;">
                <h3 class="mb-3">Posts List</h3>
                <a href="{{ route('posts.export') }}" class="btn btn-success mb-3">Export to CSV</a>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posts as $post)
                            <tr>
                                <td>{{ $post->id }}</td>
                                <td>{{ $post->title }}</td>
                                <td>{{ $post->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
        
                </table>
            </div>
        </div>

    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</html>