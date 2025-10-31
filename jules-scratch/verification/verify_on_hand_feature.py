
import re
from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    try:
        # Log in
        page.goto("http://localhost:5173/login")
        page.wait_for_load_state("networkidle")
        page.get_by_placeholder("Enter your email").fill("admin@gmail.com")
        page.get_by_placeholder("Enter your password").fill("password")
        page.get_by_role("button", name="Login").click()

        # Go to the master data page
        page.goto("http://localhost:5173/master-data")

        # Click the "Bin Location" tab
        page.get_by_role("button", name="Bin Location").click()

        # Click the first bin with items
        page.locator("text=/.* Item/").first.click()

        # Wait for the modal to appear
        expect(page.locator("h3:has-text('Material di Bin')")).to_be_visible()

        # Take a screenshot
        page.screenshot(path="jules-scratch/verification/on_hand_verification.png")

    except Exception as e:
        print(f"An error occurred: {e}")
        page.screenshot(path="jules-scratch/verification/error.png")
    finally:
        browser.close()

with sync_playwright() as playwright:
    run(playwright)
