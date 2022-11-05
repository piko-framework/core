# Piko framework core

[![Tests](https://github.com/piko-framework/core/actions/workflows/php.yml/badge.svg)](https://github.com/piko-framework/core/actions/workflows/php.yml)
[![Coverage Status](https://coveralls.io/repos/github/piko-framework/core/badge.svg?branch=main)](https://coveralls.io/github/piko-framework/core?branch=main)

The base package of the piko Framework. It Contains the [Piko](src/Piko.php) helper class and some usefull traits :

- [Piko\EventHandlerTrait](src/EventHandlerTrait.php) to dispatch and listen for events
- [Piko\BehaviorTrait](src/BehaviorTrait.php) to inject custom behaviors in objects using this trait
- [Piko\ModelTrait](src/ModelTrait.php) to facilitate model entity manipulation and validation.