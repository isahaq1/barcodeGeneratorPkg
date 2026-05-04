name: Bug Report
description: Report a bug to help us improve
title: "[BUG] "
labels: ["bug"]

body:

- type: markdown
  attributes:
  value: |
  Thanks for reporting an issue! 🙏

- type: textarea
  id: description
  attributes:
  label: Describe the bug
  description: A clear and concise description of what the bug is
  placeholder: |
  When I [perform this action], [this happens instead of that].
  validations:
  required: true

- type: textarea
  id: reproduction
  attributes:
  label: Steps to reproduce
  description: Steps to reproduce the behavior
  placeholder: | 1. Generate a barcode with type '...' 2. Set parameters to '...' 3. Call method '...' 4. See error
  validations:
  required: true

- type: textarea
  id: expected
  attributes:
  label: Expected behavior
  description: What should happen instead
  placeholder: The barcode should be generated without errors
  validations:
  required: true

- type: textarea
  id: code
  attributes:
  label: Code example
  description: Minimal code that reproduces the issue
  render: php
  placeholder: |
  use Isahaq\Barcode\Types\Code128;
  $barcode = new Code128();
  $barcode->encode('test');

- type: input
  id: php-version
  attributes:
  label: PHP Version
  placeholder: "8.0"
  validations:
  required: true

- type: input
  id: laravel-version
  attributes:
  label: Laravel Version (if applicable)
  placeholder: "9.0"

- type: textarea
  id: environment
  attributes:
  label: Environment
  description: OS and other relevant info
  placeholder: | - OS: Windows 10 - PHP Version: 8.1 - Laravel Version: 10.0

- type: textarea
  id: logs
  attributes:
  label: Error logs
  description: Any error messages or stack traces
  render: php
