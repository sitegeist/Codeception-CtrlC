<?php

namespace Natterbox\Codeception\Tests;

use \AcceptanceTester;

class DemoCest
{
    public function demo(AcceptanceTester $I)
    {
        $seconds = 60;
        $I->amGoingTo('loop for one minute; press Ctrl+C to abort');
        while (--$seconds > 0 && $I->amNotInterrupted()) {
            sleep(1);
            codecept_debug('...');
        }
    }
}
