import { initLucide } from "./lucide";

function createModal() {
    const overlay = document.createElement("div");
    overlay.className =
        "fixed inset-0 z-9999 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4";

    const cancelLabel = window.langFilepond?.cancel ?? "Cancel";
    const captureLabel = window.langFilepond?.take_photo ?? "Take Photo";
    const titleLabel = window.langFilepond?.camera_title ?? "Ambil Foto";

    overlay.innerHTML = `
        <div class="bg-(--color-light) dark:bg-(--color-dark-slate) rounded-2xl w-full max-w-3xl max-h-[calc(100dvh-2rem)] shadow-2xl overflow-hidden border border-(--color-light-gray) dark:border-(--color-slate) flex flex-col">
            <div class="shrink-0 flex justify-between items-center py-3 px-4 border-b border-(--color-light-gray) dark:border-(--color-slate)">
                <h3 class="font-semibold text-(--color-dark) dark:text-(--color-light)">
                    ${titleLabel}
                </h3>
                <button
                    type="button"
                    data-action="cancel"
                    class="size-8 inline-flex justify-center items-center rounded-full bg-(--color-light-gray) border border-(--color-gray) text-(--color-dark) hover:bg-(--color-gray)/40 dark:bg-(--color-dark) dark:border-(--color-slate) dark:text-(--color-light) transition-colors duration-200 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain">
                <div class="px-4 pt-4">
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            data-aspect="16:9"
                            class="aspect-btn py-2 px-2 text-xs sm:text-sm rounded-lg border transition-colors duration-200 cursor-pointer">
                            <span class="block font-medium">16:9</span>
                            <span class="text-[10px] opacity-70">Landscape</span>
                        </button>
                        <button
                            type="button"
                            data-aspect="3:4"
                            class="aspect-btn py-2 px-2 text-xs sm:text-sm rounded-lg border transition-colors duration-200 cursor-pointer">
                            <span class="block font-medium">3:4</span>
                            <span class="text-[10px] opacity-70">Portrait</span>
                        </button>
                    </div>
                </div>

                <div class="relative bg-black mt-4 flex items-center justify-center overflow-hidden">
                    <div
                        data-el="camera-container"
                        class="relative w-full max-h-[55dvh] overflow-hidden bg-black"
                        style="aspect-ratio: 3 / 4;">
                        <video autoplay playsinline muted class="absolute inset-0 w-full h-full object-cover"></video>
                        <canvas class="hidden"></canvas>

                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none" data-el="loading">
                            <div class="size-10 border-4 border-(--color-light)/30 border-t-(--color-light) rounded-full animate-spin"></div>
                        </div>

                        <div data-el="camera-controls" class="hidden absolute left-3 right-3 bottom-3 flex-col gap-2 p-3 rounded-xl bg-black/50 backdrop-blur-md">
                            <div data-control="zoom" class="hidden">
                                <div class="flex items-center gap-2 text-white">
                                    <i data-lucide="zoom-out" class="w-4 h-4 shrink-0"></i>
                                    <input type="range" data-input="zoom" min="1" max="1" step="0.1" value="1" class="w-full accent-(--color-primary) cursor-pointer">
                                    <i data-lucide="zoom-in" class="w-4 h-4 shrink-0"></i>
                                    <span data-value="zoom" class="text-xs min-w-10 text-right">1x</span>
                                </div>
                            </div>
                            <div data-control="focus" class="hidden">
                                <div class="flex items-center gap-2 text-white">
                                    <i data-lucide="focus" class="w-4 h-4 shrink-0"></i>
                                    <input type="range" data-input="focus" min="0" max="1" step="0.01" value="0" class="w-full accent-(--color-primary) cursor-pointer">
                                    <span data-value="focus" class="text-xs min-w-12 text-right">Auto</span>
                                    <button
                                        type="button"
                                        data-action="reset-focus"
                                        title="${window.langFilepond?.reset_focus ?? "Reset to Auto Focus"}"
                                        class="shrink-0 size-6 inline-flex items-center justify-center rounded-full hover:bg-white/20 transition-colors duration-200 cursor-pointer">
                                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            data-action="toggle-settings"
                            title="${window.langFilepond?.camera_settings ?? "Camera Settings"}"
                            class="hidden absolute top-3 left-3 size-10 items-center justify-center rounded-full bg-black/50 hover:bg-black/70 text-white backdrop-blur-sm transition-colors duration-200 cursor-pointer">
                            <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                        </button>

                        <button
                            type="button"
                            data-action="switch"
                            title="${window.langFilepond?.switch_camera ?? "Switch Camera"}"
                            class="hidden absolute top-3 right-3 size-10 items-center justify-center rounded-full bg-black/50 hover:bg-black/70 text-white backdrop-blur-sm transition-colors duration-200 cursor-pointer">
                            <i data-lucide="switch-camera" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="shrink-0 flex justify-end gap-3 p-4 border-t border-(--color-light-gray) dark:border-(--color-slate)">
                <button
                    type="button"
                    data-action="cancel"
                    class="py-2 px-4 text-sm rounded-lg cursor-pointer border border-(--color-gray) bg-(--color-light) hover:bg-(--color-light-gray) text-(--color-primary) dark:bg-(--color-dark) dark:border-(--color-dark-gray) dark:hover:bg-(--color-dark-slate) dark:text-(--color-light) transition-colors duration-200">
                    ${cancelLabel}
                </button>
                <button
                    type="button"
                    data-action="capture"
                    class="py-2 px-4 text-sm rounded-lg cursor-pointer bg-(--color-success) hover:bg-(--color-success)/70 text-(--color-light) transition-colors duration-200 inline-flex items-center gap-2">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                    ${captureLabel}
                </button>
            </div>
        </div>
    `;

    return overlay;
}

async function openCameraCapture({ onCapture }) {
    const overlay = createModal();
    document.body.appendChild(overlay);
    initLucide();

    const video = overlay.querySelector("video");
    const canvas = overlay.querySelector("canvas");
    const cameraContainer = overlay.querySelector(
        '[data-el="camera-container"]',
    );
    const cancelBtns = overlay.querySelectorAll('[data-action="cancel"]');
    const captureBtn = overlay.querySelector('[data-action="capture"]');
    const switchBtn = overlay.querySelector('[data-action="switch"]');
    const toggleSettingsBtn = overlay.querySelector(
        '[data-action="toggle-settings"]',
    );
    const loadingEl = overlay.querySelector('[data-el="loading"]');
    const aspectBtns = overlay.querySelectorAll("[data-aspect]");
    const cameraControls = overlay.querySelector('[data-el="camera-controls"]');
    const zoomControl = overlay.querySelector('[data-control="zoom"]');
    const focusControl = overlay.querySelector('[data-control="focus"]');
    const zoomInput = overlay.querySelector('[data-input="zoom"]');
    const focusInput = overlay.querySelector('[data-input="focus"]');
    const zoomValue = overlay.querySelector('[data-value="zoom"]');
    const focusValue = overlay.querySelector('[data-value="focus"]');
    const resetFocusBtn = overlay.querySelector('[data-action="reset-focus"]');

    let stream = null;
    let currentTrack = null;
    let currentFacingMode = "environment";
    let hasMultipleCameras = false;
    let currentAspect = "3:4";
    let settingsVisible = true;

    const aspectRatios = {
        "16:9": 16 / 9,
        "3:4": 3 / 4,
    };

    function cleanup() {
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
        }

        stream = null;
        currentTrack = null;
        overlay.remove();
    }

    function setLoading(isLoading) {
        loadingEl.classList.toggle("hidden", !isLoading);
    }

    function updateAspectButtons() {
        aspectBtns.forEach((btn) => {
            const active = btn.dataset.aspect === currentAspect;
            btn.classList.toggle("bg-(--color-primary)", active);
            btn.classList.toggle("text-(--color-light)", active);
            btn.classList.toggle("border-(--color-primary)", active);
            btn.classList.toggle("bg-(--color-light)", !active);
            btn.classList.toggle("dark:bg-(--color-dark)", !active);
            btn.classList.toggle("border-(--color-gray)", !active);
            btn.classList.toggle("dark:border-(--color-slate)", !active);
            btn.classList.toggle("text-(--color-dark)", !active);
            btn.classList.toggle("dark:text-(--color-light)", !active);
        });
    }

    function updateAspectRatio() {
        cameraContainer.style.aspectRatio = `${aspectRatios[currentAspect]}`;
        updateAspectButtons();
    }

    function setAspectRatio(aspect) {
        if (!aspectRatios[aspect]) return;
        currentAspect = aspect;
        updateAspectRatio();
    }

    function getCropDimensions(videoWidth, videoHeight, targetRatio) {
        const sourceRatio = videoWidth / videoHeight;
        let cropWidth = videoWidth;
        let cropHeight = videoHeight;

        if (sourceRatio > targetRatio) {
            cropWidth = Math.round(videoHeight * targetRatio);
        } else if (sourceRatio < targetRatio) {
            cropHeight = Math.round(videoWidth / targetRatio);
        }

        const cropX = Math.round((videoWidth - cropWidth) / 2);
        const cropY = Math.round((videoHeight - cropHeight) / 2);

        return { cropX, cropY, cropWidth, cropHeight };
    }

    async function checkMultipleCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            hasMultipleCameras =
                devices.filter((d) => d.kind === "videoinput").length > 1;
            switchBtn.classList.toggle("hidden", !hasMultipleCameras);
            switchBtn.classList.toggle("flex", hasMultipleCameras);
        } catch (e) {
            hasMultipleCameras = false;
        }
    }

    function resetCameraControls() {
        zoomControl.classList.add("hidden");
        focusControl.classList.add("hidden");
        toggleSettingsBtn.classList.add("hidden");
        toggleSettingsBtn.classList.remove("flex");
        cameraControls.classList.add("hidden");
        cameraControls.classList.remove("flex");

        zoomInput.value = 1;
        zoomInput.min = 1;
        zoomInput.max = 1;
        zoomValue.textContent = "1x";

        focusInput.value = 0;
        focusValue.textContent = "Auto";
    }

    function applySettingsVisibility() {
        const hasAnyControl =
            !zoomControl.classList.contains("hidden") ||
            !focusControl.classList.contains("hidden");
        if (!hasAnyControl) return;

        toggleSettingsBtn.classList.remove("hidden");
        toggleSettingsBtn.classList.add("flex");
        cameraControls.classList.toggle("hidden", !settingsVisible);
        cameraControls.classList.toggle("flex", settingsVisible);
    }

    function setupCameraControls(track) {
        resetCameraControls();
        if (!track) return;

        const capabilities =
            typeof track.getCapabilities === "function"
                ? track.getCapabilities()
                : {};

        if (
            capabilities.zoom &&
            typeof capabilities.zoom.min === "number" &&
            typeof capabilities.zoom.max === "number"
        ) {
            const minZoom = capabilities.zoom.min;
            const maxZoom = capabilities.zoom.max;
            const step = capabilities.zoom.step || 0.1;

            zoomInput.min = minZoom;
            zoomInput.max = maxZoom;
            zoomInput.step = step;
            zoomInput.value = minZoom;
            zoomValue.textContent = `${Number(minZoom).toFixed(1)}x`;
            zoomControl.classList.remove("hidden");
        }

        if (
            capabilities.focusDistance &&
            typeof capabilities.focusDistance.min === "number" &&
            typeof capabilities.focusDistance.max === "number" &&
            Array.isArray(capabilities.focusMode) &&
            capabilities.focusMode.includes("manual")
        ) {
            const minFocus = capabilities.focusDistance.min;
            const maxFocus = capabilities.focusDistance.max;
            const step = capabilities.focusDistance.step || 0.01;

            focusInput.min = minFocus;
            focusInput.max = maxFocus;
            focusInput.step = step;
            focusValue.textContent = "Auto";
            focusControl.classList.remove("hidden");
        }

        applySettingsVisibility();
    }

    async function setZoom(value) {
        if (!currentTrack) return;

        try {
            await currentTrack.applyConstraints({
                advanced: [{ zoom: Number(value) }],
            });
            zoomValue.textContent = `${Number(value).toFixed(1)}x`;
        } catch (err) {
            console.warn("Camera zoom tidak didukung:", err);
        }
    }

    async function setFocus(value) {
        if (!currentTrack) return;

        try {
            await currentTrack.applyConstraints({
                advanced: [
                    { focusMode: "manual", focusDistance: Number(value) },
                ],
            });
            focusValue.textContent = Number(value).toFixed(2);
        } catch (err) {
            console.warn("Manual camera focus tidak didukung:", err);
            focusValue.textContent = "Auto";
        }
    }

    async function setContinuousFocus() {
        if (!currentTrack) return;

        try {
            await currentTrack.applyConstraints({
                advanced: [{ focusMode: "continuous" }],
            });
            focusInput.value = 0;
            focusValue.textContent = "Auto";
        } catch (err) {}
    }

    function waitForVideoFrame() {
        return new Promise((resolve) => {
            if (typeof video.requestVideoFrameCallback === "function") {
                video.requestVideoFrameCallback(() => resolve());
            } else {
                requestAnimationFrame(() => requestAnimationFrame(resolve));
            }
        });
    }

    function setControlsDisabled(disabled) {
        captureBtn.disabled = disabled;
        switchBtn.disabled = disabled;
        captureBtn.classList.toggle("opacity-50", disabled);
        captureBtn.classList.toggle("pointer-events-none", disabled);
        switchBtn.classList.toggle("opacity-50", disabled);
        switchBtn.classList.toggle("pointer-events-none", disabled);
    }

    async function startStream(facingMode) {
        setLoading(true);
        setControlsDisabled(true);

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
        }

        currentTrack = null;
        resetCameraControls();

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: facingMode } },
                audio: false,
            });

            video.srcObject = stream;
            currentFacingMode = facingMode;
            await video.play();

            currentTrack = stream.getVideoTracks()[0];
            setupCameraControls(currentTrack);

            await waitForVideoFrame();
        } catch (err) {
            Toast.error(
                window.langFilepond?.camera_error ?? "Failed to access camera",
            );
            cleanup();
            return;
        }

        setControlsDisabled(false);
        setLoading(false);
    }

    aspectBtns.forEach((btn) => {
        btn.addEventListener("click", () => setAspectRatio(btn.dataset.aspect));
    });

    setAspectRatio(currentAspect);
    await startStream(currentFacingMode);
    await checkMultipleCameras();

    cancelBtns.forEach((btn) => {
        btn.addEventListener("click", cleanup);
    });

    switchBtn.addEventListener("click", async () => {
        const nextFacingMode =
            currentFacingMode === "environment" ? "user" : "environment";
        await startStream(nextFacingMode);
    });

    toggleSettingsBtn.addEventListener("click", () => {
        settingsVisible = !settingsVisible;
        applySettingsVisibility();
    });

    zoomInput.addEventListener("input", () => setZoom(zoomInput.value));
    focusInput.addEventListener("input", () => setFocus(focusInput.value));

    resetFocusBtn.addEventListener("click", () => setContinuousFocus());

    captureBtn.addEventListener("click", async () => {
        if (!video.videoWidth || !video.videoHeight) return;

        await waitForVideoFrame();

        const targetRatio = aspectRatios[currentAspect];
        const { cropX, cropY, cropWidth, cropHeight } = getCropDimensions(
            video.videoWidth,
            video.videoHeight,
            targetRatio,
        );

        canvas.width = cropWidth;
        canvas.height = cropHeight;

        const ctx = canvas.getContext("2d");
        if (!ctx) return;

        ctx.save();

        if (currentFacingMode === "user") {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(
                video,
                cropX,
                cropY,
                cropWidth,
                cropHeight,
                0,
                0,
                canvas.width,
                canvas.height,
            );
        } else {
            ctx.drawImage(
                video,
                cropX,
                cropY,
                cropWidth,
                cropHeight,
                0,
                0,
                canvas.width,
                canvas.height,
            );
        }

        ctx.restore();

        canvas.toBlob(
            (blob) => {
                if (!blob) return;

                const file = new File([blob], `camera-${Date.now()}.jpg`, {
                    type: "image/jpeg",
                });
                onCapture(file);
                cleanup();
            },
            "image/jpeg",
            0.92,
        );
    });
}

export default openCameraCapture;
