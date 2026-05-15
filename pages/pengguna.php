<?php
// pages/pengguna.php
require_once '../includes/header.php';

if ($_SESSION['role'] !== 'admin') {
    echo "<div style='padding:60px;text-align:center;color:#ef4444;font-family:Space Grotesk,sans-serif;font-size:16px;font-weight:700;'><i class='ti ti-lock' style='font-size:40px;display:block;margin-bottom:12px;'></i>Akses Ditolak. Halaman ini khusus Admin.</div>";
    require_once '../includes/footer.php';
    exit();
}
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}
.page-title { font-family: 'Space Grotesk', sans-serif; font-size: 20px; font-weight: 700; color: #fff; }

.btn-add {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: linear-gradient(135deg, #1d6ae0, #7c3aed);
    border: none;
    border-radius: 12px;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity 0.15s;
    box-shadow: 0 4px 16px rgba(29,106,224,0.3);
}
.btn-add:hover { opacity: 0.9; }

.data-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; }

.data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.data-table thead tr { background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border); }
.data-table thead th {
    padding: 12px 18px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--text-muted);
}
.data-table tbody tr { border-top: 1px solid rgba(255,255,255,0.03); transition: background 0.12s; }
.data-table tbody tr:hover { background: rgba(255,255,255,0.03); }
.data-table tbody td { padding: 14px 18px; color: var(--text-sec); vertical-align: middle; }

.user-avatar {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-orange), #f59e0b);
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-right: 8px;
    box-shadow: 0 2px 8px rgba(249,115,22,0.3);
}

.role-badge {
    display: inline-flex;
    align-items: center;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.role-admin { background: rgba(96,165,250,0.1); color: #60a5fa; border: 1px solid rgba(96,165,250,0.2); }
.role-pimpinan { background: rgba(167,139,250,0.1); color: #a78bfa; border: 1px solid rgba(167,139,250,0.2); }
.role-kasir { background: rgba(249,115,22,0.1); color: #f97316; border: 1px solid rgba(249,115,22,0.2); }

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 11px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
    border: 1px solid transparent;
}
.btn-reset { background: rgba(249,115,22,0.08); color: #f97316; border-color: rgba(249,115,22,0.2); }
.btn-reset:hover { background: rgba(249,115,22,0.15); border-color: rgba(249,115,22,0.4); }

.sk-cell {
    height: 13px;
    background: linear-gradient(90deg, #1e2a45 25%, #2d3a55 50%, #1e2a45 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
    border-radius: 6px;
    display: inline-block;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Modals */
.modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 999; display: none; align-items: center; justify-content: center; }
.modal-backdrop.open { display: flex; }
.modal-inner { background: var(--bg-card); border: 1px solid var(--border); border-radius: 22px; width: 100%; max-width: 400px; overflow: hidden; animation: modalIn 0.22s ease; }
@keyframes modalIn { from{opacity:0;transform:scale(0.96) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }
.modal-hd { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--border); background: rgba(255,255,255,0.02); }
.modal-hd h3 { font-family: 'Space Grotesk', sans-serif; font-size: 15px; font-weight: 700; color: #fff; margin: 0; display: flex; align-items: center; gap: 8px; }
.modal-hd h3 i { color: var(--accent-blue); }
.modal-close { background: transparent; border: 1px solid var(--border); color: var(--text-muted); width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 15px; transition: all 0.15s; }
.modal-close:hover { border-color: #ef4444; color: #ef4444; }
.modal-body { padding: 20px; }
.form-group { margin-bottom: 14px; }
.form-label { display: block; font-size: 10px; font-weight: 800; letter-spacing: 1.2px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 7px; }
.form-control { width: 100%; background: var(--bg-input); border: 1px solid var(--border); color: var(--text-pri); font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; padding: 10px 14px; border-radius: 10px; outline: none; transition: border-color 0.15s, box-shadow 0.15s; }
.form-control:focus { border-color: rgba(29,106,224,0.5); box-shadow: 0 0 0 3px rgba(29,106,224,0.08); }
.form-control::placeholder { color: var(--text-muted); }
.btn-primary-full { width: 100%; padding: 11px; background: linear-gradient(135deg, #1d6ae0, #7c3aed); border: none; border-radius: 12px; color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 700; cursor: pointer; transition: opacity 0.15s; display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 6px; }
.btn-primary-full:hover { opacity: 0.9; }
.btn-primary-full:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-orange-full { width: 100%; padding: 11px; background: linear-gradient(135deg, #f97316, #f59e0b); border: none; border-radius: 12px; color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 700; cursor: pointer; transition: opacity 0.15s; display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 6px; }
.btn-orange-full:hover { opacity: 0.9; }
.btn-orange-full:disabled { opacity: 0.5; cursor: not-allowed; }
</style>

<div class="page-header">
    <div>
        <div class="page-title">Manajemen User</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:3px;">Kelola akun pengguna sistem</div>
    </div>
    <button class="btn-add" onclick="openUser()">
        <i class="ti ti-user-plus" style="font-size:14px;"></i> Tambah Akun
    </button>
</div>

<div class="data-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th style="text-align:center;">Role</th>
                <th>Login Terakhir</th>
                <th style="text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody id="u-tbody">
            <?php for($i=0;$i<4;$i++): ?>
            <tr>
                <td><span class="sk-cell" style="width:130px;"></span></td>
                <td><span class="sk-cell" style="width:90px;"></span></td>
                <td style="text-align:center;"><span class="sk-cell" style="width:60px;"></span></td>
                <td><span class="sk-cell" style="width:110px;"></span></td>
                <td style="text-align:center;"><span class="sk-cell" style="width:100px;margin:auto;display:block;"></span></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div class="modal-backdrop" id="user-modal">
    <div class="modal-inner">
        <div class="modal-hd">
            <h3><i class="ti ti-user-plus"></i> Buat Akun Baru</h3>
            <button class="modal-close" onclick="closeUser()"><i class="ti ti-x"></i></button>
        </div>
        <form onsubmit="saveUser(event)" class="modal-body">
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" id="u-name" class="form-control" placeholder="Nama lengkap pengguna" required>
            </div>
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" id="u-user" class="form-control" placeholder="username" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" id="u-pass" class="form-control" placeholder="Min. 6 karakter" required minlength="6">
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <select id="u-role" class="form-control">
                    <option value="kasir">Kasir</option>
                    <option value="pimpinan">Pimpinan</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-primary-full" id="btn-save-user">
                <i class="ti ti-check"></i> Simpan Akun
            </button>
        </form>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal-backdrop" id="reset-pass-modal">
    <div class="modal-inner">
        <div class="modal-hd">
            <h3><i class="ti ti-lock-open" style="color:#f97316;"></i> Ubah Password</h3>
            <button class="modal-close" onclick="closeResetPassModal()"><i class="ti ti-x"></i></button>
        </div>
        <form onsubmit="submitResetPass(event)" class="modal-body">
            <input type="hidden" id="reset-user-id">
            <div style="background:rgba(249,115,22,0.08);border:1px solid rgba(249,115,22,0.2);border-radius:10px;padding:10px 14px;margin-bottom:16px;">
                <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#f97316;margin-bottom:4px;">Setel ulang password untuk:</div>
                <div id="reset-user-name" style="font-size:14px;font-weight:700;color:#fff;"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" id="reset-new-pass" class="form-control" placeholder="Min. 6 karakter" minlength="6" required>
            </div>
            <button type="submit" id="btn-submit-reset" class="btn-orange-full">
                <i class="ti ti-lock"></i> Ganti Password
            </button>
        </form>
    </div>
</div>

<script>
async function loadUsers() {
    const tbody = document.getElementById('u-tbody');
    try {
        const result = await apiFetch('../api/auth/get_users.php');
        if (result.status === 'success') {
            if (!result.data.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="padding:50px;text-align:center;color:var(--text-muted);">Belum ada data pengguna.</td></tr>';
                return;
            }
            tbody.innerHTML = result.data.map(u => {
                const roleClass = u.role === 'admin' ? 'role-admin' : (u.role === 'pimpinan' ? 'role-pimpinan' : 'role-kasir');
                return `<tr>
                    <td>
                        <div style="display:flex;align-items:center;">
                            <div class="user-avatar">${u.name.charAt(0).toUpperCase()}</div>
                            <span style="font-weight:700;color:var(--text-pri);">${u.name}</span>
                        </div>
                    </td>
                    <td style="font-family:'Space Grotesk',sans-serif;color:var(--text-muted);">@${u.username}</td>
                    <td style="text-align:center;"><span class="role-badge ${roleClass}">${u.role}</span></td>
                    <td style="font-size:12px;color:var(--text-muted);">${u.last_login ? new Date(u.last_login).toLocaleString('id-ID') : '—'}</td>
                    <td style="text-align:center;">
                        <button class="action-btn btn-reset" onclick="openResetPassModal(${u.id}, '${u.name.replace(/'/g, "\\'")}')">
                            <i class="ti ti-lock-open" style="font-size:12px;"></i> Ganti Password
                        </button>
                    </td>
                </tr>`;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="5" style="padding:40px;text-align:center;color:#ef4444;font-weight:600;">${result.message}</td></tr>`;
        }
    } catch(e) {
        tbody.innerHTML = `<tr><td colspan="5" style="padding:40px;text-align:center;color:#ef4444;">Gagal memuat data. Periksa file get_users.php</td></tr>`;
    }
}

function openUser() {
    document.querySelector('#user-modal form').reset();
    document.getElementById('user-modal').classList.add('open');
}
function closeUser() { document.getElementById('user-modal').classList.remove('open'); }

document.getElementById('user-modal').addEventListener('click', function(e) { if (e.target === this) closeUser(); });

async function saveUser(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save-user');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin 1s linear infinite;"></i> Menyimpan...';

    const payload = {
        name: document.getElementById('u-name').value,
        username: document.getElementById('u-user').value,
        password: document.getElementById('u-pass').value,
        role: document.getElementById('u-role').value
    };

    try {
        const result = await apiFetch('../api/auth/register.php', { method: 'POST', body: JSON.stringify(payload) });
        if (result.status === 'success') {
            showToast(result.message || 'Akun berhasil dibuat!', 'success');
            closeUser();
            loadUsers();
        } else {
            showToast(result.message || 'Gagal membuat akun.', 'error');
        }
    } catch(e) {
        showToast('Terjadi kesalahan saat menyimpan akun.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-check"></i> Simpan Akun';
    }
}

function openResetPassModal(id, name) {
    document.getElementById('reset-user-id').value = id;
    document.getElementById('reset-user-name').textContent = name;
    document.getElementById('reset-new-pass').value = '';
    document.getElementById('reset-pass-modal').classList.add('open');
}
function closeResetPassModal() { document.getElementById('reset-pass-modal').classList.remove('open'); }

document.getElementById('reset-pass-modal').addEventListener('click', function(e) { if (e.target === this) closeResetPassModal(); });

async function submitResetPass(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-submit-reset');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin 1s linear infinite;"></i> Memproses...';

    const payload = {
        user_id: document.getElementById('reset-user-id').value,
        new_password: document.getElementById('reset-new-pass').value
    };

    try {
        const result = await apiFetch('../api/auth/admin_reset_password.php', { method: 'POST', body: JSON.stringify(payload) });
        if (result.status === 'success') {
            showToast(result.message || 'Password berhasil diubah!', 'success');
            closeResetPassModal();
        } else {
            showToast(result.message || 'Gagal mengganti password.', 'error');
        }
    } catch(e) {
        showToast('Terjadi kesalahan. Pastikan file admin_reset_password.php ada.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-lock"></i> Ganti Password';
    }
}

loadUsers();
</script>

<?php require_once '../includes/footer.php'; ?>