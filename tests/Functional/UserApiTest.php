<?php

namespace Tests\Functional;

class UserApiTest extends BaseTestCase
{
    /**
     * @var \Application\Models\User
     */
    public $user;

    public function setUp()
    {
        parent::setUp();

        $this->runSeed('UserSeeder');

        $this->user = $this->db->run("SELECT * FROM users ORDER BY id DESC LIMIT 1")[0];
    }

    public function testApiCanReturnAResponse()
    {
        $res = $this
            ->actingAs($this->user->email)
            ->get('/api/users');

        $body = json_decode((string)$res->getBody());

        $this->assertTrue(is_object($body));
        $this->assertEquals(5, count($body->data));
    }
}
