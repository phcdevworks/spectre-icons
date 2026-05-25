'use strict'

import { readFileSync } from 'node:fs'
import { join } from 'node:path'

type Classification = 'additive' | 'semantic change' | 'breaking'
type BumpType = 'major' | 'minor' | 'patch'

const repoRoot = process.cwd()
const changelogPath = join(repoRoot, 'CHANGELOG.md')
const packagePath = join(repoRoot, 'package.json')

const CLASSIFICATION_PREFIX = 'Contract change type:'
const ALLOWED: Classification[] = ['additive', 'semantic change', 'breaking']

const changelog = readFileSync(changelogPath, 'utf8')
const unreleasedSection = changelog.split('## [Unreleased]')[1]?.split('\n## [')[0] ?? ''

const classificationPattern = new RegExp(
  `${CLASSIFICATION_PREFIX}\\s*(${ALLOWED.join('|')})`,
  'i'
)
const match = unreleasedSection.match(classificationPattern)

if (!match) {
  const hasContent = unreleasedSection.trim().length > 0
  if (!hasContent) {
    console.log('No unreleased changes. No version bump needed.')
    process.exit(0)
  }
  throw new Error(
    [
      'Unreleased section has content but is missing a contract change classification.',
      `Expected: ${CLASSIFICATION_PREFIX} <${ALLOWED.join(' | ')}>`,
      'Add a classification line to CHANGELOG.md [Unreleased] before proposing a version.'
    ].join('\n')
  )
}

const classification = match[1].toLowerCase() as Classification
const pkg = JSON.parse(readFileSync(packagePath, 'utf8')) as { version: string }
const current = pkg.version
const [majorStr, minorStr, patchStr] = current.split('.')
const major = parseInt(majorStr, 10)
const minor = parseInt(minorStr, 10)
const patch = parseInt(patchStr, 10)

let proposed: string
let bumpType: BumpType

if (classification === 'breaking') {
  proposed = `${major + 1}.0.0`
  bumpType = 'major'
} else if (classification === 'additive' || classification === 'semantic change') {
  proposed = `${major}.${minor + 1}.0`
  bumpType = 'minor'
} else {
  proposed = `${major}.${minor}.${patch + 1}`
  bumpType = 'patch'
}

console.log(`Current version : ${current}`)
console.log(`Classification  : ${classification}`)
console.log(`Bump type       : ${bumpType}`)
console.log(`Proposed version: ${proposed}`)
