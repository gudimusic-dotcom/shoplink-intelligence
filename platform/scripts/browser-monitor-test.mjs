const targetUrl = process.argv[2];

if (!targetUrl) {
    console.log(JSON.stringify({
        ok: false,
        message: "Missing URL.",
    }));
    process.exit(1);
}

const { chromium } = await import("playwright");

const browser = await chromium.launch({
    headless: true,
    chromiumSandbox: false,
});

const page = await browser.newPage({
    locale: "sk-SK",
    userAgent: "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36",
});

function extractPriceCandidates(text) {
    const matches = text.match(/\b\d{1,3}(?:[ .]\d{3})*(?:[.,]\d{1,2})?\s?(?:€|EUR|Kč|CZK|Ft|HUF)\b/gu) ?? [];

    return [...new Set(matches.map((value) => value.replace(/\s+/g, " ").trim()))].slice(0, 6);
}

let payload;

try {
    const response = await page.goto(targetUrl, {
        waitUntil: "networkidle",
        timeout: 60000,
    });

    const bodyText = await page.locator("body").innerText();
    const title = await page.title();
    const challenge = /cloudflare|attention required|verify you are human|checking your browser/i.test(bodyText)
        ? "Cloudflare"
        : null;
    const priceCandidates = extractPriceCandidates(bodyText);

    payload = {
        ok: !challenge && priceCandidates.length > 0,
        statusCode: response?.status() ?? null,
        title,
        challenge,
        priceCandidates,
        htmlBytes: Buffer.byteLength(await page.content(), "utf8"),
        message: challenge
            ? "Browser otvoril stránku, ale narazil na challenge."
            : (priceCandidates.length > 0
                ? "Browser našiel kandidátov na cenu."
                : "Browser otvoril stránku, ale cena sa nenašla."),
    };
} catch (error) {
    payload = {
        ok: false,
        message: error instanceof Error ? error.message : "Unknown browser error.",
    };
} finally {
    await browser.close();
}

console.log(JSON.stringify(payload));
