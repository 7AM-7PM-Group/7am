import os
from PIL import Image, ImageOps

# Folder utama tempat gambar berada
BASE_DIR = r"C:\Users\merta\Downloads\Compressed\WHITE-20260126T074815Z-1-001\WHITE"

# Kualitas kompresi (0–100)
QUALITY = 30

def compress_image(path):
    try:
        img = Image.open(path)

        # Perbaiki orientasi berdasarkan EXIF (banyak foto portrait disimpan
        # dengan flag orientasi sehingga tampak landscape ketika dibuka tanpa
        # memperhitungkan EXIF). Pillow menyediakan helper untuk ini.
        try:
            img = ImageOps.exif_transpose(img)
        except Exception:
            # jika tidak ada EXIF atau transposenya gagal, lanjutkan saja
            pass

        # Convert ke RGB jika image memiliki alpha (PNG)
        if img.mode in ("RGBA", "P"):
            img = img.convert("RGB")

        # Resize (setengah ukuran sebagai contoh). Pastikan tidak jadi 0.
        new_size = (max(1, img.width // 2), max(1, img.height // 2))
        img = img.resize(new_size, Image.LANCZOS)

        # Pilih parameter simpan berdasarkan tipe file
        save_kwargs = {}
        # Gunakan ekstensi/format untuk menentukan opsi
        if path.lower().endswith((".jpg", ".jpeg")):
            save_kwargs["quality"] = QUALITY
            # simpan EXIF (jika ada) agar metadata lain tetap ada
            exif = img.info.get("exif")
            if exif:
                save_kwargs["exif"] = exif
            img.save(path, "JPEG", **save_kwargs)
        elif path.lower().endswith(".png"):
            # PNG tidak pakai quality; optimize untuk ukuran lebih kecil
            img.save(path, optimize=True)
        else:
            # fallback: gunakan quality jika format mendukung
            img.save(path, quality=QUALITY)

        print(f"Compressed: {path}")

    except Exception as e:
        print(f"Failed: {path} -> {e}")


for root, dirs, files in os.walk(BASE_DIR):
    for file in files:
        if file.lower().endswith((".jpg", ".jpeg", ".png")):
            full_path = os.path.join(root, file)
            compress_image(full_path)

print("Done!")
