from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Navigate to the login page
    page.goto("http://localhost:8000/login")
    page.fill('input[name="email"]', 'admin@_gondowangi.com')
    page.fill('input[name="password"]', 'admin')
    page.click('button[type="submit"]')
    page.wait_for_load_state('networkidle')

    # Navigate to the goods receipt page
    page.goto("http://localhost:8000/transaction/goods-receipt")
    page.wait_for_load_state('networkidle')

    # Click the "Buat Penerimaan" button
    page.click('button:has-text("Buat Penerimaan")')
    page.wait_for_selector('select[v-model="newShipment.noPo"]')

    # Select a PO
    page.select_option('select[v-model="newShipment.noPo"]', index=1)
    page.wait_for_load_state('networkidle')

    # Take a screenshot
    page.screenshot(path="jules-scratch/verification/verification.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
