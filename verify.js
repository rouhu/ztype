const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  await page.goto('http://localhost:8000');

  // Wait for the canvas to be visible
  await page.waitForSelector('#game');
  await page.waitForTimeout(2000); // Wait for game to initialize

  await page.screenshot({ path: 'before_click.png' });

  // Click on the "settings" button
  await page.mouse.click(480, 438);
  await page.waitForTimeout(1000);

  await page.screenshot({ path: 'after_click.png' });
  await browser.close();
})();