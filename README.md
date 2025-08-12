# Laravel CSV Import/Export using Maatwebsite/Excel

## 1. Install maatwebsite/excel
    composer require maatwebsite/excel
        
## 2. Create the posts table migration
##### Bash
    php artisan make:model Post -m
##### posts table
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });
    }
##### Post Model
    class Post extends Model
    {
        protected $fillable = ['title', 'description'];
    }
##### Bash
    php artisan migrate

## 3. Create Import Class
    php artisan make:import PostsImport --model=Post
##### PostImport class in App\Imports
    namespace App\Imports;
    
    use App\Models\Post;
    use Maatwebsite\Excel\Concerns\ToModel;
    use Maatwebsite\Excel\Concerns\WithHeadingRow;
    
    class PostsImport implements ToModel, WithHeadingRow
    {
        public function model(array $row)
        {
            return new Post([
                'title'       => $row['title'],
                'description' => $row['description'],
            ]);
        }
    }

## 4.  Create Export Class (Optional)

    php artisan make:export PostsExport --model=Post
##### PostExport Class in App\Exports
    namespace App\Exports;
    
    use App\Models\Post;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    
    class PostsExport implements FromCollection, WithHeadings
    {
        public function collection()
        {
            return Post::select('title', 'description')->get();
        }
    
        public function headings(): array
        {
            return ['title', 'description'];
        }
    }


## 5.  Create Controller
      php artisan make:controller PostCsvController
##### Controller
        class PostCsvController extends Controller
        {
            public function showForm()
            {
                return view('posts.csv');
            }
        
            public function import(Request $request)
            {
                $request->validate([
                    'csv_file' => 'required|mimes:csv,txt'
                ]);
        
                Excel::import(new PostsImport, $request->file('csv_file'));
        
                return back()->with('success', 'Posts imported successfully.');
            }
        
            public function export() // optional 
            {
                return Excel::download(new PostsExport, 'posts.csv');
            }
        }

## 6. Create Blade View
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

## 7. Add Routes
    use App\Http\Controllers\PostCsvController;
    
    Route::get('posts/csv', [PostCsvController::class, 'showForm'])->name('posts.csv.form');
    Route::post('posts/import', [PostCsvController::class, 'import'])->name('posts.import');
    Route::get('posts/export', [PostCsvController::class, 'export'])->name('posts.export');










