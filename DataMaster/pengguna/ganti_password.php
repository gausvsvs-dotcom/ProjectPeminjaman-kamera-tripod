
<!-- KEAMANAN AKUN -->
<div class="profile-security-card mt-4">
    <div class="d-flex align-items-center gap-3">
        <div class="profile-security-icon">
            <i class="bi bi-shield-lock"></i>
        </div>

        <div>
            <h6 class="mb-1">Keamanan Akun</h6>
            <p class="text-muted mb-2">
                Ubah password untuk menjaga keamanan akun.
            </p>

            <button
                type="button"
                class="btn-detail-back"
                id="btnBukaPassword"
                onclick="toggleFormPassword()"
            >
                <i class="bi bi-key me-1"></i>
                Ganti Password
                <i class="bi bi-chevron-down ms-1" id="iconPassword"></i>
            </button>
        </div>
    </div>

    <!-- FORM GANTI PASSWORD -->
    <div id="formPassword" style="display: none;" class="mt-3">
        <hr>

        <div id="pesanPassword"></div>

        <form id="formGantiPassword">

            <div class="mb-3">
                <label for="password_lama" class="data-form-label">
                    Password Lama
                </label>
                <input
                    type="password"
                    id="password_lama"
                    name="password_lama"
                    class="data-form-control"
                    placeholder="Masukkan password lama"
                    autocomplete="current-password"
                    required
            >
            </div>

            <div class="mb-3">
                <label for="password_baru" class="data-form-label">
                    Password Baru
                </label>
                <input
                    type="password"
                    id="password_baru"
                    name="password_baru"
                    class="data-form-control"
                    placeholder="Minimal 8 karakter"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >
                <small class="text-muted">
                    Gunakan password yang sulit ditebak.
                </small>
            </div>

            <div class="mb-3">
                <label for="konfirmasi_password" class="data-form-label">
                    Konfirmasi Password Baru
                </label>
                <input
                    type="password"
                    id="konfirmasi_password"
                    name="konfirmasi_password"
                    class="data-form-control"
                    placeholder="Ulangi password baru"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn-form-save" id="btnSimpanPassword">
                    <i class="bi bi-shield-check me-1"></i>
                    Simpan Password
                </button>

                <button
                    type="button"
                    class="btn-form-cancel"
                    onclick="toggleFormPassword()"
                >
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
<script>

document.addEventListener("DOMContentLoaded", function () {

    // Buka dan tutup form password di dalam card profil
    window.toggleFormPassword = function () {

        const form = document.getElementById("formPassword");
        const icon = document.getElementById("iconPassword");

        if (!form || !icon) return;

        if (form.style.display === "none") {
            form.style.display = "block";
            icon.className = "bi bi-chevron-up ms-1";
        } else {
            form.style.display = "none";
            icon.className = "bi bi-chevron-down ms-1";
        }
    };

    // Proses submit ganti password
    const formPassword = document.getElementById("formGantiPassword");

    if (!formPassword) return;

    formPassword.addEventListener("submit", async function (event) {

        event.preventDefault();

        const tombol = document.getElementById("btnSimpanPassword");
        const pesan = document.getElementById("pesanPassword");

        tombol.disabled = true;
        tombol.innerHTML = "Menyimpan...";
        pesan.innerHTML = "";

        const formData = new FormData(this);
        formData.append("ganti_password", "1");

        try {

            const response = await fetch(
                "../pengguna/proses_ganti_password.php",
                {
                    method: "POST",
                    body: formData
                }
            );

            const data = await response.json();

            if (data.status === "berhasil") {

                pesan.innerHTML = `
                    <div class="alert alert-success py-2">
                        <i class="bi bi-check-circle me-2"></i>
                        ${data.pesan}
                    </div>
                `;

                this.reset();

            } else {

                pesan.innerHTML = `
                    <div class="alert alert-danger py-2">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        ${data.pesan}
                    </div>
                `;

            }

        } catch (error) {

            pesan.innerHTML = `
                <div class="alert alert-danger py-2">
                    Terjadi kesalahan saat menghubungi server.
                </div>
            `;

            console.error(error);

        } finally {

            tombol.disabled = false;
            tombol.innerHTML =
                '<i class="bi bi-shield-check me-1"></i> Simpan Password';

        }

    });

});

</script>