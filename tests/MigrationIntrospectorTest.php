<?php

namespace RendyAFS\RenKit\Tests;

use Illuminate\Support\Facades\File;
use RendyAFS\RenKit\Support\MigrationIntrospector;

class MigrationIntrospectorTest extends TestCase
{
    protected MigrationIntrospector $introspector;
    protected string $tempMigrationPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->introspector = new MigrationIntrospector();
        $this->tempMigrationPath = database_path('migrations/2026_01_01_000000_create_products_table.php');
        File::ensureDirectoryExists(dirname($this->tempMigrationPath));
    }

    protected function tearDown(): void
    {
        if (File::exists($this->tempMigrationPath)) {
            File::delete($this->tempMigrationPath);
        }
        parent::tearDown();
    }

    public function test_it_inspects_columns_from_migration_content(): void
    {
        $migrationContent = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->foreignId('category_id')->nullable();
            $table->timestamps();
        });
    }
};
PHP;

        File::put($this->tempMigrationPath, $migrationContent);

        $columns = $this->introspector->inspectFromMigrationFile('products');

        $this->assertCount(5, $columns);
        $names = array_column($columns, 'name');
        $this->assertEquals(['name', 'description', 'price', 'is_active', 'category_id'], $names);

        $formHtml = $this->introspector->generateFormBlade($columns, 'product', 'Product');
        $this->assertStringContainsString('id="product-name"', $formHtml);
        $this->assertStringContainsString('id="product-description"', $formHtml);
        $this->assertStringContainsString('id="product-price"', $formHtml);
        $this->assertStringContainsString('id="product-is-active"', $formHtml);
        $this->assertStringContainsString('<x-select id="product-category_id"', $formHtml);

        $tableHeaders = $this->introspector->generateTableHeaders($columns);
        $this->assertStringContainsString('Name', $tableHeaders);
        $this->assertStringContainsString('Description', $tableHeaders);

        $fillable = $this->introspector->generateModelFillable($columns);
        $this->assertStringContainsString("'name'", $fillable);
        $this->assertStringContainsString("'price'", $fillable);

        $rules = $this->introspector->generateValidationRules($columns, 'products');
        $this->assertStringContainsString("'name' => ['required', 'string', 'max:255']", $rules);
        $this->assertStringContainsString("'price' => ['required', 'numeric']", $rules);
    }

    public function test_it_handles_empty_migration_gracefully(): void
    {
        $columns = [];
        $formHtml = $this->introspector->generateFormBlade($columns, 'empty_item', 'EmptyItem');
        $this->assertStringContainsString('Belum ada kolom custom pada migration terdeteksi', $formHtml);

        $tableHeaders = $this->introspector->generateTableHeaders($columns);
        $this->assertStringContainsString('ID', $tableHeaders);
    }
}
