# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: elementor/icon-libraries.spec.ts >> Elementor icon libraries >> Elementor icon picker shows bundled Spectre library tabs
- Location: tests/e2e/elementor/icon-libraries.spec.ts:15:7

# Error details

```
Test timeout of 60000ms exceeded.
```

```
Error: page.goto: net::ERR_ABORTED at http://localhost:8888/wp-login.php
Call log:
  - navigating to "http://localhost:8888/wp-login.php", waiting until "load"

```

```
Test timeout of 60000ms exceeded while running "afterEach" hook.
```

# Page snapshot

```yaml
- generic [active] [ref=e1]:
  - generic [ref=e2]:
    - navigation "Main menu":
      - link "Skip to main content" [ref=e3] [cursor=pointer]:
        - /url: "#wpbody-content"
      - link "Skip to toolbar" [ref=e4] [cursor=pointer]:
        - /url: "#wp-toolbar"
      - list [ref=e7]:
        - listitem [ref=e8]:
          - link "Dashboard" [ref=e9] [cursor=pointer]:
            - /url: index.php
            - generic [ref=e10]: 
            - generic [ref=e11]: Dashboard
          - list [ref=e12]:
            - listitem [ref=e13]:
              - link "Home" [ref=e14] [cursor=pointer]:
                - /url: index.php
            - listitem [ref=e15]:
              - link "Updates" [ref=e16] [cursor=pointer]:
                - /url: update-core.php
        - listitem [ref=e17]:
          - link "Elementor" [ref=e18] [cursor=pointer]:
            - /url: admin.php?page=elementor-home
            - generic [ref=e20]: Elementor
          - list [ref=e21]:
            - listitem [ref=e22]:
              - link "Home" [ref=e23] [cursor=pointer]:
                - /url: admin.php?page=elementor-home
            - listitem [ref=e24]:
              - link "Editor" [ref=e25] [cursor=pointer]:
                - /url: admin.php?page=elementor
            - listitem [ref=e26]:
              - link "Upgrade" [ref=e27] [cursor=pointer]:
                - /url: admin.php?page=elementor-one-upgrade
        - listitem [ref=e28]
        - listitem [ref=e30]:
          - link "Posts" [ref=e31] [cursor=pointer]:
            - /url: edit.php
            - generic [ref=e32]: 
            - generic [ref=e33]: Posts
          - list [ref=e34]:
            - listitem [ref=e35]:
              - link "All Posts" [ref=e36] [cursor=pointer]:
                - /url: edit.php
            - listitem [ref=e37]:
              - link "Add Post" [ref=e38] [cursor=pointer]:
                - /url: post-new.php
            - listitem [ref=e39]:
              - link "Categories" [ref=e40] [cursor=pointer]:
                - /url: edit-tags.php?taxonomy=category
            - listitem [ref=e41]:
              - link "Tags" [ref=e42] [cursor=pointer]:
                - /url: edit-tags.php?taxonomy=post_tag
        - listitem [ref=e43]:
          - link "Media" [ref=e44] [cursor=pointer]:
            - /url: upload.php
            - generic [ref=e45]: 
            - generic [ref=e46]: Media
          - list [ref=e47]:
            - listitem [ref=e48]:
              - link "Library" [ref=e49] [cursor=pointer]:
                - /url: upload.php
            - listitem [ref=e50]:
              - link "Add Media File" [ref=e51] [cursor=pointer]:
                - /url: media-new.php
        - listitem [ref=e52]:
          - link "Pages" [ref=e53] [cursor=pointer]:
            - /url: edit.php?post_type=page
            - generic [ref=e54]: 
            - generic [ref=e55]: Pages
          - list [ref=e56]:
            - listitem [ref=e57]:
              - link "All Pages" [ref=e58] [cursor=pointer]:
                - /url: edit.php?post_type=page
            - listitem [ref=e59]:
              - link "Add Page" [ref=e60] [cursor=pointer]:
                - /url: post-new.php?post_type=page
        - listitem [ref=e61]:
          - link "Comments" [ref=e62] [cursor=pointer]:
            - /url: edit-comments.php
            - generic [ref=e63]: 
            - generic [ref=e64]: Comments
        - text:  
        - listitem [ref=e65]
        - listitem [ref=e67]:
          - link "Appearance" [ref=e68] [cursor=pointer]:
            - /url: themes.php
            - generic [ref=e69]: 
            - generic [ref=e70]: Appearance
          - list [ref=e71]:
            - listitem [ref=e72]:
              - link "Themes" [ref=e73] [cursor=pointer]:
                - /url: themes.php
            - listitem [ref=e74]:
              - link "Editor" [ref=e75] [cursor=pointer]:
                - /url: site-editor.php
        - listitem [ref=e76]:
          - link "Plugins" [ref=e77] [cursor=pointer]:
            - /url: plugins.php
            - generic [ref=e78]: 
            - generic [ref=e79]: Plugins
          - list [ref=e80]:
            - listitem [ref=e81]:
              - link "Installed Plugins" [ref=e82] [cursor=pointer]:
                - /url: plugins.php
            - listitem [ref=e83]:
              - link "Add Plugin" [ref=e84] [cursor=pointer]:
                - /url: plugin-install.php
        - listitem [ref=e85]:
          - link "Users" [ref=e86] [cursor=pointer]:
            - /url: users.php
            - generic [ref=e87]: 
            - generic [ref=e88]: Users
          - list [ref=e89]:
            - listitem [ref=e90]:
              - link "All Users" [ref=e91] [cursor=pointer]:
                - /url: users.php
            - listitem [ref=e92]:
              - link "Add User" [ref=e93] [cursor=pointer]:
                - /url: user-new.php
            - listitem [ref=e94]:
              - link "Profile" [ref=e95] [cursor=pointer]:
                - /url: profile.php
        - listitem [ref=e96]:
          - link "Tools" [ref=e97] [cursor=pointer]:
            - /url: tools.php
            - generic [ref=e98]: 
            - generic [ref=e99]: Tools
          - list [ref=e100]:
            - listitem [ref=e101]:
              - link "Available Tools" [ref=e102] [cursor=pointer]:
                - /url: tools.php
            - listitem [ref=e103]:
              - link "Import" [ref=e104] [cursor=pointer]:
                - /url: import.php
            - listitem [ref=e105]:
              - link "Export" [ref=e106] [cursor=pointer]:
                - /url: export.php
            - listitem [ref=e107]:
              - link "Site Health" [ref=e108] [cursor=pointer]:
                - /url: site-health.php
            - listitem [ref=e109]:
              - link "Export Personal Data" [ref=e110] [cursor=pointer]:
                - /url: export-personal-data.php
            - listitem [ref=e111]:
              - link "Erase Personal Data" [ref=e112] [cursor=pointer]:
                - /url: erase-personal-data.php
            - listitem [ref=e113]:
              - link "Theme File Editor" [ref=e114] [cursor=pointer]:
                - /url: theme-editor.php
            - listitem [ref=e115]:
              - link "Plugin File Editor" [ref=e116] [cursor=pointer]:
                - /url: plugin-editor.php
        - listitem [ref=e117]:
          - link "Settings" [ref=e118] [cursor=pointer]:
            - /url: options-general.php
            - generic [ref=e119]: 
            - generic [ref=e120]: Settings
          - list [ref=e121]:
            - listitem [ref=e122]:
              - link "General" [ref=e123] [cursor=pointer]:
                - /url: options-general.php
            - listitem [ref=e124]:
              - link "Writing" [ref=e125] [cursor=pointer]:
                - /url: options-writing.php
            - listitem [ref=e126]:
              - link "Reading" [ref=e127] [cursor=pointer]:
                - /url: options-reading.php
            - listitem [ref=e128]:
              - link "Discussion" [ref=e129] [cursor=pointer]:
                - /url: options-discussion.php
            - listitem [ref=e130]:
              - link "Media" [ref=e131] [cursor=pointer]:
                - /url: options-media.php
            - listitem [ref=e132]:
              - link "Permalinks" [ref=e133] [cursor=pointer]:
                - /url: options-permalink.php
            - listitem [ref=e134]:
              - link "Privacy" [ref=e135] [cursor=pointer]:
                - /url: options-privacy.php
            - listitem [ref=e136]:
              - link "Spectre Icons" [ref=e137] [cursor=pointer]:
                - /url: options-general.php?page=spectre-icons-elementor
        - listitem [ref=e138]:
          - button "Collapse Main menu" [expanded] [ref=e139] [cursor=pointer]:
            - generic [ref=e141]: Collapse Menu
    - generic [ref=e142]:
      - generic [ref=e143]:
        - navigation "Toolbar":
          - menu:
            - group [ref=e144]:
              - menuitem "About WordPress" [ref=e145] [cursor=pointer]:
                - generic [ref=e147]: About WordPress
            - group [ref=e148]:
              - menuitem "spectre-icons" [ref=e149] [cursor=pointer]
            - group [ref=e150]:
              - menuitem "0 Comments in moderation" [ref=e151] [cursor=pointer]:
                - generic [ref=e153]: "0"
                - generic [ref=e154]: 0 Comments in moderation
            - group [ref=e155]:
              - menuitem "New" [ref=e156] [cursor=pointer]:
                - generic [ref=e158]: New
          - menu [ref=e159]:
            - group [ref=e160]:
              - menuitem "Howdy, admin" [ref=e161] [cursor=pointer]
      - main [ref=e162]:
        - generic [ref=e164]:
          - heading "Spectre Icons – Elementor Integration" [level=1] [ref=e165]
          - generic [ref=e166]:
            - 'heading "Spectre Icons: Elementor Libraries" [level=2] [ref=e167]'
            - text: Enabled Icon Libraries
            - generic [ref=e168]:
              - generic [ref=e169]:
                - checkbox "Lucide Icons" [checked] [ref=e170] [cursor=pointer]
                - text: Lucide Icons
              - generic [ref=e171]:
                - checkbox "Font Awesome" [checked] [ref=e172] [cursor=pointer]
                - text: Font Awesome
            - paragraph [ref=e173]:
              - button "Save Changes" [ref=e174] [cursor=pointer]
    - contentinfo [ref=e175]:
      - paragraph [ref=e176]:
        - text: Enjoyed
        - strong [ref=e177]: Elementor
        - text: "? Please leave us a"
        - link "★★★★★" [ref=e178] [cursor=pointer]:
          - /url: https://go.elementor.com/admin-review/
        - text: rating. We really appreciate your support!
      - paragraph [ref=e179]: Version 6.9.4
  - generic [ref=e183]:
    - heading "The Editor has a new home" [level=3] [ref=e184]
    - paragraph [ref=e185]:
      - text: Editor tools are now grouped together for quick access. Build and grow your site with everything you need in one place.
      - link "Learn more" [ref=e186] [cursor=pointer]:
        - /url: https://go.elementor.com/wp-dash-editor-one-learn-more/
    - paragraph [ref=e187]:
      - button "Got it" [ref=e188] [cursor=pointer]
    - link "Dismiss" [ref=e190] [cursor=pointer]:
      - /url: "#"
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | import { openNewPageInElementor, addIconWidget, openIconPicker, expectLibraryTabHidden, expectLibraryTabVisible } from '../support/elementor';
  3  | import { openSpectreIconsSettings, saveSettings, setLibraryEnabled } from '../support/wp-admin';
  4  | 
  5  | test.describe('Elementor icon libraries', () => {
  6  |   test.describe.configure({ mode: 'serial' });
  7  | 
> 8  |   test.afterEach(async ({ page }) => {
     |        ^ Test timeout of 60000ms exceeded while running "afterEach" hook.
  9  |     await openSpectreIconsSettings(page);
  10 |     await setLibraryEnabled(page, 'Lucide Icons', true);
  11 |     await setLibraryEnabled(page, 'Font Awesome', true);
  12 |     await saveSettings(page);
  13 |   });
  14 | 
  15 |   test('Elementor icon picker shows bundled Spectre library tabs', async ({ page }) => {
  16 |     await openSpectreIconsSettings(page);
  17 |     await setLibraryEnabled(page, 'Lucide Icons', true);
  18 |     await setLibraryEnabled(page, 'Font Awesome', true);
  19 |     await saveSettings(page);
  20 | 
  21 |     await openNewPageInElementor(page);
  22 |     await addIconWidget(page);
  23 |     await openIconPicker(page);
  24 | 
  25 |     await expect(page.locator('body')).toContainText('Spectre');
  26 |     await expectLibraryTabVisible(page, 'spectre-lucide');
  27 |     await expectLibraryTabVisible(page, 'spectre-fontawesome');
  28 |   });
  29 | 
  30 |   test('Disabled libraries are removed from the Elementor picker', async ({ page }) => {
  31 |     await openSpectreIconsSettings(page);
  32 |     await setLibraryEnabled(page, 'Lucide Icons', true);
  33 |     await setLibraryEnabled(page, 'Font Awesome', false);
  34 |     await saveSettings(page);
  35 | 
  36 |     await openNewPageInElementor(page);
  37 |     await addIconWidget(page);
  38 |     await openIconPicker(page);
  39 | 
  40 |     await expectLibraryTabVisible(page, 'spectre-lucide');
  41 |     await expectLibraryTabHidden(page, 'spectre-fontawesome');
  42 |   });
  43 | });
  44 | 
```