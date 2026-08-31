const express = require('express');
const cors = require('cors');
const dotenv = require('dotenv');
const { PrismaClient } = require('@prisma/client');

// Patch BigInt to be serializable by JSON.stringify
BigInt.prototype.toJSON = function () {
    return this.toString();
};

dotenv.config();

const app = express();
const prisma = new PrismaClient();
const port = process.env.PORT || 5000;

app.use(cors());
app.use(express.json());

// API Routes
app.get('/api/dashboard', async (req, res) => {
    try {
        const totalBarang = await prisma.barangs.count();
        const totalPeminjaman = await prisma.peminjamans.count();
        const totalMaintenance = await prisma.maintenances.count();
        const totalUsers = await prisma.users.count();

        // Getting recently added barangs
        const recentBarangs = await prisma.barangs.findMany({
            take: 5,
            orderBy: { id: 'desc' },
            select: { id: true, nama_barang: true, kategori: true, jumlah: true }
        });

        res.json({
            message: 'Dashboard API working',
            data: {
                totalBarang,
                totalPeminjaman,
                totalMaintenance,
                totalUsers,
                recentBarangs
            }
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ error: 'Internal Server Error' });
    }
});

app.listen(port, () => {
    console.log(`Server is running on port ${port}`);
});
