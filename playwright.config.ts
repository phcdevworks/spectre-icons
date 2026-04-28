import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  timeout: 90_000,
  workers: 1,
  outputDir: 'test-results',
  use: {
    baseURL: process.env.SPECTRE_E2E_BASE_URL ?? 'http://localhost:8888',
    headless: true,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure'
  },
  reporter: [['list']]
});
