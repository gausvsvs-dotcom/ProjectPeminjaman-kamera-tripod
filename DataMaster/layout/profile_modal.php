<!-- MODAL PROFIL SAYA -->
<div
    class="modal fade"
    id="profileModal"
    tabindex="-1"
    aria-labelledby="profileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content profile-modal-content">
            <!-- HEADER -->
            <div class="modal-header profile-modal-header">
                <div>
                    <h5 class="modal-title" id="profileModalLabel">
                        <i class="bi bi-person-circle me-2"></i>
                        Profil Saya
                    </h5>
                    <p class="mb-0">Kelola informasi akun administrator kamu.</p>
                </div>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup">
                </button>
            </div>
            <!-- BODY -->
            <div class="modal-body p-4">
                <form 
                    id="profileForm"
                    action="../pengguna/proses_profile.php"
                    method="POST"
                    enctype="multipart/form-data">
                    <div class="row g-4">
                        <!-- FOTO PROFIL -->
                        <div class="col-md-4 text-center">
                            <div class="profile-modal-avatar-wrapper">
                                <img
                                    id="profilePreview"
                                    src="../assets/img/<?= htmlspecialchars($_SESSION['foto'] ?? 'mutia.jpg'); ?>"
                                    alt="Foto profil"
                                    class="profile-modal-avatar">
                            </div>
                            <h6 class="mt-3 mb-1">
                                <?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator'); ?>
                            </h6>
                            <p class="text-muted small mb-3">
                                Foto profil akun
                            </p>
                            <label
                                for="profilePhotoInput"
                                class="btn profile-upload-btn btn-sm">
                                <i class="bi bi-camera me-1"></i>
                                Ganti Foto
                            </label>
                            <input
                                type="file"
                                id="profilePhotoInput"
                                name="foto"
                                accept="image/jpeg,image/png,image/webp"
                                class="d-none">
                            <button
                                type="button"
                                id="removeProfilePhoto"
                                class="btn btn-outline-secondary btn-sm mt-2">
                                <i class="bi bi-trash me-1"></i>
                                Hapus Pilihan Foto
                            </button>
                            <small class="d-block text-muted mt-2">
                                JPG, PNG, atau WEBP.
                            </small>
                        </div>
                        <!-- INFORMASI AKUN -->
                        <div class="col-md-8">
                            <h6 class="profile-section-title">
                                <i class="bi bi-person-vcard me-2"></i>
                                Informasi Akun
                            </h6>
                            <div class="mb-3">
                                <label for="profileName" class="form-label">
                                    Nama Lengkap
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="profileName"
                                    name="nama"
                                    value="<?= htmlspecialchars($_SESSION['nama'] ?? ''); ?>"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="profileUsername" class="form-label">
                                    Username
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="profileUsername"
                                    name="username"
                                    value="<?= htmlspecialchars($_SESSION['username'] ?? ''); ?>"
                                    required>
                            </div>

                            <div class="profile-security-box">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="profile-security-icon">
                                        <i class="bi bi-shield-lock-fill"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Keamanan Akun</h6>
                                        <p class="text-muted small mb-0">
                                            Ubah password untuk menjaga keamanan akun.
                                        </p>
                                    </div>
                                </div>

                                <a
                                    href="/ProjectPeminjaman-kamera-tripod/DataMaster/pengguna/ganti_password.php"
                                    class="btn profile-password-btn btn-sm mt-3">
                                    <i class="bi bi-key-fill me-1"></i>
                                    Ganti Password
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER MODAL -->
                    <div class="profile-modal-footer mt-4 pt-3">
                        <button
                            type="button"
                            class="btn btn-light border"
                            data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="btn profile-save-btn">
                            <i class="bi bi-check2-circle me-1"></i>
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>

document.addEventListener("DOMContentLoaded", function () {
    const profileForm = document.getElementById("profileForm");
    const photoInput = document.getElementById("profilePhotoInput");
    const preview = document.getElementById("profilePreview");
    const removeButton = document.getElementById("removeProfilePhoto");
    const nameInput = document.getElementById("profileName");
    const usernameInput = document.getElementById("profileUsername");
    const saveButton = profileForm.querySelector('[type="submit"]');

    if (!profileForm || !photoInput || !preview || !removeButton) return;

    const originalName = nameInput.value;
    const originalUsername = usernameInput.value;
    const originalPhoto = preview.src;

    let fotoSementara = null;

    // Preview foto
    photoInput.addEventListener("change", function () {
        const file = this.files[0];

        if (!file) return;

        if (!["image/jpeg", "image/png", "image/webp"].includes(file.type)) {
            alert("Format foto harus JPG, PNG, atau WEBP.");
            this.value = "";
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert("Ukuran foto maksimal 2 MB.");
            this.value = "";
            return;
        }

        if (fotoSementara) {
            URL.revokeObjectURL(fotoSementara);
        }

        fotoSementara = URL.createObjectURL(file);
        preview.src = fotoSementara;
    });

    // Batalkan pilihan foto
    removeButton.addEventListener("click", function () {
        photoInput.value = "";

        if (fotoSementara) {
            URL.revokeObjectURL(fotoSementara);
            fotoSementara = null;
        }

        preview.src = originalPhoto;
    });

    // Simpan profil tanpa pindah halaman
    profileForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        const nama = nameInput.value.trim();
        const username = usernameInput.value.trim();
        const fotoDipilih = photoInput.files.length > 0;

        if (nama === "" || username === "") {
            alert("Nama dan username wajib diisi!");
            return;
        }

        if (
            nama === originalName &&
            username === originalUsername &&
            !fotoDipilih
        ) {
            alert("Tidak ada perubahan profil.");
            return;
        }

        const formData = new FormData(profileForm);

        saveButton.disabled = true;
        saveButton.innerHTML = "Menyimpan...";

        try {
            const response = await fetch(profileForm.action, {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            if (data.status === "berhasil") {
                alert(data.pesan);

                // Update nama dan username
                document.querySelectorAll(
                    ".top-profile-name, .profile-dropdown-name, .profile-name"
                ).forEach(el => el.textContent = data.nama);

                document.querySelectorAll(
                    ".profile-dropdown-username, .profile-username"
                ).forEach(el => el.textContent = "@" + data.username);

                // Update foto navbar, sidebar, dan modal
                const fotoBaru =
                    "/ProjectPeminjaman-kamera-tripod/DataMaster/assets/img/" +
                    encodeURIComponent(data.foto);

                document.querySelectorAll(
                    ".top-profile-photo, .profile-dropdown-photo, .profile-photo, #profilePreview"
                ).forEach(img => {
                    img.src = fotoBaru;
                });

                // Simpan nilai terbaru sebagai nilai awal
                nameInput.value = data.nama;
                usernameInput.value = data.username;

                photoInput.value = "";

                // Update nama di samping foto dalam modal
                const namaModal = document.querySelector(
                    "#profileModal .col-md-4 h6"
                );

                if (namaModal) {
                    namaModal.textContent = data.nama;
                }

            } else {
                alert(data.pesan);
            }

        } catch (error) {
            alert("Terjadi kesalahan saat menyimpan profil.");
            console.error(error);
        } finally {
            saveButton.disabled = false;
            saveButton.innerHTML =
                '<i class="bi bi-check2-circle me-1"></i> Simpan Perubahan';
        }
    });
});

</script>
