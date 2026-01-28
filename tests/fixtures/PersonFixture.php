<?php
// tests/fixtures/PersonFixture.php
namespace Tests\Fixtures;

use App\Entity\Person;

class PersonFixture
{
    public static function createDefaultPerson(): Person
    {
        return new Person('FixtureName', 'EUR');
    }
}
