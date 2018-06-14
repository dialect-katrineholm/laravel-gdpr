<?php

namespace Dialect\Gdpr;

/**
 * Feature tests asserting that you can decrypt complete models or collection of models.
 *
 * @author  r4z0rFr <erwankreutz@gmail.com>
 * @license The MIT License
 */
class EncryptedTest extends TestCase
{
    /** @test */
    public function test_if_it_decrypt_a_complete_model_to_an_array()
    {
        $originalValues = [
            'name'          => 'TestUser',
            'email'         => 'test@email.com',
            'adress'        => '30 test adress',
        ];

        $decryptedArray = $this->customer->decryptToArray();

        array_splice($decryptedArray, 3, 3);

        $this->assertEquals($originalValues, $decryptedArray);
    }

    /** @test */
    public function test_if_it_decrypt_a_collection_of_models()
    {
        $decryptedCollection = $this->customer->decryptToCollection();

        $this->assertEquals('TestUser', $decryptedCollection->name);
        $this->assertEquals('test@email.com', $decryptedCollection->email);
        $this->assertEquals('30 test adress', $decryptedCollection->adress);
    }
}
