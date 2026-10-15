/**
 * Optional browser compatibility suite. STAGING ONLY.
 *
 * WP_E2E_STAGING=1 WP_BASE_URL=https://staging.example.com \
 * WP_ADMIN_USER=editor WP_ADMIN_PASSWORD=... npx playwright test
 *
 * Tests navigate admin forms without submitting them. WordPress may create
 * transient auto-draft records when an Add New screen is opened.
 */
const { test, expect } = require('@playwright/test');

const BASE = (process.env.WP_BASE_URL || '').replace(/\/+$/, '');
const USER = process.env.WP_ADMIN_USER || '';
const PASSWORD = process.env.WP_ADMIN_PASSWORD || '';
const ENABLED = process.env.WP_E2E_STAGING === '1' && !!BASE && !!USER && !!PASSWORD;
test.skip(!ENABLED, 'Requires WP_E2E_STAGING=1 and staging WordPress credentials');

const types = {
    service: { tabs: 4, taxonomy: 'service_category' },
    project: { tabs: 4, taxonomy: 'project_category' },
    case_study: { tabs: 5, taxonomy: 'case_study_category' },
    industry: { tabs: 4, taxonomy: 'industry_category' },
    team: { tabs: 4, taxonomy: 'team_department' },
    pricing: { tabs: 4, taxonomy: 'pricing_category' },
    faq: { tabs: 3, taxonomy: 'faq_category' },
};

async function login(page) {
    await page.goto(BASE + '/wp-login.php');
    await page.locator('#user_login').fill(USER);
    await page.locator('#user_pass').fill(PASSWORD);
    await page.locator('#wp-submit').click();
    await expect(page.locator('#wpadminbar')).toBeVisible();
}

test.describe('TheOct CPT admin — read-only smoke coverage', () => {
    test.beforeEach(async ({ page }) => {
        await login(page);
    });

    for (const [postType, meta] of Object.entries(types)) {
        test(postType + ' has tabs, checklist and taxonomy filter', async ({ page }) => {
            await page.goto(BASE + '/wp-admin/edit.php?post_type=' + postType);
            await expect(page.locator('th#theoct_details')).toHaveCount(1);
            await expect(page.locator('select[name="' + meta.taxonomy + '"]')).toHaveCount(1);

            await page.goto(BASE + '/wp-admin/post-new.php?post_type=' + postType);
            const box = page.locator('#theoct-fields-content');
            await expect(box).toHaveCount(1);
            const buttons = box.locator('[role="tab"]');
            await expect(buttons).toHaveCount(meta.tabs);
            await expect(box.locator('#theoct-admin-sections-0-panel-0')).toBeVisible();
            await buttons.nth(1).click();
            await expect(buttons.nth(1)).toHaveAttribute('aria-selected', 'true');
            await expect(box.locator('#theoct-admin-sections-0-panel-0')).toBeHidden();

            await buttons.nth(1).press('Home');
            await expect(buttons.first()).toHaveAttribute('aria-selected', 'true');

            const expand = box.getByRole('button', { name: 'Show all sections' });
            await expand.click();
            await expect(box.locator('#theoct-admin-sections-0-panel-0')).toBeVisible();
            await expect(box.locator('#theoct-admin-sections-0-panel-1')).toBeVisible();
            await box.getByRole('button', { name: 'Show one section' }).click();
            await expect(box.locator('#theoct-admin-sections-0-panel-1')).toBeHidden();

            await expect(page.locator('#theoct-editing-guide')).toHaveCount(1);
            await expect(page.locator('#theoct-editing-guide .theoct-editor-checklist li')).not.toHaveCount(0);
        });
    }

    test('Homepage has no generated theme frontend', async ({ page }) => {
        const response = await page.request.get(BASE + '/');
        expect(response.ok()).toBeTruthy();
        const body = (await response.text()).trim();
        expect(body).toBe('');
    });
});
