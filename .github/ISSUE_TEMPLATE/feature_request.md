name: Feature Request
description: Suggest an idea for this project
title: "[FEATURE] "
labels: ["enhancement"]

body:

- type: markdown
  attributes:
  value: |
  Thanks for suggesting an idea! 💡

- type: textarea
  id: problem
  attributes:
  label: Problem description
  description: Is your feature request related to a problem?
  placeholder: |
  I'm always frustrated when [...] because [...]
  validations:
  required: true

- type: textarea
  id: solution
  attributes:
  label: Proposed solution
  description: Describe the solution you'd like
  placeholder: |
  I would like to [...] so that [...]
  validations:
  required: true

- type: textarea
  id: alternatives
  attributes:
  label: Alternative solutions
  description: Describe any alternative solutions or features you've considered
  placeholder: |
  Another way to solve this could be [...]

- type: textarea
  id: context
  attributes:
  label: Additional context
  description: Add any other context or screenshots
  placeholder: |
  This feature would be useful for [...] use cases

- type: checkboxes
  id: terms
  attributes:
  label: Checklist
  options: - label: I've checked for similar feature requests
  required: true - label: I'm willing to help implement this feature
  required: false
