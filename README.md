# Laravel CSV Import/Export using Maatwebsite/Excel

## 1. Install maatwebsite/excel
    composer require maatwebsite/excel
        
## 2. Create the posts table migration

    php artisan make:model Post -m

    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });
    }

    class Post extends Model
    {
        protected $fillable = ['title', 'description'];
    }

    php artisan migrate

## 4. Create Import Class
    php artisan make:import PostsImport --model=Post

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

## 4.5.  Create Export Class (Optional)

    php artisan make:export PostsExport --model=Post

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
        <html>
        <head>
            <title>CSV Import/Export</title>
        </head>
        <body>
            @if(session('success'))
                <p style="color: green">{{ session('success') }}</p>
            @endif
        
            <h3>Import CSV</h3>
            <form action="{{ route('posts.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="csv_file" accept=".csv">
                <button type="submit">Import</button>
            </form>
        
            <h3>Export CSV</h3>
            <a href="{{ route('posts.export') }}">Download CSV</a>
        </body>
        </html>

## 7. Add Routes
    use App\Http\Controllers\PostCsvController;
    
    Route::get('posts/csv', [PostCsvController::class, 'showForm'])->name('posts.csv.form');
    Route::post('posts/import', [PostCsvController::class, 'import'])->name('posts.import');
    Route::get('posts/export', [PostCsvController::class, 'export'])->name('posts.export');










