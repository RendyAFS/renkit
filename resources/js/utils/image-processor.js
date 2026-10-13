async function loadImage(file) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            resolve(img);
        };
        img.onerror = (e) => {
            URL.revokeObjectURL(url);
            reject(e);
        };
        img.src = url;
    });
}

function canvasToBlob(canvas, mimeType, quality = 0.88) {
    return new Promise((resolve) => {
        canvas.toBlob((blob) => resolve(blob), mimeType, quality);
    });
}

async function processImageFile(
    file,
    {
        maxSizeBytes = 2 * 1024 * 1024, // 2 MB
        maxDimension = 1920,
        mimeType = "image/jpeg",
        quality = 0.88,
    } = {},
) {
    const img = await loadImage(file);

    const originalWidth = img.naturalWidth || img.width;
    const originalHeight = img.naturalHeight || img.height;

    const scale = Math.min(
        1,
        maxDimension / originalWidth,
        maxDimension / originalHeight,
    );

    let width = Math.round(originalWidth * scale);
    let height = Math.round(originalHeight * scale);

    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");

    let blob = null;

    while (true) {
        canvas.width = width;
        canvas.height = height;

        ctx.clearRect(0, 0, width, height);
        ctx.drawImage(img, 0, 0, width, height);

        blob = await canvasToBlob(canvas, mimeType, quality);

        if (!blob) {
            throw new Error("Gagal memproses gambar.");
        }

        if (blob.size <= maxSizeBytes) {
            break;
        }

        const nextWidth = Math.round(width * 0.9);
        const nextHeight = Math.round(height * 0.9);

        if (nextWidth < 320 || nextHeight < 320) {
            break;
        }

        width = nextWidth;
        height = nextHeight;
    }

    const fileName = file.name
        ? file.name.replace(/\.[^/.]+$/, "") + ".jpg"
        : `image-${Date.now()}.jpg`;

    return new File([blob], fileName, {
        type: mimeType,
        lastModified: Date.now(),
    });
}

export default processImageFile;
