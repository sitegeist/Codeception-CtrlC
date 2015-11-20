# Codeception-CtrlC
Codeception module that allows safe abortion of a run by pressing `Ctrl+C`

# Installation

```
composer install "natterbox/codeception-ctrlc: ~1.0"
```

# Usage

Enable the module in `tests/<SuiteName>.suite.yml` or `codeception.yml`.

```
...
modules:
    enabled:
        ...
        - Codeception\Extension\CtrlC
        ...
    config:
        ...
        Codeception\Extension\CtrlC:
            debug: true
        ...
```

Benefits of this module can be enjoyed just by adding it in the config.

For a demo, please do checkout the example. Run the demo test and press `Ctrl+C`.

IMPORTANT NOTE: The code shown in examples folder is just a demo and it is not the most common use-case for this module.
