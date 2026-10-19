<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFeatureTest extends TestCase
{
    use RefreshDatabase;


    public function test_contact_form_page_displays_categories_and_tags_and_thanks_page_is_accessible(): void
    {
        $category = Category::factory()->create(['content' => 'テストカテゴリ']);
        $tag = Tag::factory()->create(['name' => 'テストタグ']);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewHasAll(['categories', 'tags']);
        $response->assertSee('テストカテゴリ');
        $response->assertSee('テストタグ');


        $thanksResponse = $this->get('/thanks');
        $thanksResponse->assertStatus(200);
    }


    public function test_guest_is_redirected_to_login_and_admin_can_access_dashboard(): void
    {

        $response = $this->get('/admin');
        $response->assertRedirect('/login');


        $user = User::factory()->create();
        $authenticatedResponse = $this->actingAs($user)->get('/admin');
        $authenticatedResponse->assertStatus(200);
    }


    public function test_contact_submission_and_validation(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $validData = [
            'first_name'  => '山田',
            'last_name'   => '太郎',
            'gender'      => '1',
            'email'       => 'yamada@example.com',
            'tel'         => '09012345678',
            'address'     => '東京都新宿区',
            'category_id' => $category->id,
            'detail'      => 'お問い合わせの詳細内容です。',
            'tag_ids'     => [$tag->id],
        ];

        $invalidData = $validData;
        $invalidData['first_name'] = '';

        $invalidResponse = $this->post('/contacts', $invalidData);
        $invalidResponse->assertSessionHasErrors(['first_name']);

        $response = $this->post('/contacts', $validData);
        $response->assertRedirect(route('contact.thanks'));

        $this->assertDatabaseHas('contacts', [
            'first_name' => '山田',
            'email'      => 'yamada@example.com',
        ]);

        $contact = Contact::first();
        $this->assertCount(1, $contact->tags);
        $this->assertEquals($tag->id, $contact->tags->first()->id);
    }

    


    public function test_admin_can_view_contact_detail_with_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['content' => '特別質問']);
        $contact = Contact::factory()->create([
            'first_name'  => '田中',
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.show');
        $response->assertSee('田中');
        $response->assertSee('特別質問'); // リレーションのカテゴリ内容が表示されているか確認
    }


    public function test_admin_can_delete_contact(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create();


        $this->assertDatabaseHas('contacts', ['id' => $contact->id]);

        $response = $this->actingAs($user)->delete("/admin/contacts/{$contact->id}");

        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
