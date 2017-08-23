<?php

use Symfony\Component\Process\Process;

class simpleTest extends PHPUnit_Framework_TestCase
{
    private $validator = '';

    public function setUp()
    {
        $realpath = realpath(dirname(__FILE__));
        $this->validator = '`which node` ' . $realpath . '/../validator.js';
    }

    /**
     * @dataProvider eifSchemaProvider
     */
    public function testEifSchema($json, $expected, $description) {
        $process = new Process($this->validator, NULL, NULL, $json);
        $process->setTimeout(2);
        $process->run();

        $actual = ($process->isSuccessful()) ? TRUE : FALSE;

        $this->assertEquals($expected, $actual, $description);
    }

    public function eifSchemaProvider() {
        return $this->eifSchemaChecksFromFixture('schema-checks');
    }

    public function eifSchemaChecksFromFixture($file) {
        $realpath = realpath(dirname(__FILE__));
        $groups = json_decode(file_get_contents($realpath . '/fixtures/' . $file . '.json'));

        $checks = [];
        foreach ($groups as $group) {
            foreach ($group->tests as $test) {
                $checks[] = [
                    json_encode($test->data),
                    $test->valid,
                    $test->description,
                ];
            }
        }

        return $checks;
    }
}
