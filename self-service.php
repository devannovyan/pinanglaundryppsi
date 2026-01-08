<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pinang Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <span class="navbar-brand fw-bold"><i class="bi bi-water"></i> Pinang Laundry</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="layanan.php">Layanan</a></li>
                </ul>
            </div>
        </div>
</nav>

<hr class="line">

<h2 class="judul-halaman">Ketersediaan Mesin</h2>

<p class="subjudul">Silakan pilih mesin yang tersedia.</p>

<div class="grid" id="machineGrid" style="margin-top: 20px;"></div>


<div id="popupBooking" class="popup hidden">
  <div class="popup-box">
    <h3>Booking Mesin</h3>
    <input id="name" placeholder="Nama">
    <input id="phone" placeholder="No HP">

    <button onclick="toPayment()" class="btn">Lanjut Pembayaran</button>
    <button onclick="closeAllPopup()" class="btn ghost">Batal</button>
  </div>
</div>

<div id="popupQR" class="popup hidden">
  <div class="popup-box">
    <h3>QR Pembayaran</h3>
    <h3>Rp 16.000</h3>

    <img src="images/qris.jpg" id="qrImage">

    <p>Silakan lakukan pembayaran terlebih dahulu</p>

    <button onclick="submitBooking()" class="btn">
      Buat Booking
    </button>
  </div>
</div>

<div id="popupKode" class="popup hidden">
  <div class="popup-box">
    <h3>Booking Berhasil</h3>

    <p>Nomor Pelanggan Anda:</p>
    <h2 id="customerCode"></h2>

    <p>
      Silakan datang ke outlet dalam <b>30 menit</b><br>
      dan tunjukkan kode ini ke kasir
    </p>

    <button onclick="closeAllPopup()" class="btn ghost">
      Tutup
    </button>
  </div>
</div>


<script>
let selectedMachine = null;

function loadMachines(){
    fetch("api_status.php")
    .then(r=>r.json())
    .then(res=>{
        const grid = document.getElementById("machineGrid");
        grid.innerHTML = "";

        res.forEach(m => {
            let box = document.createElement("div");
            box.className = "machine " + m.status;

            let countdown = m.remaining ? `<div class='timer'>${m.remaining}</div>` : "";

            box.innerHTML = `
                <img src="images/mesin.png" class="machine-img">
                <div class='mname'>${m.name}</div>
                <div class='mstatus'>${m.status}</div>
                ${countdown}
            `;

            if(m.status === 'Tersedia'){
                box.onclick = () => openBooking(m);
            }

            grid.appendChild(box);
        });
    });
}


function openBooking(m){
    selectedMachine = m;
    document.getElementById("popupBooking").classList.remove("hidden");
}
function closeAllPopup(){
    document.querySelectorAll('.popup').forEach(p => {
        p.classList.add('hidden');
    });
}

function toPayment(){
    if(!document.getElementById("name").value || !document.getElementById("phone").value){
        alert("Nama dan No HP wajib diisi");
        return;
    }

    document.getElementById("popupBooking").classList.add("hidden");
    document.getElementById("popupQR").classList.remove("hidden");
}
function closeBookingPopup(){
    document.getElementById("popupBooking").classList.add("hidden");
}


function submitBooking(){
    let form = new FormData();
    form.append("machine_id", selectedMachine.id);
    form.append("name", document.getElementById("name").value);
    form.append("phone", document.getElementById("phone").value);
    form.append("total_biaya", 16000);

    fetch("api_booking.php", {
        method: 'POST',
        body: form
    })
    .then(r => r.json())
    .then(res => {
        if(res.success){
            document.getElementById("popupQR").classList.add("hidden");
            document.getElementById("popupKode").classList.remove("hidden");

            document.getElementById("customerCode").innerText = res.kode_booking;

            loadMachines();
        } else {
            alert("Gagal booking");
        }
    });
}


function closeQR(){
    document.getElementById("popupQR").classList.add("hidden");
}

setInterval(loadMachines, 2000);
loadMachines();
</script>
</body>
</html>
