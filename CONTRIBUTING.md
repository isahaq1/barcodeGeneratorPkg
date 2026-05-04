# Contributing to Isahaq Barcode Generator

Thank you for your interest in contributing! We love getting contributions from our community. This document will guide you through the process.

## Code of Conduct

Please note that this project is released with a [Code of Conduct](CODE_OF_CONDUCT.md). By participating in this project, you agree to abide by its terms.

## How to Contribute

### 1. Reporting Bugs

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps which reproduce the problem**
- **Provide specific examples to demonstrate the steps**
- **Describe the behavior you observed after following the steps**
- **Explain which behavior you expected to see instead and why**
- **Include PHP version and OS information**

### 2. Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- **Use a clear and descriptive title**
- **Provide a step-by-step description of the suggested enhancement**
- **Provide specific examples to demonstrate the steps**
- **Describe the current behavior and expected new behavior**
- **Explain why this enhancement would be useful**

### 3. Pull Requests

- Fill in the required template
- Follow the PHP styleguide (PSR-12)
- Write meaningful commit messages
- End all files with a newline

## Development Setup

### Prerequisites

- PHP 8.0 or higher
- Composer

### Installation

```bash
# Clone the repository
git clone https://github.com/isahaq1/barcodeGeneratorPkg.git
cd barcodeGeneratorPkg

# Install dependencies
composer install

# Install dev dependencies
composer install --dev
```

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
vendor/bin/phpunit tests/Code128Test.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/
```

## Code Style Guidelines

This project follows **PSR-12** coding standards. 

### Key Points

- Use 4 spaces for indentation (not tabs)
- Maximum line length of 120 characters
- Use meaningful variable and function names
- Add DocBlocks to all classes and methods
- Use type hints whenever possible

### Example:

```php
<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Types\BarcodeTypeInterface;

/**
 * Code128 Barcode Type
 * 
 * @package Isahaq\Barcode\Types
 */
class Code128 implements BarcodeTypeInterface
{
    /**
     * Encode data using Code128
     * 
     * @param string $data The data to encode
     * @return array The encoded barcode structure
     */
    public function encode(string $data): array
    {
        // Implementation here
    }
}
```

## Commit Messages

Write clear and meaningful commit messages:

```
# Good
Add support for new barcode type: Aztec
Fix validation error in Code128 type
Improve performance of batch generation

# Bad
Fix stuff
Update code
Changes
```

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

Where `<type>` is one of:
- **feat**: A new feature
- **fix**: A bug fix
- **docs**: Documentation only changes
- **style**: Changes that don't affect code meaning (formatting, etc.)
- **refactor**: Code change that neither fixes a bug nor adds a feature
- **perf**: Code change that improves performance
- **test**: Adding missing tests or correcting existing tests

## Adding New Features

### Adding a New Barcode Type

1. Create a new file in `src/Types/` implementing `BarcodeTypeInterface`
2. Implement the required methods
3. Add tests in `tests/`
4. Update documentation in `docs/BARCODE_TYPES.md`
5. Update `README.md` with the new type

Example:

```php
<?php

namespace Isahaq\Barcode\Types;

class NewBarcode implements BarcodeTypeInterface
{
    public function encode(string $data): array
    {
        // Your implementation
    }
    
    public function validate(string $data): bool
    {
        // Your validation logic
    }
}
```

### Adding a New Renderer

1. Create a new file in `src/Renderers/` implementing `RendererInterface`
2. Implement the required methods
3. Add tests
4. Update documentation

## Testing Requirements

All new features must include tests:

- Unit tests for new barcode types
- Integration tests if applicable
- Test coverage should be maintained or improved
- All tests must pass before merging

## Documentation

- Update `README.md` if adding user-facing features
- Update relevant documentation files in `docs/`
- Add code comments for complex logic
- Update `CHANGELOG.md` with your changes

## Pull Request Process

1. **Fork** the repository
2. **Create** a feature branch: `git checkout -b feature/your-feature-name`
3. **Commit** your changes: `git commit -am 'Add new feature'`
4. **Push** to the branch: `git push origin feature/your-feature-name`
5. **Create** a Pull Request

### PR Checklist

- [ ] My code follows the PSR-12 style guidelines
- [ ] I have updated the documentation accordingly
- [ ] I have added tests for new functionality
- [ ] All new and existing tests pass
- [ ] I have updated the CHANGELOG.md
- [ ] My commit messages are clear and descriptive

## Review Process

- At least one maintainer review required
- All conversations must be respectful and constructive
- Changes may be requested before approval
- Once approved and tests pass, the PR will be merged

## Questions?

Feel free to open an issue or email us at hmisahaq01@gmail.com

---

Happy contributing! 🎉
