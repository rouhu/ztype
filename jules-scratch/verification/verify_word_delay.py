from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()
    page.goto("http://localhost:8000")
    page.press("body", "Escape")
    page.screenshot(path="jules-scratch/verification/settings_menu.png")
    browser.close()

with sync_playwright() as playwright:
    run(playwright)