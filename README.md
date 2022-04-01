### Hexlet tests and linter status:
[![Actions Status](https://github.com/SellY0urS0ul/php-project-lvl2/workflows/hexlet-check/badge.svg)](https://github.com/SellY0urS0ul/php-project-lvl2/actions)
![Linter Status](https://github.com/SellY0urS0ul/php-project-lvl2/actions/workflows/github-actions.yml/badge.svg)
[![Maintainability](https://api.codeclimate.com/v1/badges/3235aa486867a055844c/maintainability)](https://codeclimate.com/github/SellY0urS0ul/php-project-lvl2/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/3235aa486867a055844c/test_coverage)](https://codeclimate.com/github/SellY0urS0ul/php-project-lvl2/test_coverage)

[![asciicast](https://asciinema.org/a/MU53TDKwbqoZ84xcUBmMDFGcE.svg)](https://asciinema.org/a/MU53TDKwbqoZ84xcUBmMDFGcE)
[![asciicast](https://asciinema.org/a/8ioRKlQ7TKtb0W234wkFgLfDd.svg)](https://asciinema.org/a/8ioRKlQ7TKtb0W234wkFgLfDd)

## Description

The Difference Calculator is a console solution for finding differences between two files using PHP. Support for JSON, YML and YAML formats is provided. The solution supports various formats for outputting differences (Stylish, Plain and Json). The operation logic is based on recursive file comparison and takes into account the types and structure of the compared data.

## Setup

```sh
git clone https://github.com/SellY0urS0ul/php-project-lvl2.git
make install
```

## Calculate Differences

Calculate difference between file1 and file2:
```sh
./bin/gendiff --format <format> <file1> <file2> 
```

For more information
```sh
./bin/gendiff -h
```