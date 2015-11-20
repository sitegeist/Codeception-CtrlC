<?php

namespace Natterbox\Codeception\Tests;

use \AcceptanceTester;

class DemoCest
{
    /**
     * IMPORTANT NOTE: This is just a demo and it is not the most common use-case for the CtrlC module.
     * The benefits of this module can be enjoyed just by adding it to the list of enabled modules, in the config.
     *
     * @param AcceptanceTester $I
     */
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
