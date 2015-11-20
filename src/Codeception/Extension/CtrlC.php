<?php
/**
 * @copyright 2009-2014 Red Matter Ltd (UK)
 */

namespace Codeception\Extension;

use Codeception\Exception\TestRuntime as TestRuntimeException;
use Codeception\Module;
use Codeception\Step;
use Codeception\TestCase;

class CtrlC extends Module
{
    protected $config = ['debug'=>false];

    protected $requiredFields = [];

    private static $KillOnSignals = [SIGINT, SIGTERM];

    protected $terminate = false;
    protected $terminateSignal = 0;

    /**
     * Condition / config driven debug
     *
     * @param $message
     */
    protected function debug($message)
    {
        if ($this->config['debug']) {
            parent::debug($message);
        }
    }

    /**
     * hack to put something on to the console
     *
     * @param $message
     */
    protected function showMessage($message)
    {
        file_put_contents(
            'php://stdout',
            "\r\n" .
            "\r\n".str_repeat('>', strlen($message)+2)."\r\n" .
            " $message" .
            "\r\n".str_repeat('>', strlen($message)+2)."\r\n" .
            "\r\n"
        );
    }

    /**
     * Initialise signals
     */
    public function initSignalHandler()
    {
        $this->debug(__CLASS__.'::initSignalHandler +++++++');

        if (function_exists('pcntl_signal')) {
            foreach (self::$KillOnSignals as $signal) {
                pcntl_signal($signal, function ($sig) {
                    // we only want to capture the first signal
                    if ($this->terminate === false) {
                        $this->terminate = true;
                        $this->terminateSignal = $sig;
                        $this->showMessage("Please wait for the scenario to finish...");
                    } elseif ($this->terminate === true) {
                        $this->showMessage("Wait! Scenario / Cleanup in progress ...");
                    }
                });
            }
        }
    }

    /**
     * De-initialise signals
     */
    public function deinitSignalHandler()
    {
        $this->debug(__CLASS__.'::deinitSignalHandler ------');

        if (function_exists('pcntl_signal')) {
            foreach (self::$KillOnSignals as $signal) {
                pcntl_signal($signal, SIG_DFL);
            }
        }
    }

    /**
     * Gracefully terminate if interrupted
     */
    private function failOnInterruption()
    {
        if ($this->terminate !== false) {
            $this->terminate = null;
            throw new TestRuntimeException("Test interrupted by signal {$this->terminateSignal}");
        }
    }

    /**
     * Hook on every Actor class initialization
     */
    // @codingStandardsIgnoreLine overridden function from \Codeception\Module
    public function _cleanup()
    {
        $this->debug(__CLASS__.'::_cleanup');

        // if there was an interruption; let us abort any further scenario!
        $this->failOnInterruption();
    }

    /**
     * Hook after suite
     */
    // @codingStandardsIgnoreLine overridden function from \Codeception\Module
    public function _afterSuite()
    {
        // if there was an interruption; let us abort the whole run!
        $this->failOnInterruption();
        parent::_afterSuite();
    }

    /**
     * Hook before each step
     *
     * @param Step $step the test case step
     */
    // @codingStandardsIgnoreLine overridden function from \Codeception\Module
    public function _beforeStep(Step $step)
    {
        $this->debug(__CLASS__.'::_beforeStep '.$step->getHumanizedAction());

        // if there was an interruption; fail the step
        $this->failOnInterruption();
        parent::_beforeStep($step);
    }

    /**
     * Hook after each step
     *
     * @param Step $step the test case step
     */
    // @codingStandardsIgnoreLine overridden function from \Codeception\Module
    public function _afterStep(Step $step)
    {
        $this->debug(__CLASS__.'::_afterStep '.$step->getHumanizedAction());

        // if there was an interruption; fail the step
        $this->failOnInterruption();
        parent::_afterStep($step);
    }

    /**
     * Hook before each scenario
     *
     * @param TestCase $test test scenario
     */
    // @codingStandardsIgnoreLine overridden function from \Codeception\Module
    public function _before(TestCase $test)
    {
        $this->debug(__CLASS__.'::_before ['.$test->getName().']');

        $this->initSignalHandler();
        parent::_before($test);
    }

    /**
     * Hook after each scenario
     *
     * @param TestCase $test test scenario
     */
    // @codingStandardsIgnoreLine overridden function from \Codeception\Module
    public function _after(TestCase $test)
    {
        $this->debug(__CLASS__.'::_after ['.$test->getName().']');

        $this->deinitSignalHandler();
        parent::_after($test);
    }

    /**
     * Hook after each scenario fails
     *
     * @param TestCase $test test scenario
     * @param          $fail
     */
    // @codingStandardsIgnoreLine overridden function from \Codeception\Module
    public function _failed(TestCase $test, $fail)
    {
        $this->debug(__CLASS__.'::_failed ['.$test->getName().']');

        $this->deinitSignalHandler();
        parent::_failed($test, $fail);
    }
}
