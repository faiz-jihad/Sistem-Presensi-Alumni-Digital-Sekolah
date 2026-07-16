import pkg from 'whatsapp-web.js';
const { Client, LocalAuth } = pkg;
import qrcode from 'qrcode-terminal';
import express from 'express';
import bodyParser from 'body-parser';

import fs from 'fs';

const app = express();
app.use(bodyParser.json());

// Deteksi path Google Chrome secara otomatis di Windows untuk menghindari error "Could not find Chrome"
let chromePath = undefined;
if (process.platform === 'win32') {
    const commonWindowsPaths = [
        'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
        process.env.LOCALAPPDATA ? `${process.env.LOCALAPPDATA}\\Google\\Chrome\\Application\\chrome.exe` : null
    ].filter(Boolean);

    for (const p of commonWindowsPaths) {
        if (fs.existsSync(p)) {
            chromePath = p;
            console.log(`[INFO] Menemukan Google Chrome di: ${chromePath}`);
            break;
        }
    }
}

const puppeteerConfig = {
    args: [
        '--no-sandbox', 
        '--disable-setuid-sandbox', 
        '--disable-dev-shm-usage', 
        '--disable-accelerated-2d-canvas', 
        '--no-first-run', 
        '--no-zygote', 
        '--disable-gpu',
        '--user-data-dir=./.chrome-data'
    ],
    headless: true
};

if (chromePath) {
    puppeteerConfig.executablePath = chromePath;
}

// Inisialisasi client whatsapp-web.js dengan strategi autentikasi lokal
let client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: puppeteerConfig,
    webVersionCache: {
        type: 'local',
    }
});

let isReady = false;
let latestQr = '';

// Event ketika QR Code digenerate (tampilkan di terminal)
client.on('qr', (qr) => {
    latestQr = qr;
    console.log('\n==================================================================');
    console.log('SCAN QR CODE BERIKUT DENGAN WHATSAPP ANDA UNTUK LOGIN:');
    console.log('==================================================================\n');
    qrcode.generate(qr, { small: true });
    console.log('\n==================================================================\n');
    console.log('[TIPS] Jika QR Code terminal di atas tidak bisa di-scan:');
    console.log('Buka browser Anda dan akses link berikut untuk scan QR Code:');
    console.log(`http://localhost:5000/qr\n`);
    console.log('==================================================================\n');
});

// Event ketika client siap digunakan
client.on('ready', () => {
    console.log('\n[INFO] WhatsApp Web Client siap digunakan dan terhubung!');
    isReady = true;
});

// Event ketika otentikasi berhasil
client.on('authenticated', () => {
    console.log('[INFO] Otentikasi berhasil!');
});

// Event ketika otentikasi gagal
client.on('auth_failure', (msg) => {
    console.error('[ERROR] Otentikasi gagal:', msg);
});

// Event ketika terputus
client.on('disconnected', (reason) => {
    console.log('[WARNING] WhatsApp Web terputus:', reason);
    isReady = false;
    // Inisialisasi ulang client
    console.log('[INFO] Mencoba menghubungkan ulang dalam 5 detik...');
    setTimeout(async () => {
        try {
            await client.destroy();
        } catch (e) {}
        client = new Client({
            authStrategy: new LocalAuth(),
            puppeteer: puppeteerConfig,
            webVersionCache: {
                type: 'local',
            }
        });
        client.on('qr', (qr) => { latestQr = qr; });
        client.on('ready', () => { isReady = true; });
        client.initialize();
    }, 5000);
});

/**
 * Endpoint API untuk mengirim pesan WhatsApp
 * POST http://localhost:5000/send
 * Body: { "number": "628xxx", "message": "Pesan kamu" }
 */
app.post('/send', async (req, res) => {
    const { number, message } = req.body;

    if (!isReady) {
        return res.status(503).json({
            status: false,
            message: 'WhatsApp Web Client belum siap. Harap tunggu atau scan QR terlebih dahulu.'
        });
    }

    if (!number || !message) {
        return res.status(400).json({
            status: false,
            message: 'Parameter "number" dan "message" wajib diisi.'
        });
    }

    try {
        // Format nomor agar sesuai kebutuhan whatsapp-web.js
        let formattedNumber = number.replace(/\D/g, ''); // Hapus karakter non-digit
        
        // Ubah awalan 0 menjadi 62 jika ada
        if (formattedNumber.startsWith('0')) {
            formattedNumber = '62' + formattedNumber.substring(1);
        }

        // Pastikan format internasional berakhiran @c.us
        if (!formattedNumber.endsWith('@c.us')) {
            formattedNumber = `${formattedNumber}@c.us`;
        }

        console.log(`[SEND] Mengirim pesan ke ${formattedNumber}...`);
        await client.sendMessage(formattedNumber, message);

        return res.json({
            status: true,
            message: `Pesan berhasil dikirim ke ${number}`
        });
    } catch (error) {
        console.error('[ERROR] Gagal mengirim pesan:', error);
        return res.status(500).json({
            status: false,
            message: 'Gagal mengirim pesan',
            error: error.message
        });
    }
});

/**
 * Endpoint untuk menampilkan QR Code di browser jika di terminal tidak bisa di-scan
 * GET http://localhost:5000/qr
 */
app.get('/qr', (req, res) => {
    if (isReady) {
        return res.send('<h1>WhatsApp Web Client sudah terhubung!</h1>');
    }
    if (!latestQr) {
        return res.send('<h1>QR Code belum siap. Harap tunggu sebentar...</h1>');
    }
    
    const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(latestQr)}`;
    
    res.send(`
        <html>
            <head>
                <title>Scan WhatsApp QR Code</title>
                <meta http-equiv="refresh" content="15">
                <style>
                    body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; background-color: #f3f4f6; }
                    .card { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; }
                    img { margin: 1.5rem 0; border: 1px solid #e5e7eb; padding: 10px; background: white; }
                    .info { color: #4b5563; font-size: 0.9rem; }
                </style>
            </head>
            <body>
                <div class="card">
                    <h2>Scan QR Code untuk Login WhatsApp</h2>
                    <p class="info">Halaman ini akan otomatis merefresh setiap 15 detik</p>
                    <img src="${qrImageUrl}" alt="WhatsApp QR Code" />
                    <p class="info">Setelah discan, halaman ini akan otomatis mendeteksi status.</p>
                </div>
            </body>
        </html>
    `);
});

/**
 * Endpoint API untuk mengecek status koneksi WhatsApp
 * GET http://localhost:5000/status
 */
app.get('/status', (req, res) => {
    res.json({
        status: true,
        whatsapp_ready: isReady,
        qr: isReady ? null : latestQr,
        user: isReady && client.info ? {
            name: client.info.pushname || 'WhatsApp Client',
            number: client.info.wid ? client.info.wid.user : null
        } : null
    });
});

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
    console.log(`\n==================================================================`);
    console.log(`WhatsApp Web JS Gateway berjalan di http://localhost:${PORT}`);
    console.log(`==================================================================`);
    console.log('[INFO] Menghubungkan ke WhatsApp Web...');
    client.initialize();
});
