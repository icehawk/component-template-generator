# IceHawk\ComponentTemplateGenerator

A generator tool for IceHawk components.

## Requirements

* PHP version >= 7.0

## Installation

* Download the [latest release](https://github.com/icehawk/component-template-generator/releases/latest).

### With varification (recommended)

```bash
RELEASE="vX.X.X"
wget -c "https://github.com/icehawk/component-template-generator/releases/download/$RELEASE/icehawk-ctg.phar"
wget -c "https://github.com/icehawk/component-template-generator/releases/download/$RELEASE/icehawk-ctg.phar.asc"
gpg --keyserver hkps.pool.sks-keyservers.net --recv-keys C8107679
gpg --verify icehawk-ctg.phar.asc icehawk-ctg.phar
```

### Without verification (not recommended)

```bash
RELEASE="vX.X.X"
wget -c "https://github.com/icehawk/component-template-generator/releases/download/$RELEASE/icehawk-ctg.phar"
```

## Usage

```bash
# List commands
php icehawk-ctg.phar list

# Generate a new IceHawk component template 
php icehawk-ctg.phar generate:component [-f|--force] [targetDir]

# ... now answer the interactive questions and you're done.
```