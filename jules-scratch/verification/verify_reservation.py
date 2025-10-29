
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch()
    page = browser.new_page()
    page.goto("http://127.0.0.1:5173/transaction/reservation")
    page.get_by_role("button", name="Buat Request").click()
    page.get_by_role("button", name="FOH & RS").click()
    page.get_by_label("No Reservasi").fill("RSV/20251029/0001")
    page.get_by_label("Tanggal Permintaan").fill("2025-10-29T12:00")
    page.get_by_label("Alasan Reservasi").fill("Test reservation")
    page.get_by_label("Departemen").select_option("FOH")
    page.get_by_role("button", name="+ Tambah Baris").click()
    page.get_by_role("row", name="1").get_by_role("textbox").nth(0).fill("FOH-001")
    page.get_by_role("row", name="1").get_by_role("textbox").nth(1).fill("Test item")
    page.get_by_role("row", name="1").get_by_role("spinbutton").fill("1")
    page.get_by_role("row", name="1").get_by_role("combobox").select_option("PCS")
    page.screenshot(path="jules-scratch/verification/reservation.png")
    browser.close()
