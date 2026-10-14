<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Two independent things about the shared admin image endpoints
 * (AdminImageController, HandlesImageUploads):
 *
 *  - Uploads are decoded and re-encoded via GD, not just MIME-sniffed, so a
 *    file that isn't actually a valid image can't be smuggled onto disk
 *    even if it's renamed with an image extension.
 *  - Deleting an image now requires the path to actually belong to the
 *    record being edited — previously any admin with delete permission on
 *    ANY one image field could delete ANY image on the site by naming its
 *    path, regardless of which record it belonged to.
 */
class ImageSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    private function mainAdmin(): User
    {
        return User::where('email', 'admin@example.com')->firstOrFail();
    }

    public function test_a_non_image_file_disguised_with_an_image_extension_is_rejected(): void
    {
        $categoryId = DB::table('nakheel_categories')->value('id');

        $fakePhp = UploadedFile::fake()->createWithContent('shell.jpg', '<?php echo "not an image"; ?>');

        $response = $this->actingAs($this->mainAdmin())->post("/admin/images/categories/{$categoryId}", [
            'field' => 'image',
            'image' => $fakePhp,
        ]);

        $response->assertStatus(422);
    }

    public function test_a_genuine_image_upload_succeeds_and_is_reencoded(): void
    {
        $categoryId = DB::table('nakheel_categories')->value('id');
        $realImage = UploadedFile::fake()->image('photo.jpg', 10, 10);

        $response = $this->actingAs($this->mainAdmin())->post("/admin/images/categories/{$categoryId}", [
            'field' => 'image',
            'image' => $realImage,
        ]);

        $response->assertStatus(201);
        $path = $response->json('path');
        Storage::disk('public')->assertExists($path);
        $this->assertStringStartsWith('nakheel-images/', $path);
    }

    public function test_deleting_an_image_that_belongs_to_a_different_record_is_rejected(): void
    {
        $now = now();
        $recordA = DB::table('nakheel_categories')->insertGetId([
            'title' => 'Record A', 'image' => 'nakheel-images/record-a-image.jpg',
            'sort_order' => 100, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $recordB = DB::table('nakheel_categories')->insertGetId([
            'title' => 'Record B', 'image' => 'nakheel-images/record-b-image.jpg',
            'sort_order' => 101, 'created_at' => $now, 'updated_at' => $now,
        ]);
        Storage::disk('public')->put('nakheel-images/record-b-image.jpg', 'fake image bytes');

        // Editing record A, but naming record B's image path.
        $response = $this->actingAs($this->mainAdmin())->delete("/admin/images/categories/{$recordA}", [
            'field' => 'image',
            'path' => 'nakheel-images/record-b-image.jpg',
        ]);

        $response->assertStatus(404);

        // Record B's file and DB reference must be completely untouched.
        Storage::disk('public')->assertExists('nakheel-images/record-b-image.jpg');
        $this->assertSame('nakheel-images/record-b-image.jpg', DB::table('nakheel_categories')->where('id', $recordB)->value('image'));
    }

    public function test_deleting_an_images_own_path_still_works(): void
    {
        $now = now();
        $recordId = DB::table('nakheel_categories')->insertGetId([
            'title' => 'Own Record', 'image' => 'nakheel-images/own-image.jpg',
            'sort_order' => 102, 'created_at' => $now, 'updated_at' => $now,
        ]);
        Storage::disk('public')->put('nakheel-images/own-image.jpg', 'fake image bytes');

        $response = $this->actingAs($this->mainAdmin())->delete("/admin/images/categories/{$recordId}", [
            'field' => 'image',
            'path' => 'nakheel-images/own-image.jpg',
        ]);

        $response->assertNoContent();
        Storage::disk('public')->assertMissing('nakheel-images/own-image.jpg');
        $this->assertNull(DB::table('nakheel_categories')->where('id', $recordId)->value('image'));
    }
}
