/* =========================================================
   TIXORA ADMIN
   ADMIN.JS
========================================================= */

/* =========================================================
   SECTION DATA
========================================================= */

const sectionTitles = {
    dashboard: "Dashboard",

    events: "Event Management",

    categories: "Categories",

    tickets: "Ticket Management",

    seats: "Seat Management",

    orders: "Orders",

    payments: "Payments",

    "ticket-validation": "QR Validation",

    users: "User Management",

    articles: "Articles",

    reports: "Reports",
};

/* =========================================================
   SHOW SECTION
========================================================= */

function showSection(sectionId) {
    const sections = document.querySelectorAll(".admin-section");

    sections.forEach((section) => {
        section.classList.remove("active");
    });

    const target = document.getElementById(sectionId);

    if (target) {
        target.classList.add("active");
    }

    const menuItems = document.querySelectorAll(".menu-item");

    menuItems.forEach((item) => {
        item.classList.remove("active");

        if (item.dataset.section === sectionId) {
            item.classList.add("active");
        }
    });

    const pageTitle = document.getElementById("pageTitle");

    if (pageTitle && sectionTitles[sectionId]) {
        pageTitle.textContent = sectionTitles[sectionId];
    }

    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });

    closeSidebar();
}

window.showSection = showSection;

/* =========================================================
   SIDEBAR
========================================================= */

function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");

    sidebar.classList.toggle("open");
}

function closeSidebar() {
    const sidebar = document.getElementById("sidebar");

    if (window.innerWidth <= 800) {
        sidebar.classList.remove("open");
    }
}

/* =========================================================
   EVENT MODAL
========================================================= */

function openEventForm() {
    const modal = document.getElementById("eventFormModal");

    modal.classList.add("active");

    document.body.style.overflow = "hidden";
}

/* =========================================================
   CLOSE MODAL
========================================================= */

function closeAdminModal(id) {
    const modal = document.getElementById(id);

    if (!modal) return;

    modal.classList.remove("active");

    document.body.style.overflow = "";
}

/* =========================================================
   SAVE EVENT
========================================================= */

function saveEvent(event) {
    event.preventDefault();

    const form = event.target;

    const namaEvent = form.querySelector('input[type="text"]').value;
    const kategori = form.querySelector("select").value;

    const inputs = form.querySelectorAll('input[type="text"]');

    const lokasi = inputs[1].value;

    const tanggal = form.querySelector('input[type="date"]').value;

    const statusSelects = form.querySelectorAll("select");
    const status = statusSelects[1].value;

    const tableBody = document.querySelector("#eventTable tbody");

    if (!tableBody) {
        return;
    }

    const row = document.createElement("tr");

    row.innerHTML = `
        <td>
            <strong>${namaEvent}</strong>
        </td>

        <td>
            ${kategori}
        </td>

        <td>
            ${lokasi}
        </td>

        <td>
            ${tanggal}
        </td>

        <td>
            -
        </td>

        <td>
            <span class="status-pill orange">
                ${status.toUpperCase()}
            </span>
        </td>

        <td>
            <div class="action-buttons">

                    <button type="button">
                        <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" />
                        </svg>
                    </button>

                    <button type="button">
                        <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                        </svg>
                    </button>

            </div>
        </td>
    `;

    tableBody.appendChild(row);

    closeAdminModal("eventFormModal");

    form.reset();
}

/* =========================================================
   EDIT
========================================================= */

function editItem(itemName) {
    showToast(
        `Edit "${itemName}" akan terhubung ke database pada tahap backend.`,
    );
}

/* =========================================================
   DELETE
========================================================= */

function deleteItem(itemName) {
    const confirmation = confirm(`Yakin ingin menghapus "${itemName}"?`);

    if (!confirmation) {
        return;
    }

    showToast(`"${itemName}" berhasil dihapus. (Prototype)`);
}

/* =========================================================
   FILTER EVENT
========================================================= */

function filterAdminEvents(keyword) {
    const rows = document.querySelectorAll("#eventTable tbody tr");

    const value = keyword.toLowerCase().trim();

    rows.forEach((row) => {
        const text = row.innerText.toLowerCase();

        row.style.display = text.includes(value) ? "" : "none";
    });
}

/* =========================================================
   ADD CATEGORY
========================================================= */

function addCategory() {
    showToast("Form kategori akan tersedia pada tahap database.");
}
// +
function openCategoryForm(categoryName = "") {
    const modal = document.getElementById("categoryFormModal");
    const title = document.getElementById("categoryModalTitle");
    const input = document.getElementById("categoryName");
    const status = document.getElementById("categoryStatus");
    const description = document.getElementById("categoryDescription");
    const hiddenId = document.getElementById("categoryEditingId");

    if (!modal) return;

    if (categoryName) {
        title.textContent = "Edit Kategori";
        input.value = categoryName;
        status.value = "active";
        description.value =
            "Kategori " + categoryName + " untuk event tertentu.";
        // store editing key (use name as temporary id)
        if (hiddenId) hiddenId.value = categoryName;
    } else {
        title.textContent = "Tambah Kategori";
        input.value = "";
        status.value = "active";
        description.value = "";
        if (hiddenId) hiddenId.value = "";
    }

    modal.classList.add("active");
    document.body.style.overflow = "hidden";
}

function saveCategory(event) {
    event.preventDefault();

    const name = document.getElementById("categoryName").value.trim();
    const status = document.getElementById("categoryStatus").value;
    const description = document
        .getElementById("categoryDescription")
        .value.trim();

    if (!name) {
        if (typeof showToast === "function") {
            showToast("Nama kategori wajib diisi.");
        } else {
            alert("Nama kategori wajib diisi.");
        }
        return;
    }

    // Check if editing existing category
    const editingIdEl = document.getElementById("categoryEditingId");
    const editingId = editingIdEl ? editingIdEl.value : "";

    // Prevent duplicate category names when creating new or renaming
    const existing = Array.from(
        document.querySelectorAll(".admin-category-card h3"),
    ).find((h) => h.textContent.trim().toLowerCase() === name.toLowerCase());

    if (
        existing &&
        (!editingId || editingId.toLowerCase() !== name.toLowerCase())
    ) {
        if (typeof showToast === "function") {
            showToast("Kategori dengan nama tersebut sudah ada.");
        } else {
            alert("Kategori dengan nama tersebut sudah ada.");
        }
        return;
    }

    // Find grid container
    const grid = document.getElementById("categoryGrid");

    if (!grid) {
        // If grid doesn't exist, fallback to toast and log
        if (typeof showToast === "function") {
            showToast("Kategori disimpan, tetapi grid tidak ditemukan.");
        }

        console.log({ name, status, description });
        closeAdminModal("categoryFormModal");
        return;
    }

    // Helper: choose icon class based on category keywords
    function pickIcon(name) {
        const key = name.toLowerCase();
        if (key.includes("music")) return "fa-solid fa-music";
        if (key.includes("sport")) return "fa-solid fa-futbol";
        if (key.includes("esport")) return "fa-solid fa-gamepad";
        if (key.includes("festival")) return "fa-solid fa-star";
        if (key.includes("theater") || key.includes("teater"))
            return "fa-solid fa-masks-theater";
        return "fa-solid fa-people-group";
    }

    const card = document.createElement("div");
    card.className = "admin-category-card";
    card.dataset.status = status;

    const iconClass = pickIcon(name);

    card.innerHTML = `
        <div class="category-admin-icon ${status === "active" ? "purple" : "orange"}">
            <i class="${iconClass}"></i>
        </div>

        <h3>${name}</h3>
        <span>0 event aktif</span>

        <div></div>
    `;

    // add action buttons programmatically (so we avoid inline onclick escaping)
    const actions = card.querySelector("div:last-child");

    const editBtn = document.createElement("button");
    editBtn.type = "button";
    editBtn.title = "Edit kategori";
    editBtn.innerHTML = '<i class="fa-solid fa-pen"></i>';
    editBtn.addEventListener("click", () => openCategoryForm(name));

    const delBtn = document.createElement("button");
    delBtn.type = "button";
    delBtn.title = "Hapus kategori";
    delBtn.innerHTML = '<i class="fa-solid fa-trash"></i>';
    delBtn.addEventListener("click", () => deleteCategory(name));

    actions.appendChild(editBtn);
    actions.appendChild(delBtn);

    if (editingId) {
        // find existing card and replace/update it
        const old = Array.from(
            document.querySelectorAll(".admin-category-card"),
        ).find((c) => {
            const h = c.querySelector("h3");
            return (
                h &&
                h.textContent.trim().toLowerCase() === editingId.toLowerCase()
            );
        });

        if (old && old.parentNode) {
            old.parentNode.replaceChild(card, old);
        } else {
            grid.prepend(card);
        }
        // clear editing id
        if (editingIdEl) editingIdEl.value = "";
    } else {
        // add new card at the top
        grid.prepend(card);
    }

    if (typeof showToast === "function")
        showToast("Kategori berhasil disimpan.");

    closeAdminModal("categoryFormModal");

    // reset form if available
    try {
        const form = event.target;
        if (form && typeof form.reset === "function") form.reset();
        // also clear editing id if any
        const editing = document.getElementById("categoryEditingId");
        if (editing) editing.value = "";
    } catch (e) {
        /* ignore */
    }

    console.log({ name, status, description });
}

function deleteCategory(name) {
    const confirmDelete = window.confirm("Hapus kategori " + name + "?");

    if (!confirmDelete) return;

    // find and remove the category card in DOM
    const cards = Array.from(document.querySelectorAll(".admin-category-card"));
    const target = cards.find((c) => {
        const h = c.querySelector("h3");
        return h && h.textContent.trim().toLowerCase() === name.toLowerCase();
    });

    if (target && target.parentNode) {
        target.parentNode.removeChild(target);
    }

    if (typeof showToast === "function") {
        showToast("Kategori " + name + " berhasil dihapus.");
    } else {
        alert("Kategori " + name + " berhasil dihapus.");
    }
}

function filterCategoryAdmin(keyword) {
    const value = keyword.toLowerCase().trim();
    const cards = document.querySelectorAll(".admin-category-card");

    cards.forEach((card) => {
        const text = card.innerText.toLowerCase();
        const matches = text.includes(value);

        card.style.display = matches ? "" : "none";
    });
}
// +
/* =========================================================
   ADD ARTICLE
========================================================= */

function addArticle() {
    showToast("Form artikel akan tersedia pada tahap database.");
}

/* =========================================================
   QR SIMULATION
========================================================= */

function simulateScan() {
    const result = document.getElementById("validationResult");

    result.innerHTML = `

        <div
            style="
                border:1px solid #dcefe0;
                background:#f2fbf4;
                border-radius:12px;
                padding:22px;
            "
        >

            <div
                style="
                    width:54px;
                    height:54px;
                    background:#dff5e4;
                    color:#34a04e;
                    border-radius:50%;
                    display:grid;
                    place-items:center;
                    margin:0 auto 12px;
                    font-size:22px;
                "
            >

                <i class="fa-solid fa-check"></i>

            </div>


            <div
                style="
                    text-align:center;
                "
            >

                <strong
                    style="
                        display:block;
                        color:#299746;
                        font-size:15px;
                    "
                >
                    VALID TICKET
                </strong>


                <span
                    style="
                        display:block;
                        color:#888;
                        font-size:8px;
                        margin-top:3px;
                    "
                >
                    QR berhasil diverifikasi
                </span>

            </div>


            <div
                style="
                    margin-top:18px;
                    padding-top:15px;
                    border-top:1px solid #dcefe0;
                    font-size:9px;
                    line-height:2;
                "
            >

                <strong>
                    Ticket ID:
                </strong>

                TIX-2026-883421

                <br>

                <strong>
                    Customer:
                </strong>

                Haikal

                <br>

                <strong>
                    Event:
                </strong>

                Jakarta Music Fest

                <br>

                <strong>
                    Ticket:
                </strong>

                VIP 1

                <br>

                <strong>
                    Seat:
                </strong>

                A01

                <br>

                <strong>
                    Status:
                </strong>

                PAID

            </div>


            <button
                onclick="checkInTicket()"
                style="
                    width:100%;
                    height:42px;
                    border:none;
                    border-radius:8px;
                    background:#299746;
                    color:white;
                    margin-top:15px;
                    font-size:9px;
                    font-weight:900;
                "
            >

                CHECK-IN

            </button>

        </div>

    `;
}

/* =========================================================
   CHECK IN
========================================================= */

function checkInTicket() {
    showToast("Ticket berhasil CHECK-IN.");

    const result = document.getElementById("validationResult");

    setTimeout(() => {
        result.innerHTML = `

                <div
                    style="
                        border:1px solid #dddde3;
                        background:#f8f8fa;
                        border-radius:12px;
                        padding:25px;
                        text-align:center;
                    "
                >

                    <div
                        style="
                            width:54px;
                            height:54px;
                            background:#eeeef1;
                            color:#68686f;
                            border-radius:50%;
                            display:grid;
                            place-items:center;
                            margin:auto;
                        "
                    >

                        <i class="fa-solid fa-check"></i>

                    </div>


                    <h3
                        style="
                            margin-top:11px;
                            font-size:15px;
                        "
                    >
                        CHECK-IN BERHASIL
                    </h3>


                    <p
                        style="
                            margin-top:5px;
                            font-size:8px;
                            color:#888;
                        "
                    >
                        Tiket sudah digunakan
                        untuk masuk venue.
                    </p>

                </div>

            `;
    }, 700);
}

/* =========================================================
   TOAST
========================================================= */

function showToast(message) {
    const toast = document.getElementById("adminToast");

    const text = document.getElementById("adminToastMessage");

    if (!toast || !text) {
        return;
    }

    text.textContent = message;

    toast.classList.add("show");

    clearTimeout(window.adminToastTimer);

    window.adminToastTimer = setTimeout(() => {
        toast.classList.remove("show");
    }, 2800);
}

/* =========================================================
   ADMIN LOGOUT
========================================================= */

function adminLogout() {
    const confirmation = confirm("Apakah kamu yakin ingin logout?");

    if (!confirmation) {
        return;
    }

    showToast("Logout berhasil. (Prototype)");

    setTimeout(() => {
        window.location.href = "index.html";
    }, 900);
}

/* =========================================================
   WINDOW RESIZE
========================================================= */

window.addEventListener("resize", function () {
    if (window.innerWidth > 800) {
        document.getElementById("sidebar")?.classList.remove("open");
    }
});

window.openEventForm = openEventForm;
window.closeAdminModal = closeAdminModal;
window.saveEvent = saveEvent;
window.addCategory = addCategory;
window.openCategoryForm = openCategoryForm;
window.saveCategory = saveCategory;
window.deleteCategory = deleteCategory;
window.filterCategoryAdmin = filterCategoryAdmin;

/* =========================================================
   INITIAL LOAD
========================================================= */

document.addEventListener("DOMContentLoaded", function () {
    showSection("dashboard");
    // wire category status filter (if present)
    const statusFilter = document.getElementById("categoryStatusFilter");

    if (statusFilter) {
        statusFilter.addEventListener("change", function () {
            const val = this.value;
            const cards = document.querySelectorAll(".admin-category-card");

            cards.forEach((card) => {
                if (val === "all") {
                    card.style.display = "";
                } else {
                    card.style.display =
                        card.dataset.status === val ? "" : "none";
                }
            });
        });
    }
});
