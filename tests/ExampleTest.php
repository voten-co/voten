<?php


class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->get('/about')
             ->assertSee('Social Bookmarking For The 21st Century');
    }
}
