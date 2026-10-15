const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
    testDir: '.',
    testMatch: '**/*.spec.js',
    timeout: 45000,
    retries: 0,
    workers: 1,
    reporter: [['list']],
    use: {
        headless: true,
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure'
    }
});
