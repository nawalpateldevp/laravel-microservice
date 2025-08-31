<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Author;

class AuthorTest extends \Tests\TestCase
{
    use DatabaseMigrations;

    public function testIndexAuthors()
    {
        Author::factory()->count(3)->create();
        $response = $this->get('/authors');
        $response->seeStatusCode(200)
                 ->seeJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'gender', 'country']
                     ]
                 ]);
    }

    public function testShowAuthor()
    {
        $author = Author::factory()->create();
        $response = $this->get("/authors/{$author->id}");
        $response->seeStatusCode(200)
                 ->seeJsonContains(['id' => $author->id]);
    }

    public function testCreateAuthor()
    {
        $data = [
            'name' => 'Test Author',
            'gender' => 'male',
            'country' => 'Testland'
        ];
        $response = $this->post('/authors', $data);
        $response->seeStatusCode(201)
                 ->seeJsonContains($data);
    }

    public function testUpdateAuthor()
    {
        $author = Author::factory()->create();
        $data = ['name' => 'Updated Name'];
        $response = $this->put("/authors/{$author->id}", $data);
        $response->seeStatusCode(200)
                 ->seeJsonContains($data);
    }

    public function testDeleteAuthor()
    {
        $author = Author::factory()->create();
        $response = $this->delete("/authors/{$author->id}");
        $response->seeStatusCode(200)
                 ->seeJsonContains(['message' => 'Author deleted']);
    }
}
