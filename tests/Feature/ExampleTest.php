<?php

it('returns a successful response', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
