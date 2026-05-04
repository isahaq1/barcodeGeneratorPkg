#!/bin/bash
# Quick Start Guide for Open Sourcing Your Barcode Package

echo "🚀 Open Source Setup Helper"
echo "============================"
echo ""

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: composer.json not found!"
    echo "Please run this script from the package root directory."
    exit 1
fi

echo "✅ Found composer.json"
echo ""

# Step 1: Initialize git if needed
if [ ! -d ".git" ]; then
    echo "📌 Step 1: Initializing git repository..."
    git init
    git add .
    git commit -m "Initial commit: Open source release"
    echo "✅ Git repository initialized"
else
    echo "✅ Git repository already exists"
fi

echo ""
echo "📋 Next steps:"
echo "============="
echo ""
echo "1️⃣  Create a GitHub repository:"
echo "   - Go to https://github.com/new"
echo "   - Repository name: barcodeGeneratorPkg"
echo "   - Choose: Public"
echo "   - Do NOT initialize with README, .gitignore, or LICENSE"
echo ""

echo "2️⃣  Link your local repository to GitHub:"
echo "   git remote add origin https://github.com/YOUR_USERNAME/barcodeGeneratorPkg.git"
echo "   git branch -M main"
echo "   git push -u origin main"
echo ""

echo "3️⃣  Push your tags:"
echo "   git tag -a v1.0.0 -m 'Version 1.0.0: Initial public release'"
echo "   git push origin v1.0.0"
echo ""

echo "4️⃣  Configure GitHub:"
echo "   - Go to Settings → General"
echo "   - Add description and topics"
echo "   - Enable Discussions (optional)"
echo ""

echo "5️⃣  Create GitHub Release:"
echo "   - Go to Releases → Create a new release"
echo "   - Tag: v1.0.0"
echo "   - Add release notes from CHANGELOG.md"
echo ""

echo "6️⃣  Verify on Packagist:"
echo "   - Visit: https://packagist.org/packages/isahaq/barcode"
echo "   - Click 'Update' if needed"
echo ""

echo "📚 Documentation files created:"
echo "==============================="
echo "✓ README.md                         - Main documentation"
echo "✓ CONTRIBUTING.md                   - Contribution guidelines"
echo "✓ CODE_OF_CONDUCT.md                - Community standards"
echo "✓ CHANGELOG.md                      - Version history"
echo "✓ SECURITY.md                       - Security policy"
echo "✓ .gitignore                        - Git ignore rules"
echo "✓ .github/ISSUE_TEMPLATE/           - Issue templates"
echo "✓ .github/PULL_REQUEST_TEMPLATE.md  - PR template"
echo "✓ .github/workflows/tests.yml       - CI/CD workflow"
echo "✓ composer.json                     - Updated with metadata"
echo ""

echo "🎉 All done! Your package is ready for open source!"
echo ""
echo "📖 Read these guides for more info:"
echo "   - OPENSOURCE_SETUP.md           - Complete setup checklist"
echo "   - OPENSOURCE_BEST_PRACTICES.md  - Best practices guide"
